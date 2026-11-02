<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Jobs\SendCampaignBatch;
use App\Models\CRM\Campaign;
use App\Models\CRM\CampaignRecipient;
use App\Models\CRM\Contact;
use App\Models\CRM\EmailTemplate;
use App\Models\CRM\EmailUnsubscribe;
use App\Support\Branding;
use App\Support\CampaignMailer;
use App\Support\CampaignSender;
use App\Support\CampaignStarterTemplates;
use App\Support\PublicMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index()
    {
        if (! $this->campaignsTableReady()) {
            return $this->campaignsSetupView();
        }

        try {
            $campaigns = Campaign::query()->latest()->paginate(20);
        } catch (\Throwable $e) {
            Log::warning('CRM campaigns query failed', ['error' => $e->getMessage()]);

            return $this->campaignsSetupView();
        }

        return view('admin.crm.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        if (! $this->campaignsTableReady()) {
            return $this->campaignsSetupView();
        }

        $stages = [
            '' => 'Todos los contactos con email',
            'lead' => 'Nuevos (lead)',
            'mql' => 'Contactados (mql)',
            'sql' => 'Calificados (sql)',
            'customer' => 'Clientes',
        ];

        $contactCount = Cache::remember('crm.campaigns.contact_count', 60, function () {
            return Contact::query()->whereNotNull('email')->where('email', '!=', '')->count();
        });

        return view('admin.crm.campaigns.create', [
            'stages' => $stages,
            'contactCount' => $contactCount,
            'defaultCtaUrl' => rtrim((string) config('app.url'), '/') . '/contact',
            'savedTemplates' => $this->listSavedTemplates(),
            'starterTemplates' => CampaignStarterTemplates::all(),
        ]);
    }

    public function loadStarterTemplate(string $id)
    {
        $tpl = CampaignStarterTemplates::find($id);
        if (! $tpl) {
            return response()->json(['success' => false, 'message' => 'Plantilla no encontrada.'], 404);
        }

        return response()->json([
            'success' => true,
            'id' => $tpl['id'],
            'name' => $tpl['name'],
            'subject' => $tpl['subject'],
            'cta_label' => $tpl['cta_label'],
            'html' => $tpl['html'],
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'file' => 'required|file|max:51200', // 50 MB
        ]);

        $type = $request->input('type');
        $file = $request->file('file');

        try {
            if ($type === 'image') {
                $request->validate([
                    'file' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192',
                ]);
                $path = PublicMedia::storeImage(
                    $file,
                    'campaigns/content',
                    PublicMedia::blogImageResizeOptions()
                );
            } else {
                $ext = strtolower((string) $file->getClientOriginalExtension());
                if (! in_array($ext, ['mp4', 'webm', 'ogg'], true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El video debe ser mp4, webm u ogg.',
                    ], 422);
                }
                $path = PublicMedia::storePublicFile($file, 'videos/campaigns', ['mp4', 'webm', 'ogg']);
            }
        } catch (\Throwable $e) {
            Log::error('CRM campaign media upload failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo guardar el archivo. Verifica permisos en public/images y public/videos.',
            ], 500);
        }

        $url = $this->absolutePublicUrl($path);

        return response()->json([
            'success' => true,
            'type' => $type,
            'path' => $path,
            'url' => $url,
            'message' => $type === 'image' ? 'Imagen subida.' : 'Video subido.',
        ]);
    }

    /**
     * Absolute URL for email clients (relative paths break in inboxes).
     */
    protected function absolutePublicUrl(string $path): string
    {
        $relative = Branding::assetUrl($path);
        if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
            return $relative;
        }

        $base = rtrim((string) request()->getSchemeAndHttpHost().request()->getBaseUrl(), '/');

        return $base.'/'.ltrim($relative, '/');
    }

    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template' => [
                'nullable',
                'file',
                'max:20480',
                function (string $attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }
                    $ext = strtolower((string) $value->getClientOriginalExtension());
                    if (! in_array($ext, ['html', 'htm', 'zip'], true)) {
                        $fail('El archivo debe ser .html, .htm o .zip');
                    }
                },
            ],
            'save' => 'nullable',
            'template_name' => 'nullable|string|max:120',
            'html_body' => 'nullable|string|max:500000',
            'assets' => 'nullable|array|max:40',
            'assets.*' => 'file|max:5120',
            'asset_paths' => 'nullable|array|max:40',
            'asset_paths.*' => 'nullable|string|max:255',
        ]);

        $raw = (string) $request->input('html_body', '');
        $assetMap = [];
        $templateFile = $request->file('template');
        $isZip = $templateFile && strtolower((string) $templateFile->getClientOriginalExtension()) === 'zip';

        try {
            if ($isZip) {
                [$raw, $assetMap] = $this->extractTemplateZip($templateFile);
            } else {
                if ($raw === '' && $templateFile) {
                    $raw = (string) file_get_contents($templateFile->getRealPath());
                }
                $assetMap = $this->storeUploadedTemplateAssets($request);
            }
        } catch (\Throwable $e) {
            Log::warning('CRM template assets failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        if (trim($raw) === '') {
            return response()->json([
                'success' => false,
                'message' => 'No se recibió HTML ni archivo. Sube un .html, un .zip, o la carpeta completa (email.html + images/).',
            ], 422);
        }

        $html = $this->rewriteTemplateAssetUrls($raw, $assetMap);
        $html = $this->sanitizeCampaignHtml($html);

        if (trim(strip_tags($html)) === '' && ! str_contains(strtolower($html), '<table')) {
            return response()->json([
                'success' => false,
                'message' => 'La plantilla está vacía o no contiene HTML válido.',
            ], 422);
        }

        $savedAs = null;
        $shouldSave = $request->boolean('save')
            || $request->input('save') === '1'
            || $request->input('save') === 1
            || $request->input('save') === true
            || $request->input('save') === 'true';

        if ($shouldSave) {
            try {
                $name = $request->input('template_name');
                if (! $name && $request->hasFile('template')) {
                    $name = pathinfo($request->file('template')->getClientOriginalName(), PATHINFO_FILENAME);
                }
                $savedAs = $this->persistTemplate($html, (string) ($name ?: 'plantilla'));
            } catch (\Throwable $e) {
                Log::error('CRM template save failed: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'html' => $html,
                    'saved_as' => null,
                    'templates' => $this->listSavedTemplates(),
                    'message' => 'No se pudo guardar en la base de datos. Ejecuta database/sql/crm_email_templates.sql en Hostinger. '.$e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'saved_as' => $savedAs,
            'images' => count(array_unique(array_values($assetMap))),
            'templates' => $this->listSavedTemplates(),
            'message' => $savedAs
                ? 'Plantilla guardada. Ya puedes seleccionarla en la lista.'
                : 'Plantilla cargada correctamente.',
        ]);
    }

    public function loadTemplate(string $slug)
    {
        $template = EmailTemplate::query()->where('slug', $slug)->first();
        if ($template) {
            $html = $this->sanitizeCampaignHtml((string) $template->html_body);
            $name = $template->name;
        } else {
            // Backward compatibility with old filesystem templates
            $path = $this->templatePath($slug);
            if (! is_file($path)) {
                return response()->json(['success' => false, 'message' => 'Plantilla no encontrada.'], 404);
            }
            $html = $this->sanitizeCampaignHtml((string) file_get_contents($path));
            $name = $slug;
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'slug' => $slug,
            'name' => $name,
        ]);
    }

    public function destroyTemplate(string $slug)
    {
        EmailTemplate::query()->where('slug', $slug)->delete();

        $path = $this->templatePath($slug);
        if (is_file($path)) {
            @unlink($path);
        }

        return response()->json([
            'success' => true,
            'templates' => $this->listSavedTemplates(),
            'message' => 'Plantilla eliminada.',
        ]);
    }

    /**
     * @return list<array{slug:string,name:string}>
     */
    protected function listSavedTemplates(): array
    {
        $out = [];

        try {
            $out = EmailTemplate::query()
                ->orderBy('name')
                ->get(['name', 'slug'])
                ->map(fn (EmailTemplate $t) => [
                    'slug' => $t->slug,
                    'name' => $t->name,
                ])
                ->all();
        } catch (\Throwable $e) {
            Log::warning('CRM templates list failed: '.$e->getMessage());
        }

        // Merge any legacy filesystem templates not yet in DB
        $dir = $this->templatesDirectory();
        if (is_dir($dir)) {
            $existing = collect($out)->pluck('slug')->all();
            foreach (glob($dir . DIRECTORY_SEPARATOR . '*.html') ?: [] as $file) {
                $slug = pathinfo($file, PATHINFO_FILENAME);
                if (in_array($slug, $existing, true)) {
                    continue;
                }
                $out[] = [
                    'slug' => $slug,
                    'name' => str_replace(['-', '_'], ' ', $slug) . ' (archivo)',
                ];
            }
        }

        usort($out, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $out;
    }

    protected function templatesDirectory(): string
    {
        $dir = storage_path('app/crm-email-templates');
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        return $dir;
    }

    protected function templatePath(string $slug): string
    {
        $slug = Str::slug($slug);
        abort_if($slug === '', 404);

        return $this->templatesDirectory() . DIRECTORY_SEPARATOR . $slug . '.html';
    }

    protected function persistTemplate(string $html, string $name): string
    {
        $base = Str::slug($name) ?: ('plantilla-' . now()->format('Ymd-His'));
        $slug = $base;
        $i = 2;
        while (EmailTemplate::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        EmailTemplate::create([
            'name' => trim($name) !== '' ? trim($name) : $slug,
            'slug' => $slug,
            'html_body' => $html,
            'created_by' => auth()->id(),
        ]);

        // Also keep a file copy when storage is writable (optional backup)
        try {
            @file_put_contents($this->templatePath($slug), $html);
        } catch (\Throwable) {
            // ignore
        }

        return $slug;
    }

    /**
     * @return array<string, string> relative path => public url
     */
    protected function storeUploadedTemplateAssets(Request $request): array
    {
        $files = $request->file('assets', []);
        if (! is_array($files) || $files === []) {
            return [];
        }

        $paths = $request->input('asset_paths', []);
        $token = 't'.Str::lower(Str::random(8));
        $map = [];

        foreach ($files as $i => $file) {
            if (! $file) {
                continue;
            }
            $rel = (string) ($paths[$i] ?? $file->getClientOriginalName());
            $rel = $this->normalizeTemplateAssetPath($rel);
            $nested = trim(str_replace('\\', '/', dirname($rel)), '/.');
            $subdir = 'campaigns/templates/'.$token;
            if ($nested !== '' && $nested !== '.') {
                $subdir .= '/'.$nested;
            }
            $stored = PublicMedia::storeFilesystemFile($file->getRealPath(), $subdir, basename($rel));
            $url = Branding::assetUrl($stored);
            $map[$rel] = $url;
            $map['images/'.basename($rel)] = $url;
            $map[basename($rel)] = $url;
        }

        return $map;
    }

    /**
     * @return array{0: string, 1: array<string, string>}
     */
    protected function extractTemplateZip(\Illuminate\Http\UploadedFile $zipFile): array
    {
        if (! class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Este servidor no puede abrir ZIP. Sube el .html y las imágenes por separado.');
        }

        $tmp = storage_path('app/tmp/tpl-'.uniqid('', true));
        if (! mkdir($tmp, 0755, true) && ! is_dir($tmp)) {
            throw new \RuntimeException('No se pudo crear carpeta temporal.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFile->getRealPath()) !== true) {
            throw new \RuntimeException('No se pudo abrir el ZIP.');
        }

        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = str_replace('\\', '/', (string) $zip->getNameIndex($i));
                if ($name === '' || str_contains($name, '..') || str_ends_with($name, '/')) {
                    continue;
                }
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (! in_array($ext, ['html', 'htm', 'png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
                    continue;
                }
                $target = $tmp.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $name);
                $dir = dirname($target);
                if (! is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $contents = $zip->getFromIndex($i);
                if ($contents === false) {
                    continue;
                }
                file_put_contents($target, $contents);
            }
        } finally {
            $zip->close();
        }

        $htmlFile = $this->findExtractedHtml($tmp);
        if (! $htmlFile) {
            $this->deleteDirectory($tmp);
            throw new \RuntimeException('El ZIP no contiene un archivo .html.');
        }

        $html = (string) file_get_contents($htmlFile);
        $root = dirname($htmlFile);
        $token = 't'.Str::lower(Str::random(8));
        $map = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (! in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
                continue;
            }
            $rel = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($root))), '/');
            $rel = $this->normalizeTemplateAssetPath($rel);
            $nested = trim(str_replace('\\', '/', dirname($rel)), '/.');
            $subdir = 'campaigns/templates/'.$token;
            if ($nested !== '' && $nested !== '.') {
                $subdir .= '/'.$nested;
            }
            $stored = PublicMedia::storeFilesystemFile($file->getPathname(), $subdir, basename($rel));
            $url = Branding::assetUrl($stored);
            $map[$rel] = $url;
            $map['images/'.basename($rel)] = $url;
            $map[basename($rel)] = $url;
        }

        $this->deleteDirectory($tmp);

        return [$html, $map];
    }

    protected function findExtractedHtml(string $dir): ?string
    {
        $preferred = null;
        $any = null;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (! in_array($ext, ['html', 'htm'], true)) {
                continue;
            }
            $any = $file->getPathname();
            if (strcasecmp($file->getFilename(), 'email.html') === 0) {
                $preferred = $any;
            }
        }

        return $preferred ?: $any;
    }

    protected function normalizeTemplateAssetPath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');
        if (preg_match('#(?:^|/)(images/[^/]+)$#i', $path, $m)) {
            return $m[1];
        }

        return $path;
    }

    /**
     * @param  array<string, string>  $map
     */
    protected function rewriteTemplateAssetUrls(string $html, array $map): string
    {
        if ($map === []) {
            return $html;
        }

        uksort($map, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($map as $from => $to) {
            if ($from === '' || $to === '') {
                continue;
            }
            $html = str_replace(
                [
                    'src="'.$from.'"',
                    "src='".$from."'",
                    'href="'.$from.'"',
                    "href='".$from."'",
                    'url('.$from.')',
                    'url("'.$from.'")',
                    "url('".$from."')",
                ],
                [
                    'src="'.$to.'"',
                    "src='".$to."'",
                    'href="'.$to.'"',
                    "href='".$to."'",
                    'url('.$to.')',
                    'url("'.$to.'")',
                    "url('".$to."')",
                ],
                $html
            );
        }

        return $html;
    }

    protected function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($dir);
    }

    protected function sanitizeCampaignHtml(string $html): string
    {
        // Remove scripts / PHP / dangerous handlers while keeping email-safe markup.
        $html = preg_replace('/<\?(?:php|=)?[\s\S]*?\?>/i', '', $html) ?? $html;
        $html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html) ?? $html;
        $html = preg_replace('/\son\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/javascript\s*:/i', '', $html) ?? $html;

        // Drop iframes except trusted video hosts (YouTube / Vimeo).
        $html = preg_replace_callback('/<iframe\b[^>]*>[\s\S]*?<\/iframe>/i', function (array $m) {
            $tag = $m[0];
            if (preg_match('/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $tag, $src)
                && preg_match('#^(https?:)?//(www\.)?(youtube\.com|youtube-nocookie\.com|player\.vimeo\.com)/#i', $src[1])) {
                return $tag;
            }

            return '';
        }, $html) ?? $html;

        // Prefer body content if a full document was uploaded.
        if (preg_match('/<body\b[^>]*>([\s\S]*?)<\/body>/i', $html, $m)) {
            $body = trim($m[1]);
            // Keep inline <style> from head for email templates.
            $styles = '';
            if (preg_match_all('/<style\b[^>]*>[\s\S]*?<\/style>/i', $html, $sm)) {
                $styles = implode("\n", $sm[0]);
            }
            $html = trim($styles . "\n" . $body);
        }

        return trim($html);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'html_body' => 'required|string|max:100000',
            'text_body' => 'nullable|string|max:50000',
            'cta_label' => 'nullable|string|max:120',
            'cta_url' => 'nullable|url|max:500',
            'audience' => 'required|in:all,stage,selected',
            'lifecycle_stage' => 'nullable|in:lead,mql,sql,customer',
            'contact_ids' => 'nullable|array',
            'contact_ids.*' => 'integer|exists:crm_contacts,id',
        ]);

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'html_body' => $validated['html_body'],
            'text_body' => $validated['text_body'] ?? null,
            'cta_label' => $validated['cta_label'] ?? 'Contáctanos',
            'cta_url' => $validated['cta_url'] ?? url('/contact'),
            'status' => 'draft',
            'from_name' => config('mail.from.name'),
            'from_email' => config('mail.from.address'),
            'created_by' => auth()->id(),
        ]);

        $query = Contact::query()
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($validated['audience'] === 'stage' && ! empty($validated['lifecycle_stage'])) {
            $query->where('lifecycle_stage', $validated['lifecycle_stage']);
        } elseif ($validated['audience'] === 'selected') {
            $ids = $validated['contact_ids'] ?? [];
            $query->whereIn('id', $ids);
        }

        $contacts = $query->get(['id', 'first_name', 'last_name', 'email']);
        $unsub = EmailUnsubscribe::query()->pluck('email')->map(fn ($e) => strtolower($e))->all();
        $added = 0;

        foreach ($contacts as $contact) {
            $email = strtolower(trim((string) $contact->email));
            if ($email === '' || in_array($email, $unsub, true)) {
                continue;
            }

            CampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'name' => trim($contact->first_name . ' ' . $contact->last_name),
                'token' => Str::random(40),
                'status' => 'pending',
            ]);
            $added++;
        }

        $campaign->update(['recipients_count' => $added]);

        return redirect()
            ->route('admin.crm.campaigns.show', $campaign)
            ->with('success', "Campaña creada con {$added} destinatarios.");
    }

    public function show(Campaign $campaign)
    {
        $campaign->loadCount([
            'recipients as pending_count' => fn ($q) => $q->where('status', 'pending'),
        ]);

        $recentOpens = $campaign->events()
            ->with('recipient')
            ->where('type', 'open')
            ->latest()
            ->limit(10)
            ->get();

        $recentClicks = $campaign->events()
            ->with('recipient')
            ->where('type', 'click')
            ->latest()
            ->limit(10)
            ->get();

        $topLinks = $campaign->events()
            ->where('type', 'click')
            ->whereNotNull('url')
            ->select('url', DB::raw('count(*) as total'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $recipientFilter = (string) request()->query('status', '');
        $allowedFilters = ['pending', 'sent', 'failed', 'skipped', 'unsubscribed', 'opened', 'clicked'];
        if (! in_array($recipientFilter, $allowedFilters, true)) {
            $recipientFilter = '';
        }

        $recipients = $campaign->recipients()
            ->with('contact')
            ->when($recipientFilter === 'opened', fn ($q) => $q->whereNotNull('opened_at'))
            ->when($recipientFilter === 'clicked', fn ($q) => $q->whereNotNull('clicked_at'))
            ->when(
                $recipientFilter !== '' && ! in_array($recipientFilter, ['opened', 'clicked'], true),
                fn ($q) => $q->where('status', $recipientFilter)
            )
            ->orderByRaw('sent_at IS NULL')
            ->orderByDesc('sent_at')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $statusCounts = $campaign->recipients()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recipientCounts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'sent' => (int) ($statusCounts['sent'] ?? 0),
            'failed' => (int) ($statusCounts['failed'] ?? 0),
            'skipped' => (int) ($statusCounts['skipped'] ?? 0),
            'unsubscribed' => (int) ($statusCounts['unsubscribed'] ?? 0),
            'opened' => (int) $campaign->recipients()->whereNotNull('opened_at')->count(),
            'clicked' => (int) $campaign->recipients()->whereNotNull('clicked_at')->count(),
        ];

        $ctaClicks = $campaign->events()
            ->where('type', 'click')
            ->when($campaign->cta_url, function ($q) use ($campaign) {
                $q->where('url', 'like', '%' . parse_url($campaign->cta_url, PHP_URL_HOST) . '%');
            })
            ->count();

        $previewHtml = CampaignMailer::previewHtml($campaign);

        return view('admin.crm.campaigns.show', compact(
            'campaign', 'recentOpens', 'recentClicks', 'topLinks', 'recipients', 'recipientFilter',
            'recipientCounts', 'ctaClicks', 'previewHtml'
        ));
    }

    public function send(Request $request, Campaign $campaign)
    {
        $limit = min(100, max(1, (int) $request->input('batch', 50)));

        if (config('queue.default') !== 'sync') {
            SendCampaignBatch::dispatch($campaign->id, $limit);

            return back()->with('success', 'Envío encolado. El worker (Redis/Horizon) lo procesará.');
        }

        $result = app(CampaignSender::class)->sendBatch($campaign, $limit);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->status === 'sending') {
            return back()->with('error', 'No se puede eliminar una campaña en envío.');
        }

        $campaign->delete();

        return redirect()->route('admin.crm.campaigns.index')->with('success', 'Campaña eliminada.');
    }

    protected function campaignsSetupView()
    {
        return view('admin.crm.setup', [
            'missingTables' => ['crm_campaigns', 'crm_campaign_recipients', 'crm_campaign_events'],
            'sqlFile' => 'database/sql/crm_campaigns.sql',
        ]);
    }

    protected function campaignsTableReady(): bool
    {
        try {
            return Schema::hasTable('crm_campaigns');
        } catch (\Throwable) {
            return false;
        }
    }
}
