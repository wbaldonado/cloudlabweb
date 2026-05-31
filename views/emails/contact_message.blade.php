<x-mail::message>
# {{ __('messages.contact.title') }}

**{{ __('messages.contact.name') }}:** {{ $data['name'] }}

**{{ __('messages.contact.email') }}:** {{ $data['email'] }}

@if(!empty($data['company']))
**{{ __('messages.contact.company') }}:** {{ $data['company'] }}
@endif

@if(!empty($data['subject']))
**{{ __('messages.contact.subject') }}:** {{ $data['subject'] }}
@endif

**{{ __('messages.contact.message') }}:**

{{ $data['message'] }}

---

{{ config('app.name') }}
</x-mail::message>

