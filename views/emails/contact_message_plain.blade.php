{{ __('messages.contact.title') }}

{{ __('messages.contact.name') }}: {{ $data['name'] }}
{{ __('messages.contact.email') }}: {{ $data['email'] }}

@if(!empty($data['company']))
{{ __('messages.contact.company') }}: {{ $data['company'] }}
@endif

@if(!empty($data['subject']))
{{ __('messages.contact.subject') }}: {{ $data['subject'] }}
@endif

@if(!empty($data['reference']))
{{ __('messages.contact.confirm_reference_label') }}: {{ $data['reference'] }}
@endif

{{ __('messages.contact.message') }}:

{{ $data['message'] }}

---
{{ config('app.name') }}

