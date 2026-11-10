<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.contact.confirm_subject') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #0a1220; margin: 0; padding: 0; background: #f2fbfc;">
    <div style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <div style="background: #0a1220; color: #ffffff; padding: 18px 22px; border-radius: 8px 8px 0 0;">
            <strong style="font-size: 18px;">CloudLab Latam</strong>
        </div>
        <div style="background: #ffffff; padding: 22px; border: 1px solid #d7eef2; border-top: none; border-radius: 0 0 8px 8px;">
            <p>{{ __('messages.contact.confirm_greeting', ['name' => $data['name']]) }}</p>
            <p>{{ __('messages.contact.confirm_thanks') }}</p>
            <p>{{ __('messages.contact.confirm_received') }}</p>
            <p><strong>{{ __('messages.contact.confirm_details') }}</strong></p>
            <ul>
                <li>{{ __('messages.contact.confirm_subject_label') }}: {{ $data['subject'] }}</li>
                <li>{{ __('messages.contact.confirm_reference_label') }}: {{ $data['reference'] }}</li>
            </ul>
            <p>{{ __('messages.contact.confirm_about') }}</p>
            <p>{{ __('messages.contact.confirm_interest') }}</p>
            <p>{{ __('messages.contact.confirm_closing') }}</p>
            <p>
                <strong>CloudLab Latam</strong><br>
                {{ __('messages.contact.confirm_tagline') }}<br>
                <a href="https://cloudlablatam.tech/" style="color: #0a6d80;">https://cloudlablatam.tech/</a>
            </p>
        </div>
    </div>
</body>
</html>
