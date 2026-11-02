@extends('admin.layouts.app')

@section('title', 'CRM — Setup required')

@section('content')
<div class="pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">CRM Dashboard</h1>
</div>

<div class="alert alert-warning">
    <h5 class="alert-heading">CRM database tables are missing</h5>
    <p class="mb-2">The CRM route is working, but these tables were not found:</p>
    <ul class="mb-3">
        @foreach($missingTables as $table)
            <li><code>{{ $table }}</code></li>
        @endforeach
    </ul>
    <p class="mb-2"><strong>Fix in phpMyAdmin:</strong></p>
    <ol class="mb-0">
        @if(!empty($sqlFile))
            <li>Import <code>{{ $sqlFile }}</code></li>
        @else
            <li>Import <code>database/sql/hostinger_schema.sql</code> (full install), or</li>
            <li>Run <code>database/sql/crm_dashboard_update.sql</code> if core CRM tables already exist</li>
        @endif
        <li>Then refresh this page</li>
    </ol>
</div>

<p class="text-muted small">
    If you see a 404 instead of this page, upload <code>routes/web.php</code> and run
    <code>php artisan route:clear</code> on the server.
</p>
@endsection
