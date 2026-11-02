@extends('admin.layouts.app')

@section('title', 'Edit Blog - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Blog</h1>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Back to Blogs</a>
</div>

<form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="title" class="form-label">Title *</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $blog->title) }}" required>
        @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="excerpt" class="form-label">Excerpt</label>
        <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $blog->excerpt) }}</textarea>
        @error('excerpt')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="meta_title" class="form-label">SEO title</label>
        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" maxlength="70" value="{{ old('meta_title', $blog->meta_title) }}">
        <div class="form-text">Optional. Replaces the article title in search results. Aim for about 60 characters.</div>
        @error('meta_title')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="meta_description" class="form-label">Meta description</label>
        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" maxlength="160" rows="2">{{ old('meta_description', $blog->meta_description) }}</textarea>
        <div class="form-text">Optional. 150 to 160 characters, ending with a call to action.</div>
        @error('meta_description')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="content" class="form-label">Content *</label>
        <div id="content-editor" style="height: 400px;"></div>
        <textarea id="content" name="content" required hidden aria-hidden="true">{{ old('content', $blog->content) }}</textarea>
        <small class="form-text text-muted">Rich text: formatting, images, and YouTube/Vimeo videos.</small>
        @error('content')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="featured_image" class="form-label">Featured Image</label>
        @if($blog->featured_image)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="Current image" style="max-width: 200px;">
        </div>
        @endif
        <input type="file" class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" accept="image/*">
        <div class="form-text">Large images are auto-resized to max 1200px wide. JPEG/PNG/WebP recommended.</div>
        @error('featured_image')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_published">Publish</label>
    </div>

    <button type="submit" class="btn btn-primary">Update Blog</button>
</form>

@include('admin.partials.rich-text-editor', ['uploadRoute' => route('admin.blogs.upload-image')])
@endsection
