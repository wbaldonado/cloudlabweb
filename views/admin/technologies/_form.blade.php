@push('styles')
<style nonce="{{ $cspNonce ?? '' }}">
    .admin-tech-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
    }
    .admin-tech-icon img {
        width: 34px;
        height: 34px;
        object-fit: contain;
        display: block;
    }
    .admin-tech-icon--blue { background: rgba(0, 229, 255, 0.16); }
    .admin-tech-icon--purple,
    .admin-tech-icon--pink { background: rgba(224, 64, 251, 0.14); }
    .admin-tech-icon--purple-light { background: rgba(255, 183, 255, 0.28); }
    .admin-tech-icon--yellow { background: rgba(126, 249, 255, 0.22); }
    .admin-tech-icon--python { background: rgba(255, 218, 75, 0.28); }
    .admin-tech-icon--tableau { background: rgba(232, 118, 45, 0.16); }
</style>
@endpush
@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $colorOptions = $colorOptions ?? [];
    $technology = $technology ?? new \App\Models\Technology();
    $isEdit = !empty($technology->id);
@endphp
@csrf

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $technology->name) }}" class="form-control" required>
    @if($errors->has('name'))
        <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description (optional)</label>
    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Enter a short description">{{ old('description', $technology->description) }}</textarea>
    @if($errors->has('description'))
        <div class="text-danger small mt-1">{{ $errors->first('description') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="color_class" class="form-label">Color Style</label>
    <select name="color_class" id="color_class" class="form-select" required>
        <option value="">Select a color style</option>
        @foreach($colorOptions as $option)
            <option value="{{ $option }}" {{ old('color_class', $technology->color_class) === $option ? 'selected' : '' }}>
                {{ ucwords(str_replace('-', ' ', $option)) }}
            </option>
        @endforeach
    </select>
    @if($errors->has('color_class'))
        <div class="text-danger small mt-1">{{ $errors->first('color_class') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="display_order" class="form-label">Display Order</label>
    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $technology->display_order ?? 0) }}" class="form-control" min="0">
    <div class="form-text">Lower numbers appear first.</div>
    @if($errors->has('display_order'))
        <div class="text-danger small mt-1">{{ $errors->first('display_order') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="icon" class="form-label">Icon {{ $isEdit ? '(upload to replace)' : '' }}</label>
    <input type="file" name="icon" id="icon" class="form-control" {{ $isEdit ? '' : 'required' }} accept=".svg,.png,.jpg,.jpeg,.webp,image/svg+xml,image/png,image/jpeg,image/webp">
    <div class="form-text">SVG, PNG, JPG or WebP (max 2 MB).</div>
    @if($errors->has('icon'))
        <div class="text-danger small mt-1">{{ $errors->first('icon') }}</div>
    @endif
    @if($isEdit && $technology->icon_url)
        <div class="mt-3">
            <span class="d-block mb-2 fw-semibold">Current Icon:</span>
            <span class="admin-tech-icon admin-tech-icon--{{ $technology->color_class }}">
                <img src="{{ $technology->icon_url }}" alt="{{ $technology->name }}" width="34" height="34">
            </span>
        </div>
    @endif
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $technology->exists ? $technology->is_active : true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Technology' }}</button>
<a href="{{ route('admin.technologies.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
