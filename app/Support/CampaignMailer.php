<?php

namespace App\Support;

use App\Models\CRM\Campaign;
use App\Models\CRM\CampaignEvent;
use App\Models\CRM\CampaignRecipient;
use App\Models\CRM\EmailUnsubscribe;
use Illuminate\Support\Str;

class CampaignMailer
{
    public static function buildHtml(Campaign $campaign, CampaignRecipient $recipient): string
    {
        $html = (string) $campaign->html_body;
        $first = explode(' ', trim((string) $recipient->name))[0] ?: 'hola';
        $unsub = route('crm.track.unsubscribe', $recipient->token);
        $ctaUrl = $campaign->cta_url ?: url('/contact');
        $ctaLabel = $campaign->cta_label ?: 'Contáctanos';

        $trackedCta = route('crm.track.click', [
            'token' => $recipient->token,
            'u' => self::encodeUrl($ctaUrl),
        ]);

        $replacements = [
            '{{name}}' => e($recipient->name ?: $first),
            '{{first_name}}' => e($first),
            '{{email}}' => e($recipient->email),
            '{{unsubscribe_url}}' => $unsub,
            '{{cta_url}}' => $trackedCta,
            '{{cta_label}}' => e($ctaLabel),
            '{{site_url}}' => e(rtrim((string) config('app.url'), '/')),
            '{{logo_url}}' => e(self::absoluteLogoUrl()),
        ];

        $html = strtr($html, $replacements);

        $html = self::absolutizeMediaUrls($html);

        // Auto-append CTA button if not already in body and cta configured
        if (! str_contains($campaign->html_body, '{{cta_url}}') && $campaign->cta_url) {
            $html .= '<p style="margin:24px 0;text-align:center">'
                . '<a href="' . e($trackedCta) . '" style="display:inline-block;background:#05070a;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:600">'
                . e($ctaLabel) . '</a></p>';
        }

        // Rewrite remaining http(s) links through click tracker (except unsubscribe / track)
        $html = preg_replace_callback(
            '/href=(["\'])(https?:\/\/.+?)\1/i',
            function ($m) use ($recipient, $unsub) {
                $url = html_entity_decode($m[2], ENT_QUOTES);
                if (str_contains($url, '/t/') || $url === $unsub) {
                    return $m[0];
                }
                $tracked = route('crm.track.click', [
                    'token' => $recipient->token,
                    'u' => self::encodeUrl($url),
                ]);

                return 'href=' . $m[1] . $tracked . $m[1];
            },
            $html
        ) ?? $html;

        $company = e((string) config('crm.company_name', config('app.name')));
        $address = e((string) config('crm.company_address', ''));
        $pixel = route('crm.track.open', $recipient->token);

        $html .= '<hr style="border:none;border-top:1px solid #e5e7eb;margin:28px 0 12px">'
            . '<p style="font-size:12px;color:#6b7280;line-height:1.5">'
            . $company . ($address ? ' · ' . $address : '') . '<br>'
            . 'Si no deseas recibir estos correos, <a href="' . e($unsub) . '" style="color:#6b7280">cancela la suscripción</a>.'
            . '</p>'
            . '<img src="' . e($pixel) . '" width="1" height="1" alt="" style="display:block;width:1px;height:1px;border:0">';

        return $html;
    }

    public static function buildText(Campaign $campaign, CampaignRecipient $recipient): string
    {
        $text = trim((string) ($campaign->text_body ?: strip_tags($campaign->html_body)));
        $first = explode(' ', trim((string) $recipient->name))[0] ?: 'hola';
        $unsub = route('crm.track.unsubscribe', $recipient->token);
        $ctaUrl = route('crm.track.click', [
            'token' => $recipient->token,
            'u' => self::encodeUrl($campaign->cta_url ?: url('/contact')),
        ]);

        $text = strtr($text, [
            '{{name}}' => $recipient->name ?: $first,
            '{{first_name}}' => $first,
            '{{email}}' => $recipient->email,
            '{{unsubscribe_url}}' => $unsub,
            '{{cta_url}}' => $ctaUrl,
            '{{cta_label}}' => $campaign->cta_label ?: 'Contáctanos',
        ]);

        $text .= "\n\n---\n"
            . config('crm.company_name', config('app.name')) . "\n"
            . config('crm.company_address', '') . "\n"
            . "Cancelar suscripción: {$unsub}\n";

        return $text;
    }

