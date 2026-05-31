<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogRating;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('is_published', true)
            ->latest()
            ->paginate(9);

        return view('blogs.index', compact('blogs'))
            ->with('pageType', 'blog_list');
    }

    public function show($slug)
    {
        try {
            // Sanitize and validate slug
            $slug = trim(urldecode($slug));
            if (empty($slug)) {
                abort(404, 'Invalid blog post URL');
            }

            // Try to find the blog by slug
            $blog = Blog::where('slug', $slug)
                ->where('is_published', true)
                ->with(['ratings', 'user'])
                ->first();

            // If not found, try to find by ID (in case slug is actually an ID)
            if (!$blog && is_numeric($slug)) {
                $blog = Blog::where('id', $slug)
                    ->where('is_published', true)
                    ->with(['ratings', 'user'])
                    ->first();
            }

            // If still not found, check if blog exists but is not published
            if (!$blog) {
                $unpublishedBlog = Blog::where('slug', $slug)->first();
                if ($unpublishedBlog && !$unpublishedBlog->is_published) {
                    Log::warning('Attempted to access unpublished blog', [
                        'slug' => $slug,
                        'blog_id' => $unpublishedBlog->id,
                        'ip' => request()->ip(),
                    ]);
                    abort(404, 'Blog post not found or not published');
                }
                
                Log::warning('Blog post not found', [
                    'slug' => $slug,
                    'ip' => request()->ip(),
                    'url' => request()->fullUrl(),
                ]);
                abort(404, 'Blog post not found');
            }

            // Load comments separately to avoid eager loading issues
            $blog->load(['comments' => function($query) {
                $query->whereNull('parent_id')
                      ->where('is_approved', true)
                      ->orderBy('created_at', 'desc')
                      ->with('replies');
            }]);

            $recentBlogs = Blog::where('is_published', true)
                ->where('id', '!=', $blog->id)
                ->latest()
                ->take(3)
                ->get();

            // Check if user has already rated (by IP)
            $userIp = request()->ip();
            $hasRated = $blog->ratings()->where('ip_address', $userIp)->exists();

            return view('blogs.show', compact('blog', 'recentBlogs', 'hasRated'))
                ->with('pageType', 'blog_post')
                ->with('pageData', $blog);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Blog post not found (ModelNotFoundException)', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            abort(404, 'Blog post not found');
        } catch (\Exception $e) {
            Log::error('Error loading blog post', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(404, 'Blog post not found');
        }
    }

    public function rate(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'visitor_name' => 'nullable|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
        ]);

        $userIp = request()->ip();

        // Check if already rated from this IP
        $existingRating = BlogRating::where('blog_id', $blog->id)
            ->where('ip_address', $userIp)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $validated['rating'],
                'visitor_name' => $validated['visitor_name'] ?? null,
                'visitor_email' => $validated['visitor_email'] ?? null,
            ]);
        } else {
            // Create new rating
            BlogRating::create([
                'blog_id' => $blog->id,
                'rating' => $validated['rating'],
                'visitor_name' => $validated['visitor_name'] ?? null,
                'visitor_email' => $validated['visitor_email'] ?? null,
                'ip_address' => $userIp,
            ]);
        }

        return redirect()
            ->route('blogs.show', $blog->slug)
            ->with('success', __('messages.blog.rating_success'));
    }

    public function comment(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'required|email|max:255',
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        try {
            $comment = BlogComment::create([
                'blog_id' => $blog->id,
                'parent_id' => $validated['parent_id'] ?? null,
                'visitor_name' => $validated['visitor_name'],
                'visitor_email' => $validated['visitor_email'],
                'comment' => $validated['comment'],
                'is_approved' => true, // Auto-approve comments (you can change this to false for moderation)
                'ip_address' => request()->ip() ?? request()->header('X-Forwarded-For') ?? '0.0.0.0',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating blog comment', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('blogs.show', $blog->slug)
                ->with('error', __('messages.blog.comment_error'))
                ->withInput();
        }

        Log::info('Blog comment submitted', [
            'blog_id' => $blog->id,
            'comment_id' => $comment->id,
            'visitor_email' => $validated['visitor_email'],
        ]);

        return redirect()
            ->route('blogs.show', $blog->slug)
            ->with('success', __('messages.blog.comment_success'));
    }
}
