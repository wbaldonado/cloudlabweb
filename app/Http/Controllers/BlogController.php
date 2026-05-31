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

    /**
     * Show blog by ID (production-safe; use this for "Read more" links).
     */
    public function showById($id)
    {
        $blog = Blog::where('id', (int) $id)->where('is_published', true)->first();
        if (!$blog) {
            abort(404, 'Blog post not found');
        }
        return $this->showBlog($blog);
    }

    public function show($slug)
    {
        try {
            $rawSlug = is_string($slug) ? trim($slug) : '';
            if ($rawSlug === '') {
                abort(404, 'Blog post not found');
            }

            $blog = null;

            // 1) If numeric, try by ID first (reliable in production)
            if (is_numeric($rawSlug)) {
                $blog = Blog::where('id', (int) $rawSlug)->where('is_published', true)->first();
            }

            // 2) Try exact slug match (raw and cleaned)
            if (!$blog) {
                $blog = Blog::where('is_published', true)
                    ->where(function ($q) use ($rawSlug) {
                        $q->where('slug', $rawSlug)
                          ->orWhereRaw('LOWER(slug) = ?', [strtolower($rawSlug)]);
                    })
                    ->first();
            }
            if (!$blog) {
                $cleanedSlug = Blog::cleanSlug($rawSlug);
                if ($cleanedSlug !== '') {
                    $blog = Blog::where('is_published', true)
                        ->where(function ($q) use ($cleanedSlug) {
                            $q->where('slug', $cleanedSlug)
                              ->orWhereRaw('LOWER(slug) = ?', [strtolower($cleanedSlug)]);
                        })
                        ->first();
                }
            }

            // 3) Last resort: find any by slug then check published
            if (!$blog) {
                $blog = Blog::where('slug', $rawSlug)->orWhereRaw('LOWER(slug) = ?', [strtolower($rawSlug)])->first();
            }

            if (!$blog) {
                Log::warning('Blog post not found', [
                    'requested_slug' => $rawSlug,
                    'ip' => request()->ip(),
                ]);
                abort(404, 'Blog post not found');
            }

            if (!$blog->is_published) {
                Log::warning('Attempted to access unpublished blog', [
                    'slug' => $blog->slug,
                    'blog_id' => $blog->id,
                    'ip' => request()->ip(),
                ]);
                abort(404, 'Blog post not found or not published');
            }

            return $this->showBlog($blog);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Blog post not found (ModelNotFoundException)', [
                'url' => request()->fullUrl(),
                'error' => $e->getMessage(),
            ]);
            abort(404, 'Blog post not found');
        } catch (\Exception $e) {
            Log::error('Error loading blog post', [
                'requested_slug' => $rawSlug ?? null,
                'error' => $e->getMessage(),
            ]);
            abort(404, 'Blog post not found');
        }
    }

    /**
     * Shared logic: load relationships and return blog show view.
     */
    private function showBlog(Blog $blog)
    {
        $blog->load(['ratings', 'user']);
        $blog->load(['comments' => function ($query) {
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

        $userIp = request()->ip();
        $hasRated = $blog->ratings()->where('ip_address', $userIp)->exists();

        return view('blogs.show', compact('blog', 'recentBlogs', 'hasRated'))
            ->with('pageType', 'blog_post')
            ->with('pageData', $blog);
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

        return redirect()->route('blogs.show', $blog->slug)->with('success', __('messages.blog.rating_success'));
    }

    public function comment(Request $request, $slug)
    {
        if ($request->filled('website')) {
            return redirect()->back();
        }

        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'required|email:rfc|max:255',
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = BlogComment::where('id', $parentId)
                ->where('blog_id', $blog->id)
                ->first();
            if (!$parent) {
                return redirect()->route('blogs.show', $blog->slug)
                    ->with('error', __('messages.blog.comment_error'))
                    ->withInput();
            }
        }

        $cleanComment = strip_tags($validated['comment']);

        try {
            $comment = BlogComment::create([
                'blog_id' => $blog->id,
                'parent_id' => $parentId,
                'visitor_name' => strip_tags($validated['visitor_name']),
                'visitor_email' => $validated['visitor_email'],
                'comment' => $cleanComment,
                'is_approved' => false,
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating blog comment', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('blogs.show', $blog->slug)->with('error', __('messages.blog.comment_error'))->withInput();
        }

        Log::info('Blog comment submitted (pending approval)', [
            'blog_id' => $blog->id,
            'comment_id' => $comment->id,
        ]);

        return redirect()->route('blogs.show', $blog->slug)->with('success', __('messages.blog.comment_pending'));
    }
}
