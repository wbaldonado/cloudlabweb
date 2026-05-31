@php use Illuminate\Support\Str; @endphp
@csrf

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $technology->name ?? '') }}" class="form-control" required>
    @error('name')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description (optional)</label>
    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Enter a short description">{{ old('description', $technology->description ?? '') }}</textarea>
    @error('description')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="color_class" class="form-label">Color Style</label>
    <select name="color_class" id="color_class" class="form-select" required>
        <option value="">Select a color style</option>
        @foreach($colorOptions as $option)
            <option value="{{ $option }}" {{ old('color_class', $technology->color_class ?? '') === $option ? 'selected' : '' }}>
                {{ Str::title(str_replace('-', ' ', $option)) }}
            </option>
        @endforeach
    </select>
    @error('color_class')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="display_order" class="form-label">Display Order</label>
    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $technology->display_order ?? 0) }}" class="form-control" min="0">
    <div class="form-text">Lower numbers appear first.</div>
    @error('display_order')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="icon" class="form-label">SVG Icon {{ isset($technology) ? '(upload to replace)' : '' }}</label>
    <input type="file" name="icon" id="icon" class="form-control" {{ isset($technology) ? '' : 'required' }} accept="image/svg+xml">
    <div class="form-text">Upload an SVG file (max 2 MB).</div>
    @error('icon')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @isset($technology)
        @if($technology->icon_path)
            @php
                use Illuminate\Support\Facades\Storage;
                $iconUrl = Storage::disk('public')->exists($technology->icon_path) 
                    ? asset('storage/' . $technology->icon_path) 
                    : null;
            @endphp
            <div class="mt-3">
                <span class="d-block mb-2 fw-semibold">Current Icon:</span>
                <div class="border rounded p-3 bg-light" style="max-width: 150px;">
                    @if($iconUrl)
                        <img src="{{ $iconUrl }}" alt="{{ $technology->name }}" class="img-fluid" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span class="text-danger small" style="display: none;">Icon file not found</span>
                    @else
                        <span class="text-danger small">Icon file not found at: {{ $technology->icon_path }}</span>
                    @endif
                </div>
            </div>
        @endif
    @endisset
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $technology->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Technology' }}</button>
<a href="{{ route('admin.technologies.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
