@extends('admin.layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Blog Comments - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Blog Comments</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Date</th>
                <th>Blog</th>
                <th>Author</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comments as $comment)
            <tr>
                <td>{{ $comment->created_at->format('M d, Y H:i') }}</td>
                <td>
                    @if($comment->blog)
                        <a href="{{ route('blogs.show', $comment->blog->slug) }}" target="_blank" rel="noopener">{{ Str::limit($comment->blog->title, 40) }}</a>
                    @else
                        —
                    @endif
                </td>
                <td>
                    {{ $comment->visitor_name }}<br>
                    <small class="text-muted">{{ $comment->visitor_email }}</small>
                </td>
                <td>{{ Str::limit($comment->comment, 120) }}</td>
                <td>
                    @if($comment->is_approved)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>
                <td class="text-nowrap">
                    @unless($comment->is_approved)
                    <form action="{{ route('admin.blog-comments.approve', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    @endunless
                    <form action="{{ route('admin.blog-comments.destroy', $comment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this comment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No comments yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $comments->links() }}
@endsection
