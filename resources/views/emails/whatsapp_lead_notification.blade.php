<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lead WhatsApp</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
    <h2 style="margin-bottom: 0.5rem;">Nuevo lead desde WhatsApp</h2>
    <p style="color: #555;">Se capturó una solicitud automática desde el agente de WhatsApp.</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr>
            <td style="border-bottom: 1px solid #eee; font-weight: bold; width: 140px;">Empresa</td>
            <td style="border-bottom: 1px solid #eee;">{{ $data['company'] ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #eee; font-weight: bold;">Email</td>
            <td style="border-bottom: 1px solid #eee;">{{ $data['email'] ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #eee; font-weight: bold;">WhatsApp</td>
            <td style="border-bottom: 1px solid #eee;">{{ $data['phone'] ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #eee; font-weight: bold; vertical-align: top;">Solicitud</td>
            <td style="border-bottom: 1px solid #eee; white-space: pre-wrap;">{{ $data['request'] ?? '—' }}</td>
        </tr>
    </table>

    <p style="margin-top: 1.25rem;">
        <a href="{{ url('/admin/crm') }}">Abrir CRM Dashboard</a>
    </p>
</body>
</html>
