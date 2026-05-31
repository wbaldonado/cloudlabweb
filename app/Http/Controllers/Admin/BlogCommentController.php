<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function index()
    {
        $comments = BlogComment::with(['blog:id,title,slug'])
            ->latest()
            ->paginate(20);

        return view('admin.blog-comments.index', compact('comments'));
    }

    public function approve(BlogComment $blogComment): RedirectResponse
    {
        $blogComment->update(['is_approved' => true]);

        return back()->with('success', 'Comment approved.');
    }

    public function destroy(BlogComment $blogComment): RedirectResponse
    {
        $blogComment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