    /**
     * HTML for the admin iframe preview (no tracking pixel / click wrappers).
     */
    public static function previewHtml(Campaign $campaign): string
    {
        $html = (string) $campaign->html_body;
        $ctaUrl = $campaign->cta_url ?: url('/contact');
        $ctaLabel = $campaign->cta_label ?: 'Contáctanos';

        $html = strtr($html, [
            '{{name}}' => 'María García',
            '{{first_name}}' => 'María',
            '{{email}}' => 'preview@cloudlablatam.tech',
            '{{unsubscribe_url}}' => '#unsubscribe',
            '{{cta_url}}' => e($ctaUrl),
            '{{cta_label}}' => e($ctaLabel),
            '{{site_url}}' => e(rtrim((string) config('app.url'), '/')),
            '{{logo_url}}' => e(self::absoluteLogoUrl()),
        ]);

        if (! str_contains((string) $campaign->html_body, '{{cta_url}}') && $campaign->cta_url) {
            $html .= '<p style="margin:24px 0;text-align:center">'
                . '<a href="' . e($ctaUrl) . '" style="display:inline-block;background:#05070a;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:600">'
                . e($ctaLabel) . '</a></p>';
        }

        return self::wrapPreviewDocument($html);
    }

    /**
     * Email clients ignore root-relative image paths; make them absolute using APP_URL.
     */
    public static function absolutizeMediaUrls(string $html): string
    {
        $base = rtrim((string) config('app.url'), '/');
        if ($base === '') {
            return $html;
        }

        return preg_replace_callback(
            '/\b(src)=(["\'])(\/(?:images|videos)\/[^"\']+)\2/i',
            function (array $m) use ($base) {
                return $m[1].'='.$m[2].$base.$m[3].$m[2];
            },
            $html
        ) ?? $html;
    }

    public static function wrapPreviewDocument(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '<!DOCTYPE html><html><body style="font-family:Arial,Helvetica,sans-serif;color:#888;padding:24px">Sin contenido</body></html>';
        }

        if (preg_match('/<\s*html[\s>]/i', $html)) {
            return $html;
        }

