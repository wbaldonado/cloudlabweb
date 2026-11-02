<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Support\Branding;
use App\Support\PublicMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('user')->latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');
        $validated = $this->onlyExistingSeoColumns($validated);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = PublicMedia::storeImage(
                $request->file('featured_image'),
                'blogs',
                PublicMedia::blogImageResizeOptions()
            );
        }

        $blog = Blog::create($validated);

        // Ensure slug is set (production safeguard if model events did not run)
        if (empty(trim((string) $blog->slug))) {
            $blog->update(['slug' => 'post-' . $blog->id . '-' . uniqid()]);
        }

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function show(Blog $blog)
    {
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');
        $validated = $this->onlyExistingSeoColumns($validated);

        if ($request->hasFile('featured_image')) {
            PublicMedia::delete($blog->featured_image);
            $validated['featured_image'] = PublicMedia::storeImage(
                $request->file('featured_image'),
                'blogs',
                PublicMedia::blogImageResizeOptions()
            );
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        PublicMedia::delete($blog->featured_image);
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ]);

        if ($request->hasFile('image')) {
            $path = PublicMedia::storeImage(
                $request->file('image'),
                'blogs/content',
                PublicMedia::blogImageResizeOptions()
            );
            $url = Branding::assetUrl($path);

            return response()->json([
                'success' => true,
                'url' => $url,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file provided.',
        ], 400);
    }

    private function onlyExistingSeoColumns(array $validated): array
    {
        if (! Schema::hasColumn('blogs', 'meta_title')) {
            unset($validated['meta_title'], $validated['meta_description']);
        }

        return $validated;
    }
}
