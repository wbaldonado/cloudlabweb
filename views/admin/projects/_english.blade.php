<hr class="my-4">
<h2 class="h5">English</h2>
<p class="text-muted">Shown when a visitor chooses English. An empty field keeps the Spanish text.</p>

<div class="mb-3">
    <label for="title_en" class="form-label">Title</label>
    <input type="text" class="form-control @error('title_en') is-invalid @enderror" id="title_en" name="title_en" value="{{ old('title_en', $project->title_en ?? '') }}">
    @error('title_en')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description_en" class="form-label">Description</label>
    <textarea class="form-control @error('description_en') is-invalid @enderror" id="description_en" name="description_en" rows="3">{{ old('description_en', $project->description_en ?? '') }}</textarea>
    @error('description_en')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="meta_title_en" class="form-label">SEO title</label>
    <input type="text" class="form-control @error('meta_title_en') is-invalid @enderror" id="meta_title_en" name="meta_title_en" maxlength="70" value="{{ old('meta_title_en', $project->meta_title_en ?? '') }}">
    @error('meta_title_en')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="meta_description_en" class="form-label">Meta description</label>
    <textarea class="form-control @error('meta_description_en') is-invalid @enderror" id="meta_description_en" name="meta_description_en" maxlength="160" rows="2">{{ old('meta_description_en', $project->meta_description_en ?? '') }}</textarea>
    @error('meta_description_en')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="content_en" class="form-label">Content</label>
    <div id="content-en-editor" data-quill-editor data-quill-input="content_en" style="height: 400px;"></div>
    <textarea id="content_en" name="content_en" hidden aria-hidden="true">{{ old('content_en', $project->content_en ?? '') }}</textarea>
    @error('content_en')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="technologies_en" class="form-label">Technologies (comma separated)</label>
    <input type="text" class="form-control @error('technologies_en') is-invalid @enderror" id="technologies_en" name="technologies_en" value="{{ old('technologies_en', $project->technologies_en ?? '') }}" placeholder="Power Apps, Dataverse, Power Automate">
    @error('technologies_en')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