        return '<!DOCTYPE html><html><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<base target="_blank" rel="noopener">'
            . '<style>body{margin:0;padding:16px;font-family:Arial,Helvetica,sans-serif;line-height:1.5;color:#222;word-wrap:break-word}img{max-width:100%;height:auto}table{max-width:100%}video,iframe{max-width:100%}</style>'
            . '</head><body>' . $html . '</body></html>';
    }

    public static function encodeUrl(string $url): string
    {
        return rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
    }

    public static function decodeUrl(string $encoded): ?string
    {
        $padded = strtr($encoded, '-_', '+/');
        $pad = strlen($padded) % 4;
        if ($pad > 0) {
            $padded .= str_repeat('=', 4 - $pad);
        }
        $url = base64_decode($padded, true);
        if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (! in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        return $url;
    }

    /**
     * Click tracking may only leave the site for hosts already present in that campaign.
     */
    public static function allowedClickUrl(?string $url, ?Campaign $campaign): ?string
    {
        if (! $url) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '' || ! in_array($host, self::campaignHosts($campaign), true)) {
            return null;
        }

        return $url;
    }

    /**
     * @return array<int, string>
     */
    protected static function campaignHosts(?Campaign $campaign): array
    {
        $hosts = [];
        foreach ([config('app.url')] as $base) {
            $host = strtolower((string) parse_url((string) $base, PHP_URL_HOST));
            if ($host !== '') {
                $hosts[] = $host;
            }
        }

        $blob = trim(implode(' ', array_filter([
            $campaign?->html_body,
            $campaign?->text_body,
            $campaign?->cta_url,
        ])));

        if ($blob !== '' && preg_match_all('#https?://([^/\s"\'<>]+)#i', $blob, $matches)) {
            foreach ($matches[1] as $host) {
                $hosts[] = strtolower($host);
            }
        }

        return array_values(array_unique($hosts));
    }

    public static function recordOpen(CampaignRecipient $recipient, ?string $ip, ?string $ua): void
    {
        if (self::looksLikeBot($ua)) {
            return;
        }

        $isFirst = $recipient->opened_at === null;
        $recipient->open_count = (int) $recipient->open_count + 1;
        if ($isFirst) {
            $recipient->opened_at = now();
        }
        $recipient->save();

        CampaignEvent::create([
            'campaign_id' => $recipient->campaign_id,
            'recipient_id' => $recipient->id,
            'type' => 'open',
            'ip' => $ip,
            'user_agent' => Str::limit((string) $ua, 500, ''),
        ]);

        $campaign = $recipient->campaign;
        $campaign->increment('open_count');
        if ($isFirst) {
            $campaign->increment('unique_open_count');
        }
    }

    public static function recordClick(CampaignRecipient $recipient, string $url, ?string $ip, ?string $ua): void
    {
        $recipient->loadMissing('campaign');
        $campaign = $recipient->campaign;
        if (! $campaign) {
            return;
        }

        $isFirstClick = $recipient->clicked_at === null;
        $isFirstOpen = $recipient->opened_at === null;

        $recipient->click_count = (int) $recipient->click_count + 1;
        if ($isFirstClick) {
            $recipient->clicked_at = now();
        }

        if ($isFirstOpen) {
            $recipient->opened_at = now();
            $recipient->open_count = max(1, (int) $recipient->open_count);
            $campaign->increment('unique_open_count');
            $campaign->increment('open_count');
        }

        $recipient->save();

        CampaignEvent::create([
            'campaign_id' => $recipient->campaign_id,
            'recipient_id' => $recipient->id,
            'type' => 'click',
            'url' => Str::limit($url, 2000, ''),
            'ip' => $ip,
            'user_agent' => Str::limit((string) $ua, 500, ''),
        ]);

        $campaign->increment('click_count');
        if ($isFirstClick) {
            $campaign->increment('unique_click_count');
        }
    }

    public static function unsubscribe(CampaignRecipient $recipient, ?string $ip, ?string $ua): void
    {
        EmailUnsubscribe::query()->firstOrCreate(
            ['email' => strtolower($recipient->email)],
            ['reason' => 'campaign_unsubscribe']
        );

        $recipient->status = 'unsubscribed';
        $recipient->save();

        CampaignEvent::create([
            'campaign_id' => $recipient->campaign_id,
            'recipient_id' => $recipient->id,
            'type' => 'unsubscribe',
            'ip' => $ip,
            'user_agent' => Str::limit((string) $ua, 500, ''),
        ]);

        $recipient->campaign?->increment('unsubscribe_count');
    }

    public static function looksLikeBot(?string $ua): bool
    {
        $ua = strtolower((string) $ua);
        if ($ua === '') {
            return false;
        }

        foreach (['googleimageproxy', 'yahoo mail', 'prefetch', 'bot', 'crawler', 'spider', 'preview'] as $needle) {
            if (str_contains($ua, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected static function absoluteLogoUrl(): string
    {
        $relative = Branding::assetUrl('images/logo-brand.png');
        if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
            return $relative;
        }

        return rtrim((string) config('app.url'), '/') . '/' . ltrim($relative, '/');
    }
}
