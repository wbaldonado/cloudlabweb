<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.contact.title') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #1e40af; border-bottom: 2px solid #1e40af; padding-bottom: 10px;">
            {{ __('messages.contact.title') }}
        </h1>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <p><strong>{{ __('messages.contact.name') }}:</strong> {{ $data['name'] }}</p>
            <p><strong>{{ __('messages.contact.email') }}:</strong> {{ $data['email'] }}</p>
            
            @if(!empty($data['company']))
            <p><strong>{{ __('messages.contact.company') }}:</strong> {{ $data['company'] }}</p>
            @endif
            
            @if(!empty($data['subject']))
            <p><strong>{{ __('messages.contact.subject') }}:</strong> {{ $data['subject'] }}</p>
            @endif
        </div>
        
        <div style="margin: 20px 0;">
            <h2 style="color: #1e40af;">{{ __('messages.contact.message') }}:</h2>
            <p style="white-space: pre-wrap;">{{ $data['message'] }}</p>
        </div>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="color: #666; font-size: 12px;">
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>

