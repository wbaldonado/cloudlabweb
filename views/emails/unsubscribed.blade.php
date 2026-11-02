<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suscripción cancelada</title>
    <style>
        body { font-family: system-ui, sans-serif; background:#0a1220; color:#e8f4f7; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card { background:#121c2e; border:1px solid rgba(0,229,255,.25); border-radius:16px; padding:2rem; max-width:420px; text-align:center; }
        a { color:#7ef9ff; }
    </style>
</head>
<body>
    <div class="card">
        <h1 style="margin-top:0">Suscripción cancelada</h1>
        <p>Ya no recibirás correos de campañas de {{ config('crm.company_name', config('app.name')) }} en <strong>{{ $email }}</strong>.</p>
        <p><a href="{{ url('/') }}">Volver al sitio</a></p>
    </div>
</body>
</html>
