@extends('admin.layouts.app')

@section('title', 'Nueva campaña - CRM')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet"
      onerror="this.remove()">
<link rel="stylesheet" href="{{ \App\Support\Branding::assetUrl('css/campaign-studio.css') }}?v=preview-1">
<link rel="stylesheet" href="{{ \App\Support\Branding::alternateAssetUrl('css/campaign-studio.css') }}?v=preview-1">
<style nonce="{{ $cspNonce ?? '' }}">
    #campaign-body-editor .ql-editor {
        min-height: 280px;
        font-size: 1rem;
        line-height: 1.6;
    }
    #campaign-body-editor .ql-toolbar {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        background: #f8f9fa;
    }
    #campaign-body-editor .ql-container {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        background: #fff;
    }
    #htmlSourceEditor {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 0.85rem;
        min-height: 320px;
    }
    #campaign-body-editor .ql-editor img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1em auto;
        border-radius: 8px;
    }
    #campaign-body-editor .ql-editor video,
    #campaign-body-editor .ql-editor iframe.ql-video {
        display: block;
        width: 100%;
        max-width: 640px;
        min-height: 280px;
        margin: 1em auto;
        background: #000;
    }
    #campaignPreviewFrame {
        width: 100%;
        min-height: 420px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
    }
    .campaign-mode-tabs .nav-link { cursor: pointer; }
    .campaign-media-bar .btn { white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.crm.campaigns.index') }}" class="text-decoration-none">&larr; Campañas</a>
    <h1 class="h3 mt-2">Nueva campaña</h1>
    <p class="text-muted mb-0">Editor a la izquierda, vista previa del correo a la derecha.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@php
    $defaultBody = '<p>Hola {' . '{first_name}},</p>'
        . '<p>En CloudLab Latam te ayudamos a automatizar tu negocio con IA, cloud y analytics.</p>'
        . '<p>¿Agendamos una llamada?</p>';
@endphp

<div class="row g-4 align-items-start">
<div class="col-xl-6">
<form method="POST" action="{{ route('admin.crm.campaigns.store') }}" class="card shadow-sm" id="campaignCreateForm">
    @csrf
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre interno *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255"
                   placeholder="Ej. Promoción cloud agosto">
        </div>
        <div class="col-md-6">
            <label class="form-label">Asunto del correo *</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required maxlength="255"
                   placeholder="Evita MAYÚSCULAS y spam words">
        </div>

        <div class="col-12">
            <div class="border rounded p-3 bg-light">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <strong>Plantilla HTML</strong>
                    <span class="small text-muted">.html + carpeta images/, o un .zip con ambos</span>
                </div>
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label" for="templateFile">HTML o ZIP</label>
                        <input type="file" id="templateFile" class="form-control" accept=".html,.htm,.zip,text/html,application/zip">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="templateSaveName">Nombre al guardar</label>
                        <input type="text" id="templateSaveName" class="form-control" placeholder="promo-agosto">
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="templateSave" value="1" checked>
                            <label class="form-check-label" for="templateSave">Guardar para reutilizar</label>
                        </div>
                        <button type="button" class="btn btn-dark w-100" id="templateUploadBtn">
                            <i class="bi bi-upload"></i> Cargar plantilla
                        </button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="templateImages">Imágenes de la plantilla</label>
                        <input type="file" id="templateImages" class="form-control" accept="image/png,image/jpeg,image/gif,image/webp,.png,.jpg,.jpeg,.gif,.webp" multiple>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="templateFolder">O carpeta completa (email.html + images)</label>
                        <input type="file" id="templateFolder" class="form-control" webkitdirectory directory multiple>
                    </div>
                </div>
                <div class="form-text">Las plantillas tipo Stripo/Mailchimp traen <code>src="images/foto.png"</code>. Sube esas imágenes, la carpeta o un ZIP; si no, el correo se ve sin fotos.</div>
                <div class="row g-2 mt-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label" for="savedTemplateSelect">Plantillas guardadas</label>
                        <select id="savedTemplateSelect" class="form-select" @if(empty($savedTemplates)) disabled @endif>
                            <option value="">— Seleccionar plantilla guardada —</option>
                            @foreach($savedTemplates as $tpl)
                                <option value="{{ $tpl['slug'] }}">{{ $tpl['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary flex-fill" id="templateLoadBtn">Usar</button>
                        <button type="button" class="btn btn-outline-danger flex-fill" id="templateDeleteBtn">Eliminar</button>
                    </div>
                </div>
                <div id="templateStatus" class="small mt-2 text-muted" role="status"></div>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Cuerpo del mensaje *</label>
            <ul class="nav nav-tabs campaign-mode-tabs mb-2" role="tablist">
                <li class="nav-item"><button type="button" class="nav-link active" data-mode="visual">Visual</button></li>
                <li class="nav-item"><button type="button" class="nav-link" data-mode="html">HTML</button></li>
            </ul>

            <div class="campaign-media-bar border rounded p-2 mb-2 bg-light d-flex flex-wrap gap-2 align-items-center">
                <span class="small text-muted me-1">Medios:</span>
                <button type="button" class="btn btn-sm btn-outline-dark" id="campaignUploadImageBtn">
                    <i class="bi bi-image"></i> Subir imagen
                </button>
                <button type="button" class="btn btn-sm btn-outline-dark" id="campaignUploadVideoBtn">
                    <i class="bi bi-camera-video"></i> Subir video
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="campaignEmbedVideoBtn">
                    <i class="bi bi-youtube"></i> YouTube / Vimeo
                </button>
                <input type="file" id="campaignImageFile" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
                <input type="file" id="campaignVideoFile" class="d-none" accept="video/mp4,video/webm,video/ogg,.mp4,.webm,.ogg">
                <span id="campaignMediaStatus" class="small text-muted" role="status"></span>
            </div>
            <div class="form-text mb-2">
                Imágenes: JPG/PNG/GIF/WebP (máx. 8&nbsp;MB). Videos: MP4/WebM (máx. 50&nbsp;MB).
                Nota: muchos clientes de correo no reproducen video; se inserta con enlace de respaldo.
            </div>

            <div id="modeVisual">
                <div id="campaign-body-editor"></div>
            </div>
            <div id="modeHtml" class="d-none">
                <textarea id="htmlSourceEditor" class="form-control" rows="16" spellcheck="false"></textarea>
            </div>

            <textarea id="html_body" name="html_body" required hidden aria-hidden="true">{{ old('html_body', $defaultBody) }}</textarea>
            <div class="form-text mt-2">
                Placeholders: <code>@{{name}}</code>, <code>@{{first_name}}</code>, <code>@{{email}}</code>,
                <code>@{{cta_url}}</code>, <code>@{{cta_label}}</code>, <code>@{{unsubscribe_url}}</code>.
            </div>
            @error('html_body')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Texto del botón CTA</label>
            <input type="text" name="cta_label" class="form-control" value="{{ old('cta_label', 'Consulta gratuita') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">URL del botón CTA (se trackea)</label>
            <input type="url" name="cta_url" class="form-control" value="{{ old('cta_url', $defaultCtaUrl) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Audiencia *</label>
            <select name="audience" id="audience" class="form-select" required>
                <option value="all">Todos con email ({{ $contactCount }})</option>
                <option value="stage">Por etapa</option>
            </select>
        </div>
        <div class="col-md-6" id="stageWrap" style="display:none">
            <label class="form-label">Etapa</label>
            <select name="lifecycle_stage" class="form-select">
                @foreach($stages as $value => $label)
                    @if($value !== '')
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Versión texto (opcional)</label>
            <textarea name="text_body" class="form-control" rows="4" placeholder="Se genera automáticamente si lo dejas vacío">{{ old('text_body') }}</textarea>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('admin.crm.campaigns.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-dark">Crear campaña</button>
    </div>
</form>
</div>
<div class="col-xl-6">
    <div class="campaign-studio-aside">
        @include('admin.crm.campaigns._preview', ['frameId' => 'campaignPreviewFrame'])
        <p class="small text-muted mt-2 mb-0">La vista previa se actualiza al escribir o al cargar una plantilla.</p>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ \App\Support\Branding::assetUrl('js/campaign-preview.js') }}?v=preview-1" nonce="{{ $cspNonce ?? '' }}"></script>
<script src="{{ \App\Support\Branding::alternateAssetUrl('js/campaign-preview.js') }}?v=preview-1" nonce="{{ $cspNonce ?? '' }}"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js" nonce="{{ $cspNonce ?? '' }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    function ready(fn) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    ready(function () {
        var form = document.getElementById('campaignCreateForm');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        // Resolve API URLs from the current page path so subdirectory installs
        // (e.g. /cloudlablatam1/public/...) and Hostinger root both work.
        // /admin/crm/campaigns/create → /admin/crm/campaigns
        var campaignsBase = window.location.pathname.replace(/\/+$/, '').replace(/\/create$/, '');
        var uploadUrl = campaignsBase + '/templates/upload';
        var loadUrlBase = campaignsBase + '/templates';
        var mediaUploadUrl = campaignsBase + '/media';
        var textarea = document.getElementById('html_body');
        var source = document.getElementById('htmlSourceEditor');
        var statusEl = document.getElementById('templateStatus');
        var mediaStatusEl = document.getElementById('campaignMediaStatus');
        var savedSelect = document.getElementById('savedTemplateSelect');
        var uploadBtn = document.getElementById('templateUploadBtn');
        var mode = 'visual';
        var preferHtmlMode = false;
        var quill = null;

        document.getElementById('audience')?.addEventListener('change', function () {
            document.getElementById('stageWrap').style.display = this.value === 'stage' ? '' : 'none';
        });

        function setMediaStatus(msg, isError) {
            if (!mediaStatusEl) return;
            mediaStatusEl.textContent = msg || '';
            mediaStatusEl.className = 'small ' + (isError ? 'text-danger' : 'text-success');
        }

        function insertHtmlSnippet(snippet) {
            snippet = String(snippet || '');
            if (!snippet) return;

            if (mode === 'visual' && quill) {
                var range = quill.getSelection(true) || { index: quill.getLength(), length: 0 };
                quill.clipboard.dangerouslyPasteHTML(range.index, snippet);
                quill.setSelection(range.index + 1);
                syncFromActiveMode();
                return;
            }

            // HTML / preview: insert into source and keep hidden textarea in sync
            var target = source || textarea;
            if (target) {
                var start = typeof target.selectionStart === 'number' ? target.selectionStart : target.value.length;
                var end = typeof target.selectionEnd === 'number' ? target.selectionEnd : start;
                var before = target.value.slice(0, start);
                var after = target.value.slice(end);
                var block = '\n' + snippet + '\n';
                target.value = before + block + after;
                if (target === source && textarea) textarea.value = source.value;
                if (target === textarea && source) source.value = textarea.value;
                if (typeof target.setSelectionRange === 'function') {
                    var caret = start + block.length;
                    try {
                        target.focus();
                        target.setSelectionRange(caret, caret);
                    } catch (e) {}
                }
                syncFromActiveMode();
            }
        }

        function imageHtml(url) {
            return '<p style="text-align:center;margin:16px 0">'
                + '<img src="' + url + '" alt="" style="max-width:100%;height:auto;border:0;display:inline-block">'
                + '</p>';
        }

        function videoFileHtml(url) {
            return '<div style="text-align:center;margin:16px 0">'
                + '<video controls playsinline preload="metadata" style="max-width:100%;height:auto;background:#000">'
                + '<source src="' + url + '">'
                + '</video>'
                + '<p style="margin:8px 0 0;font-size:14px">'
                + '<a href="' + url + '" target="_blank" rel="noopener">Ver / descargar video</a>'
                + '</p></div>';
        }

        function youtubeEmbedHtml(rawUrl) {
            var url = String(rawUrl || '').trim();
            var id = null;
            var m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/i);
            if (m) id = m[1];
            if (id) {
                return '<div style="text-align:center;margin:16px 0">'
                    + '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/' + id + '"'
                    + ' title="YouTube" frameborder="0" allowfullscreen'
                    + ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"'
                    + ' style="max-width:100%;border:0"></iframe></div>';
            }
            m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
            if (m) {
                return '<div style="text-align:center;margin:16px 0">'
                    + '<iframe src="https://player.vimeo.com/video/' + m[1] + '" width="560" height="315"'
                    + ' frameborder="0" allowfullscreen style="max-width:100%;border:0"></iframe></div>';
            }
            return null;
        }

        async function uploadCampaignMedia(type, file) {
            if (!file) return;
            var maxImage = 8 * 1024 * 1024;
            var maxVideo = 50 * 1024 * 1024;
            if (type === 'image' && file.size > maxImage) {
                setMediaStatus('La imagen supera 8 MB', true);
                return;
            }
            if (type === 'video' && file.size > maxVideo) {
                setMediaStatus('El video supera 50 MB', true);
                return;
            }

            setMediaStatus('Subiendo ' + (type === 'image' ? 'imagen' : 'video') + '…');
            var fd = new FormData();
            fd.append('type', type);
            fd.append('file', file);
            fd.append('_token', csrf);

            try {
                var res = await fetch(mediaUploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd,
                    credentials: 'same-origin'
                });
                var parsed = await parseJsonSafe(res);
                if (!parsed.ok || !parsed.data.success) {
                    var msg = parsed.data.message;
                    if (parsed.data.errors) {
                        msg = Object.values(parsed.data.errors).flat().join(' ');
                    }
                    setMediaStatus(msg || ('Error HTTP ' + parsed.status), true);
                    return;
                }
                var url = parsed.data.url;
                if (type === 'image') {
                    insertHtmlSnippet(imageHtml(url));
                } else {
                    insertHtmlSnippet(videoFileHtml(url));
                }
                if (mode === 'preview') refreshPreview();
                setMediaStatus((type === 'image' ? 'Imagen' : 'Video') + ' insertado.');
            } catch (err) {
                setMediaStatus('Error de red al subir', true);
            }
        }

        function setStatus(msg, isError) {
            if (!statusEl) return;
            statusEl.textContent = msg || '';
            statusEl.className = 'small mt-2 ' + (isError ? 'text-danger' : 'text-success');
        }

        function getBody() {
            return textarea ? textarea.value : '';
        }

        function setBody(html, switchToHtml) {
            html = html || '';
            if (textarea) textarea.value = html;
            if (source) source.value = html;
            if (quill && !switchToHtml) {
                try { quill.root.innerHTML = html; } catch (e) {}
            }
            if (switchToHtml) {
                preferHtmlMode = true;
                setMode('html');
            }
            refreshPreview();
        }

        function syncFromActiveMode() {
            if (mode === 'visual' && quill) {
                var html = quill.root.innerHTML;
                if (html === '<p><br></p>') html = '';
                if (textarea) textarea.value = html;
                if (source) source.value = html;
            } else if (mode === 'html' && source) {
                if (textarea) textarea.value = source.value;
            }
        }

        var previewTimer = null;

        function schedulePreview() {
            clearTimeout(previewTimer);
            previewTimer = setTimeout(refreshPreview, 250);
        }

        function refreshPreview() {
            var frame = document.getElementById('campaignPreviewFrame');
            if (!frame) return;
            var body = getBody() || '';
            if (window.CampaignPreview) {
                CampaignPreview.render(frame, body);
                return;
            }
            if (!body.trim()) {
                frame.srcdoc = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;color:#888;padding:16px">Sin contenido</body></html>';
                return;
            }
            var html = body;
            if (!/<\s*html[\s>]/i.test(html)) {
                html = '<!DOCTYPE html><html><head><meta charset="utf-8">'
                    + '<meta name="viewport" content="width=device-width, initial-scale=1">'
                    + '<base target="_blank" rel="noopener">'
                    + '<style>body{margin:16px;font-family:Arial,Helvetica,sans-serif;line-height:1.5;color:#222;word-wrap:break-word}img{max-width:100%;height:auto}table{max-width:100%}</style>'
                    + '</head><body>' + html + '</body></html>';
            }
            try {
                frame.srcdoc = html;
            } catch (e) {
                try {
                    var doc = frame.contentDocument || frame.contentWindow.document;
                    doc.open();
                    doc.write(html);
                    doc.close();
                } catch (e2) {}
            }
        }

        function setMode(next) {
            syncFromActiveMode();
            mode = next;
            document.querySelectorAll('.campaign-mode-tabs .nav-link').forEach(function (btn) {
                btn.classList.toggle('active', btn.getAttribute('data-mode') === next);
            });
            document.getElementById('modeVisual')?.classList.toggle('d-none', next !== 'visual');
            document.getElementById('modeHtml')?.classList.toggle('d-none', next !== 'html');

            if (next === 'visual' && quill && !preferHtmlMode) {
                try { quill.root.innerHTML = getBody(); } catch (e) {}
            }
            if (next === 'html' && source) source.value = getBody();
            refreshPreview();
        }

        document.querySelectorAll('.campaign-mode-tabs .nav-link').forEach(function (btn) {
            btn.addEventListener('click', function () {
                setMode(btn.getAttribute('data-mode'));
            });
        });

        // Quill is optional — upload must work even if Quill fails (CSP, CDN, etc.)
        try {
            if (window.Quill && document.getElementById('campaign-body-editor')) {
                quill = new Quill('#campaign-body-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                [{ color: [] }, { background: [] }],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ align: [] }],
                                ['link', 'image', 'video'],
                                ['clean']
                            ],
                            handlers: {
                                image: function () {
                                    document.getElementById('campaignImageFile')?.click();
                                },
                                video: function () {
                                    var choice = window.prompt(
                                        'Pega URL de YouTube/Vimeo,\no deja vacío y se abrirá el selector para subir un archivo de video:',
                                        ''
                                    );
                                    if (choice === null) return;
                                    choice = choice.trim();
                                    if (choice) {
                                        var embed = youtubeEmbedHtml(choice);
                                        if (embed) {
                                            insertHtmlSnippet(embed);
                                            setMediaStatus('Video embebido.');
                                        } else {
                                            setMediaStatus('URL no reconocida. Usa YouTube o Vimeo.', true);
                                        }
                                    } else {
                                        document.getElementById('campaignVideoFile')?.click();
                                    }
                                }
                            }
                        }
                    },
                    placeholder: 'Escribe el mensaje o carga una plantilla HTML…'
                });
                quill.on('text-change', function () {
                    if (mode === 'visual') {
                        syncFromActiveMode();
                        schedulePreview();
                    }
                });
            } else {
                preferHtmlMode = true;
                setMode('html');
                setStatus('Editor visual no disponible; usa la pestaña HTML.', true);
            }
        } catch (e) {
            preferHtmlMode = true;
            setMode('html');
            setStatus('Editor visual no disponible; usa la pestaña HTML.', true);
        }

        document.getElementById('campaignUploadImageBtn')?.addEventListener('click', function () {
            document.getElementById('campaignImageFile')?.click();
        });
        document.getElementById('campaignUploadVideoBtn')?.addEventListener('click', function () {
            document.getElementById('campaignVideoFile')?.click();
        });
        document.getElementById('campaignEmbedVideoBtn')?.addEventListener('click', function () {
            var choice = window.prompt('URL de YouTube o Vimeo:', 'https://www.youtube.com/watch?v=');
            if (!choice) return;
            var embed = youtubeEmbedHtml(choice);
            if (!embed) {
                setMediaStatus('URL no reconocida. Usa YouTube o Vimeo.', true);
                return;
            }
            insertHtmlSnippet(embed);
            if (mode === 'preview') refreshPreview();
            setMediaStatus('Video embebido.');
        });
        document.getElementById('campaignImageFile')?.addEventListener('change', function () {
            var f = this.files && this.files[0];
            this.value = '';
            uploadCampaignMedia('image', f);
        });
        document.getElementById('campaignVideoFile')?.addEventListener('change', function () {
            var f = this.files && this.files[0];
            this.value = '';
            uploadCampaignMedia('video', f);
        });

        if (textarea && textarea.value) setBody(textarea.value, false);

        source?.addEventListener('input', function () {
            if (mode === 'html') {
                syncFromActiveMode();
                schedulePreview();
            }
        });

        form?.addEventListener('submit', function () {
            syncFromActiveMode();
        });

        function extractBodyHtml(html) {
            html = String(html || '');
            html = html.replace(/<\?(?:php|=)?[\s\S]*?\?>/gi, '');
            html = html.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '');
            var styles = [];
            html.replace(/<style\b[^>]*>[\s\S]*?<\/style>/gi, function (m) { styles.push(m); return ''; });
            var bodyMatch = html.match(/<body\b[^>]*>([\s\S]*?)<\/body>/i);
            if (bodyMatch) {
                return (styles.join('\n') + '\n' + bodyMatch[1]).trim();
            }
            return html.trim();
        }

        function readFileAsText(file) {
            return new Promise(function (resolve, reject) {
                var reader = new FileReader();
                reader.onload = function () { resolve(String(reader.result || '')); };
                reader.onerror = function () { reject(new Error('No se pudo leer el archivo')); };
                reader.readAsText(file);
            });
        }

        async function parseJsonSafe(res) {
            var raw = await res.text();
            try { return { ok: res.ok, status: res.status, data: raw ? JSON.parse(raw) : {} }; }
            catch (e) {
                var hint = raw.slice(0, 180) || ('HTTP ' + res.status);
                if (/404 Not Found/i.test(raw) || res.status === 404) {
                    hint = 'Ruta no encontrada (404). Recarga la página (Ctrl+F5). URL: ' + (res.url || '');
                }
                return { ok: res.ok, status: res.status, data: { message: hint } };
            }
        }

        function fillTemplates(list, selectSlug) {
            if (!savedSelect || !Array.isArray(list)) return;
            savedSelect.innerHTML = '<option value="">— Seleccionar plantilla guardada —</option>';
            list.forEach(function (t) {
                var opt = document.createElement('option');
                opt.value = t.slug;
                opt.textContent = t.name;
                savedSelect.appendChild(opt);
            });
            savedSelect.disabled = list.length === 0;
            if (selectSlug) {
                savedSelect.value = selectSlug;
            }
        }

        async function loadSavedTemplate(slug) {
            if (!slug) {
                setStatus('Selecciona una plantilla de la lista', true);
                return;
            }
            setStatus('Cargando plantilla…');
            try {
                var res = await fetch(loadUrlBase.replace(/\/$/, '') + '/' + encodeURIComponent(slug), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                var parsed = await parseJsonSafe(res);
                if (!parsed.ok || !parsed.data.success) {
                    setStatus(parsed.data.message || 'No se pudo cargar la plantilla', true);
                    return;
                }
                setBody(parsed.data.html, true);
                setStatus('Plantilla en uso: ' + (parsed.data.name || slug));
            } catch (err) {
                setStatus('Error de red al cargar plantilla', true);
            }
        }

        if (uploadBtn) {
            uploadBtn.addEventListener('click', async function (e) {
                e.preventDefault();
                e.stopPropagation();

                var fileInput = document.getElementById('templateFile');
                var imageInput = document.getElementById('templateImages');
                var folderInput = document.getElementById('templateFolder');
                var file = fileInput && fileInput.files && fileInput.files[0];
                var folderFiles = folderInput && folderInput.files ? Array.prototype.slice.call(folderInput.files) : [];
                var extraImages = imageInput && imageInput.files ? Array.prototype.slice.call(imageInput.files) : [];

                var folderHtml = null;
                var folderAssets = [];
                folderFiles.forEach(function (f) {
                    var rel = String(f.webkitRelativePath || f.name).replace(/\\/g, '/');
                    var lower = rel.toLowerCase();
                    if (lower.endsWith('.html') || lower.endsWith('.htm')) {
                        folderHtml = { file: f, path: rel };
                    } else if (/\.(png|jpe?g|gif|webp)$/i.test(lower)) {
                        folderAssets.push({ file: f, path: rel });
                    }
                });

                extraImages.forEach(function (f) {
                    folderAssets.push({ file: f, path: 'images/' + f.name });
                });

                if (!file && folderHtml) {
                    file = folderHtml.file;
                }

                if (!file) {
                    setStatus('Selecciona un .html, un .zip, o la carpeta completa de la plantilla', true);
                    return;
                }

                var name = (file.name || '').toLowerCase();
                var isZip = name.endsWith('.zip');
                if (!isZip && !name.endsWith('.html') && !name.endsWith('.htm')) {
                    setStatus('El archivo debe ser .html, .htm o .zip', true);
                    return;
                }

                uploadBtn.disabled = true;
                setStatus(isZip ? 'Procesando ZIP…' : 'Leyendo plantilla e imágenes…');

                try {
                    var fd = new FormData();
                    fd.append('template', file);
                    fd.append('_token', csrf);
                    var wantSave = !!document.getElementById('templateSave')?.checked;
                    fd.append('save', wantSave ? '1' : '0');
                    var saveName = document.getElementById('templateSaveName')?.value
                        || file.name.replace(/\.(html|htm|zip)$/i, '');
                    fd.append('template_name', saveName);

                    if (!isZip) {
                        var raw = await readFileAsText(file);
                        var html = extractBodyHtml(raw);
                        if (!html) {
                            setStatus('La plantilla está vacía.', true);
                            return;
                        }
                        fd.append('html_body', html);
                        folderAssets.forEach(function (item, idx) {
                            fd.append('assets[]', item.file);
                            fd.append('asset_paths[' + idx + ']', item.path);
                        });
                    }

                    setStatus('Subiendo plantilla' + (folderAssets.length && !isZip ? ' y ' + folderAssets.length + ' imágenes' : '') + '…');
                    var res = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: fd,
                        credentials: 'same-origin'
                    });
                    var parsed = await parseJsonSafe(res);
                    if (!parsed.ok || parsed.data.success === false) {
                        var msg = parsed.data.message;
                        if (parsed.data.errors) {
                            msg = Object.values(parsed.data.errors).flat().join(' ');
                        }
                        setStatus('No se pudo cargar: ' + (msg || ('HTTP ' + parsed.status)), true);
                    } else {
                        if (parsed.data.html) {
                            setBody(parsed.data.html, true);
                        }
                        fillTemplates(parsed.data.templates || [], parsed.data.saved_as || '');
                        var imgN = parsed.data.images || 0;
                        var imgMsg = imgN ? (' Incluyó ' + imgN + ' imagen(es).') : (isZip || folderAssets.length ? '' : ' Sin imágenes: sube la carpeta images/ o un ZIP.');
                        if (parsed.data.saved_as) {
                            setStatus('Guardada: «' + parsed.data.saved_as + '».' + imgMsg);
                        } else {
                            setStatus('Plantilla cargada en el editor.' + imgMsg);
                        }
                    }

                    if (fileInput) fileInput.value = '';
                    if (imageInput) imageInput.value = '';
                    if (folderInput) folderInput.value = '';
                } catch (err) {
                    setStatus('Error: ' + (err && err.message ? err.message : 'no se pudo cargar'), true);
                } finally {
                    uploadBtn.disabled = false;
                }
            });
        }

        savedSelect?.addEventListener('change', function () {
            if (savedSelect.value) {
                loadSavedTemplate(savedSelect.value);
            }
        });

        document.getElementById('templateLoadBtn')?.addEventListener('click', async function (e) {
            e.preventDefault();
            await loadSavedTemplate(savedSelect?.value);
        });

        document.getElementById('templateDeleteBtn')?.addEventListener('click', async function (e) {
            e.preventDefault();
            var slug = savedSelect?.value;
            if (!slug) {
                setStatus('Selecciona una plantilla para eliminar', true);
                return;
            }
            if (!confirm('¿Eliminar la plantilla "' + slug + '"?')) return;
            try {
                var res = await fetch(loadUrlBase.replace(/\/$/, '') + '/' + encodeURIComponent(slug), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                var parsed = await parseJsonSafe(res);
                fillTemplates(parsed.data.templates || [], '');
                setStatus(parsed.data.message || 'Eliminada', !parsed.ok);
            } catch (err) {
                setStatus('No se pudo eliminar', true);
            }
        });

        if (savedSelect && savedSelect.options.length <= 1) {
            savedSelect.disabled = true;
        }
    });
})();
</script>
@endpush
