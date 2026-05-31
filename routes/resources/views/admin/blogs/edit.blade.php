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
        <label for="content" class="form-label">Content *</label>
        <div id="content-editor" style="height: 400px;"></div>
        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required style="display: none;">{{ old('content', $blog->content) }}</textarea>
        @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
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

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Improve Quill editor styles for lists and images */
    #content-editor .ql-editor {
        min-height: 400px;
        font-size: 16px;
        line-height: 1.6;
    }
    
    /* Better list styling - support for individual line bullets */
    #content-editor .ql-editor ul,
    #content-editor .ql-editor ol {
        padding-left: 1.5em;
        margin: 0.5em 0;
    }
    
    #content-editor .ql-editor li {
        margin: 0.25em 0;
        line-height: 1.6;
        padding-left: 0.25em;
    }
    
    /* Allow individual line bullets - single item lists */
    #content-editor .ql-editor ul > li:only-child,
    #content-editor .ql-editor ol > li:only-child {
        margin: 0.5em 0;
    }
    
    /* Allow individual line bullets */
    #content-editor .ql-editor p {
        margin: 0.5em 0;
    }
    
    /* Support for mixed content - paragraphs and lists */
    #content-editor .ql-editor p + ul,
    #content-editor .ql-editor p + ol,
    #content-editor .ql-editor ul + p,
    #content-editor .ql-editor ol + p {
        margin-top: 0.5em;
    }
    
    /* Style for images within content */
    #content-editor .ql-editor img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1em auto;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Allow images to be inline or block */
    #content-editor .ql-editor .ql-image {
        display: inline-block;
        max-width: 100%;
        vertical-align: middle;
    }
    
    /* Better spacing around images */
    #content-editor .ql-editor p + img,
    #content-editor .ql-editor img + p {
        margin-top: 1em;
    }
    
    /* Improve toolbar visibility */
    #content-editor .ql-toolbar {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        background: #f8f9fa;
    }
    
    #content-editor .ql-container {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Custom image handler for Quill
        function imageHandler() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = function() {
                const file = input.files[0];
                if (file) {
                    // Check file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image size must be less than 2MB');
                        return;
                    }

                    // Create form data
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Show loading indicator
                    const range = quill.getSelection(true);
                    quill.insertText(range.index, 'Uploading image...', 'user');
                    quill.setSelection(range.index + 19);

                    // Upload image
                    fetch('{{ route("admin.blogs.upload-image") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove loading text
                            quill.deleteText(range.index, 19);
                            // Insert image with better formatting
                            quill.insertEmbed(range.index, 'image', data.url, 'user');
                            // Add a line break after image for better text flow
                            quill.insertText(range.index + 1, '\n', 'user');
                            quill.setSelection(range.index + 2);
                        } else {
                            alert('Failed to upload image: ' + (data.message || 'Unknown error'));
                            quill.deleteText(range.index, 19);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to upload image. Please try again.');
                        quill.deleteText(range.index, 19);
                    });
                }
            };
        }

        // Initialize Quill editor with improved configuration
        var quill = new Quill('#content-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['blockquote', 'code-block'],
                        ['clean']
                    ],
                    handlers: {
                        'image': imageHandler,
                        'list': function(value) {
                            // Improved list handler for better bullet support on individual lines
                            if (value === 'bullet' || value === 'ordered') {
                                const range = quill.getSelection(true);
                                if (range) {
                                    const format = quill.getFormat(range);
                                    
                                    if (format.list) {
                                        // If already in a list of the same type, toggle off
                                        if (format.list === value) {
                                            quill.format('list', false);
                                        } else {
                                            // Switch list type
                                            quill.format('list', value);
                                        }
                                    } else {
                                        // Not in a list - create one
                                        // Format the current selection/line as a list
                                        quill.format('list', value);
                                    }
                                }
                            }
                        }
                    }
                },
                // Improve list behavior
                keyboard: {
                    bindings: {
                        // Allow Enter to create new list items
                        list: {
                            key: 'Enter',
                            handler: function(range, context) {
                                const format = quill.getFormat(range);
                                if (format.list) {
                                    // Allow Enter to create new list item
                                    return true;
                                }
                                return true;
                            }
                        }
                    }
                }
            },
            placeholder: 'Write your blog content here...'
        });

        // Get the textarea element
        var textarea = document.querySelector('#content');
        
        // Set initial content if exists
        if (textarea.value) {
            quill.root.innerHTML = textarea.value;
        }

        // Add custom keyboard shortcuts for better list handling
        quill.keyboard.addBinding({
            key: 'Enter',
            handler: function(range, context) {
                const format = quill.getFormat(range);
                if (format.list) {
                    // If in a list, Enter creates new list item
                    // Shift+Enter exits list
                    if (context.event.shiftKey) {
                        quill.format('list', false);
                        quill.insertText(range.index, '\n', 'user');
                        return false;
                    }
                }
                return true;
            }
        });

        // Improve list toggle behavior
        quill.on('selection-change', function(range) {
            if (range) {
                const format = quill.getFormat(range);
                // Update toolbar button state based on current format
                const listButtons = document.querySelectorAll('.ql-list');
                listButtons.forEach(button => {
                    if (format.list) {
                        button.classList.add('ql-active');
                    } else {
                        button.classList.remove('ql-active');
                    }
                });
            }
        });

        // Update textarea on text change
        quill.on('text-change', function() {
            textarea.value = quill.root.innerHTML;
        });

        // Ensure content is synced before form submission
        var form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                textarea.value = quill.root.innerHTML;
            });
        }
        
        // Add helper function to insert bullet on current line
        window.insertBulletAtLine = function() {
            const range = quill.getSelection(true);
            if (range) {
                const format = quill.getFormat(range);
                if (!format.list) {
                    quill.format('list', 'bullet');
                }
            }
        };
    });
</script>
@endpush
@endsection

