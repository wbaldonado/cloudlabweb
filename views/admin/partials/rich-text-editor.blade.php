{{--
  Rich-text (Quill) for admin forms.
  Expects: $uploadRoute (named route for image upload)
--}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style nonce="{{ $cspNonce ?? '' }}">
    #content-editor .ql-editor,
    [data-quill-editor] .ql-editor {
        min-height: 320px;
        font-size: 1rem;
        line-height: 1.6;
    }
    #content-editor .ql-editor img,
    [data-quill-editor] .ql-editor img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1em auto;
        border-radius: 8px;
    }
    #content-editor .ql-editor iframe.ql-video,
    [data-quill-editor] .ql-editor iframe.ql-video {
        display: block;
        width: 100%;
        max-width: 720px;
        min-height: 360px;
        margin: 1em auto;
    }
    #content-editor .ql-toolbar,
    [data-quill-editor] .ql-toolbar {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        background: #f8f9fa;
    }
    #content-editor .ql-container,
    [data-quill-editor] .ql-container {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js" nonce="{{ $cspNonce ?? '' }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        function uploadImage(quill) {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = function() {
                const file = input.files[0];
                if (!file) return;

                if (file.size > 8 * 1024 * 1024) {
                    alert('Image size must be less than 8MB');
                    return;
                }

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                const range = quill.getSelection(true);
                quill.insertText(range.index, 'Uploading image...', 'user');

                fetch(@json($uploadRoute), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    quill.deleteText(range.index, 19);
                    if (data.success) {
                        quill.insertEmbed(range.index, 'image', data.url, 'user');
                        quill.insertText(range.index + 1, '\n', 'user');
                        quill.setSelection(range.index + 2);
                    } else {
                        alert('Failed to upload image: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(function() {
                    quill.deleteText(range.index, 19);
                    alert('Failed to upload image. Please try again.');
                });
            };
        }

        function bindEditor(editorEl) {
            var inputId = editorEl.getAttribute('data-quill-input') || 'content';
            var textarea = document.getElementById(inputId);
            var quill = new Quill(editorEl, {
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
                            image: function () {
                                uploadImage(this.quill);
                            }
                        }
                    }
                },
                placeholder: editorEl.getAttribute('data-placeholder') || 'Write rich content here…'
            });

            if (textarea && textarea.value) {
                quill.root.innerHTML = textarea.value;
            }

            function sync() {
                if (textarea) {
                    textarea.value = quill.root.innerHTML;
                }
            }

            quill.on('text-change', sync);

            if (textarea) {
                var form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', sync);
                }
            }
        }

        var legacy = document.getElementById('content-editor');
        if (legacy && !legacy.hasAttribute('data-quill-editor')) {
            legacy.setAttribute('data-quill-editor', '1');
            legacy.setAttribute('data-quill-input', 'content');
        }

        document.querySelectorAll('[data-quill-editor]').forEach(bindEditor);
    });
</script>
@endpush
