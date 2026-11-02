<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p><strong>Nueva respuesta en el CRM</strong></p>
    <p>
        <strong>Contacto:</strong> {{ $contact->full_name }}<br>
        <strong>Email:</strong> {{ $inbound->from_email }}<br>
        <strong>Asunto:</strong> {{ $inbound->subject }}
    </p>
    <hr>
    <div>{!! nl2br(e($inbound->body)) !!}</div>
    <p style="margin-top: 24px;">
        <a href="{{ route('admin.crm.dashboard') }}">Abrir CRM Dashboard</a>
    </p>
</body>
</html>
