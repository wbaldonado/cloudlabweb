@extends('admin.layouts.app')

@section('title', 'Edit Project - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Project</h1>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Back to Projects</a>
</div>

<form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="title" class="form-label">Title (Spanish) *</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $project->title) }}" required>
        @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description (Spanish)</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
        @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="meta_title" class="form-label">SEO title</label>
        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" maxlength="70" value="{{ old('meta_title', $project->meta_title) }}">
        <div class="form-text">Optional. Replaces the project title in search results.</div>
        @error('meta_title')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="meta_description" class="form-label">Meta description</label>
        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" maxlength="160" rows="2">{{ old('meta_description', $project->meta_description) }}</textarea>
        <div class="form-text">Optional. 150 to 160 characters.</div>
        @error('meta_description')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="content" class="form-label">Content (Spanish)</label>
        <div id="content-editor" data-quill-editor data-quill-input="content" style="height: 400px;"></div>
        <textarea id="content" name="content" hidden aria-hidden="true">{{ old('content', $project->content) }}</textarea>
        <small class="form-text text-muted">Rich text: formatting, images, and YouTube/Vimeo videos.</small>
        @error('content')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    @include('admin.projects._english')

    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        @if($project->image_url)
        <div class="mb-2">
            <img src="{{ $project->image_url }}" alt="Current image" style="max-width: 200px;">
        </div>
        @endif
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
        @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="url" class="form-label">Project URL</label>
        <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $project->url) }}">
        @error('url')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="github_url" class="form-label">GitHub URL</label>
        <input type="url" class="form-control @error('github_url') is-invalid @enderror" id="github_url" name="github_url" value="{{ old('github_url', $project->github_url) }}">
        @error('github_url')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="technologies" class="form-label">Technologies, Spanish (comma separated)</label>
        <input type="text" class="form-control @error('technologies') is-invalid @enderror" id="technologies" name="technologies" value="{{ old('technologies', $project->technologies) }}" placeholder="PHP, Laravel, MySQL">
        @error('technologies')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_featured">Featured</label>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published', $project->is_published) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_published">Publish</label>
    </div>

    <button type="submit" class="btn btn-primary">Update Project</button>
</form>

@include('admin.partials.rich-text-editor', ['uploadRoute' => route('admin.projects.upload-image')])
@endsection
