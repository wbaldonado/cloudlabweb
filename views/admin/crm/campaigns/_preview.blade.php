@php
    $frameId = $frameId ?? 'campaignPreviewFrame';
    $previewHtml = $previewHtml ?? '';
@endphp
<div class="card shadow-sm campaign-studio-preview">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <strong>Vista previa del correo</strong>
        <div class="btn-group btn-group-sm" role="group" aria-label="Tamaño de vista previa">
            <button type="button" class="btn btn-outline-secondary active" data-preview-size="desktop" data-preview-target="{{ $frameId }}">Escritorio</button>
            <button type="button" class="btn btn-outline-secondary" data-preview-size="mobile" data-preview-target="{{ $frameId }}">Móvil</button>
        </div>
    </div>
    <div class="card-body p-2 p-md-3 bg-light">
        <iframe id="{{ $frameId }}" class="campaign-preview-frame" title="Vista previa de campaña"
                sandbox="allow-popups allow-popups-to-escape-sandbox allow-presentation"></iframe>
    </div>
</div>
@if($previewHtml !== '')
<script type="application/json" data-campaign-preview="{{ $frameId }}" nonce="{{ $cspNonce ?? '' }}">@json($previewHtml)</script>
@endif
