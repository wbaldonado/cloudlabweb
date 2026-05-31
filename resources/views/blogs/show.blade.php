@extends('layouts.app')

@section('title', $blog->title . ' - CloudLab Latam')

@section('content')
@php
    $placeholderImageUrl = asset('images/placeholder-blog.jpg');
@endphp
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <article class="scroll-animate fade-in">
                @if($blog->featured_image_url)
                    <img src="{{ $blog->featured_image_url }}" 
                         class="img-fluid mb-4 scroll-animate slide-up stagger-1" 
                         alt="{{ $blog->title }}" 
                         loading="lazy"
                         style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                         onerror="this.onerror=null; this.src='{{ $placeholderImageUrl }}';">
                @endif
                
                <h1 class="scroll-animate slide-up stagger-2">{{ $blog->title }}</h1>
                <p class="text-muted">
                    {{ __('messages.blog.published') }} {{ $blog->created_at->format('F d, Y') }}
                    @if($blog->user)
                    {{ __('messages.blog.by') }} {{ $blog->user->name }}
                    @endif
                </p>

                {{-- Rating Display --}}
                @if($blog->rating_count > 0)
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">{{ __('messages.blog.average_rating') }}:</span>
                        <div class="rating-display">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($blog->average_rating))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-muted"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-muted">({{ number_format($blog->average_rating, 1) }})</span>
                        <span class="text-muted small">{{ $blog->rating_count }} {{ __('messages.blog.total_ratings') }}</span>
                    </div>
                </div>
                @endif
                
                <div class="blog-content">
                    {!! $blog->content !!}
                </div>
                
                {{-- Fix images in content if they're broken --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Fix images in blog content
                        const blogContent = document.querySelector('.blog-content');
                        if (blogContent) {
                            const images = blogContent.querySelectorAll('img');
                            images.forEach(img => {
                                // Add error handler for broken images
                                img.addEventListener('error', function() {
                                    this.style.display = 'none';
                                });
                                
                                // Ensure images are responsive
                                if (!img.hasAttribute('style') || !img.style.maxWidth) {
                                    img.style.maxWidth = '100%';
                                    img.style.height = 'auto';
                                }
                                
                                // Add loading attribute if not present
                                if (!img.hasAttribute('loading')) {
                                    img.setAttribute('loading', 'lazy');
                                }
                            });
                        }
                    });
                </script>

                {{-- Rating Form --}}
                <div class="card mt-4 mb-4 scroll-animate fade-in stagger-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.blog.rating') }}</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success') && str_contains(session('success'), 'rating'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('blogs.rate', $blog->slug) }}" method="POST" id="ratingForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.blog.rating_label') }}</label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                        <label for="rating{{ $i }}" class="star-label">
                                            <i class="bi bi-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" name="visitor_name" class="form-control" placeholder="{{ __('messages.blog.comment_name') }}" value="{{ old('visitor_name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" name="visitor_email" class="form-control" placeholder="{{ __('messages.blog.comment_email') }}" value="{{ old('visitor_email') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-modern">
                                <span>{{ __('messages.blog.rating_submit') }}</span>
                                <i class="bi bi-star-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Comments Section --}}
                <div class="card mt-4 scroll-animate fade-in stagger-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.blog.comments') }} ({{ $blog->comments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success') && str_contains(session('success'), 'comment'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- Comment Form --}}
                        <div class="mb-4">
                            <h6>{{ __('messages.blog.comment_title') }}</h6>
                            <form action="{{ route('blogs.comment', $blog->slug) }}" method="POST">
                                @csrf
                                <div class="visually-hidden" aria-hidden="true" style="position:absolute;left:-9999px;height:0;overflow:hidden;">
                                    <input type="text" name="website" tabindex="-1" autocomplete="off" value="">
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <input type="text" name="visitor_name" class="form-control @error('visitor_name') is-invalid @enderror" 
                                               placeholder="{{ __('messages.blog.comment_name') }}" 
                                               value="{{ old('visitor_name') }}" required>
                                        @error('visitor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="visitor_email" class="form-control @error('visitor_email') is-invalid @enderror" 
                                               placeholder="{{ __('messages.blog.comment_email') }}" 
                                               value="{{ old('visitor_email') }}" required>
                                        @error('visitor_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" 
                                              rows="4" 
                                              placeholder="{{ __('messages.blog.comment_placeholder') }}" 
                                              required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-modern">
                                    <span>{{ __('messages.blog.comment_submit') }}</span>
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        </div>

                        <hr>

                        {{-- Comments List --}}
                        @if($blog->comments->count() > 0)
                            <div class="comments-list">
                                @foreach($blog->comments as $comment)
                                    <div class="comment-item mb-4 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $comment->visitor_name }}</strong>
                                                <small class="text-muted ms-2">{{ $comment->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                        <p class="mb-2">{{ $comment->comment }}</p>
                                        
                                        {{-- Reply Button --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary reply-btn" 
                                                data-comment-id="{{ $comment->id }}" 
                                                data-comment-name="{{ $comment->visitor_name }}"
                                                style="position: relative; z-index: 10; pointer-events: auto; cursor: pointer;">
                                            <i class="bi bi-reply"></i> {{ __('messages.blog.reply') }}
                                        </button>

                                        {{-- Reply Form (hidden by default) --}}
                                        <div class="reply-form mt-3" id="reply-form-{{ $comment->id }}" style="display: none;">
                                            <form action="{{ route('blogs.comment', $blog->slug) }}" method="POST">
                                                @csrf
                                                <div class="visually-hidden" aria-hidden="true" style="position:absolute;left:-9999px;height:0;overflow:hidden;">
                                                    <input type="text" name="website" tabindex="-1" autocomplete="off" value="">
                                                </div>
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <input type="text" name="visitor_name" class="form-control form-control-sm" 
                                                               placeholder="{{ __('messages.blog.comment_name') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="email" name="visitor_email" class="form-control form-control-sm" 
                                                               placeholder="{{ __('messages.blog.comment_email') }}" required>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <textarea name="comment" class="form-control form-control-sm" 
                                                              rows="2" 
                                                              placeholder="{{ __('messages.blog.comment_placeholder') }}" 
                                                              required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <span>{{ __('messages.blog.comment_submit') }}</span>
                                                    <i class="bi bi-send"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary cancel-reply" 
                                                        data-comment-id="{{ $comment->id }}">
                                                    <span>Cancel</span>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Replies --}}
                                        @if($comment->replies->count() > 0)
                                            <div class="replies ms-4 mt-3">
                                                @foreach($comment->replies as $reply)
                                                    <div class="reply-item mb-3 pb-2 border-start ps-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <div>
                                                                <strong>{{ $reply->visitor_name }}</strong>
                                                                <small class="text-muted ms-2">{{ $reply->created_at->format('M d, Y') }}</small>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0">{{ $reply->comment }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{ __('messages.blog.no_comments') }}</p>
                        @endif
                    </div>
                </div>
            </article>
        </div>
        
        <div class="col-md-4 scroll-animate slide-right stagger-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.blog.recent_posts') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentBlogs->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentBlogs as $index => $recentBlog)
                        <a href="{{ url('/blogs/' . rawurlencode($recentBlog->slug ?? $recentBlog->id)) }}" 
                           class="list-group-item list-group-item-action scroll-animate fade-in stagger-{{ min($index + 1, 6) }}"
                           style="text-decoration: none; transition: all 0.3s ease; position: relative; z-index: 10; pointer-events: auto; cursor: pointer; border: none; border-bottom: 1px solid #dee2e6;">
                            <h6 class="mb-1">{{ $recentBlog->title }}</h6>
                            <small class="text-muted">{{ $recentBlog->created_at->format('M d, Y') }}</small>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">No recent posts available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
        /* Ensure all buttons and links are clickable */
        .blog-content a,
        .blog-content button,
        .card a,
        .card button,
        .list-group-item,
        .reply-btn,
        .cancel-reply {
            position: relative;
            z-index: 10;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Ensure blog content lists display correctly */
.blog-content ul,
.blog-content ol {
    margin: 1em 0 !important;
    padding-left: 2em !important;
    list-style-position: outside !important;
    display: block !important;
}

.blog-content ul {
    list-style-type: disc !important;
}

.blog-content ol {
    list-style-type: decimal !important;
}

.blog-content li {
    margin: 0.5em 0 !important;
    line-height: 1.8 !important;
    display: list-item !important;
    list-style-position: outside !important;
}

/* Support for Quill-generated content */
.blog-content ul[class*="ql-"],
.blog-content ol[class*="ql-"] {
    list-style-type: disc !important;
    padding-left: 2em !important;
}

.blog-content ol[class*="ql-"] {
    list-style-type: decimal !important;
}

/* Ensure list markers are visible */
.blog-content li::marker {
    color: var(--primary, #0066FF) !important;
    font-weight: bold !important;
}

/* Nested lists */
.blog-content ul ul,
.blog-content ol ol,
.blog-content ul ol,
.blog-content ol ul {
    margin: 0.5em 0 !important;
    padding-left: 1.5em !important;
}

.blog-content ul ul {
    list-style-type: circle !important;
}

.blog-content ul ul ul {
    list-style-type: square !important;
}

/* Fix for any inline styles that might hide lists */
.blog-content [style*="list-style"] {
    list-style-type: disc !important;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}
.rating-input input[type="radio"] {
    display: none;
}
.rating-input label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    transition: color 0.2s;
}
.rating-input label:hover,
.rating-input label:hover ~ label {
    color: #ffc107;
}
.rating-input input[type="radio"]:checked ~ label,
.rating-input input[type="radio"]:checked ~ label ~ label {
    color: #ffc107;
}
.rating-display {
    display: flex;
    gap: 2px;
}
.rating-display i {
    font-size: 1.2rem;
}
.comment-item {
    padding-bottom: 1rem;
}
.reply-form {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 5px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reply button functionality
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const replyForm = document.getElementById('reply-form-' + commentId);
            replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
        });
    });

    // Cancel reply
    document.querySelectorAll('.cancel-reply').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const replyForm = document.getElementById('reply-form-' + commentId);
            replyForm.style.display = 'none';
            replyForm.querySelector('form').reset();
        });
    });

    // Star rating interaction
    const ratingInputs = document.querySelectorAll('.rating-input input[type="radio"]');
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const value = this.value;
            const labels = this.closest('.rating-input').querySelectorAll('label');
            labels.forEach((label, index) => {
                const labelIndex = 5 - index;
                if (labelIndex <= value) {
                    label.querySelector('i').classList.remove('bi-star');
                    label.querySelector('i').classList.add('bi-star-fill');
                } else {
                    label.querySelector('i').classList.remove('bi-star-fill');
                    label.querySelector('i').classList.add('bi-star');
                }
            });
        });
    });
});
</script>
@endsection
