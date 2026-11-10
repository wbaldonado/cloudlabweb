@if($siteKey = config('services.turnstile.site_key'))
    <div class="cl-turnstile mb-3">
        <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="light" data-language="{{ app()->getLocale() }}"></div>
    </div>
@endif
