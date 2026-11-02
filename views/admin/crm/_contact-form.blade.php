<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="first_name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Apellido *</label>
        <input type="text" name="last_name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input type="text" name="phone" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Cargo</label>
        <input type="text" name="job_title" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Estado *</label>
        <select name="lifecycle_stage" class="form-select" required>
            <option value="lead">Nuevo</option>
            <option value="mql">Contactado</option>
            <option value="sql">Calificado</option>
            <option value="customer">Cliente</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Empresa</label>
        <select name="company_id" class="form-select">
            <option value="">Seleccionar empresa...</option>
            @foreach($companiesList as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Responsable</label>
        <select name="owner_id" class="form-select">
            <option value="">Sin asignar</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea name="notes" class="form-control" rows="3"></textarea>
    </div>
</div>
<div id="crmContactFormErrors" class="alert alert-danger mt-3 d-none"></div>
