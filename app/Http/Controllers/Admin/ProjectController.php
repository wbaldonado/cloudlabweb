<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\Branding;
use App\Support\PublicMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('user')->latest()->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_title_en' => 'nullable|string|max:70',
            'meta_description_en' => 'nullable|string|max:160',
            'content' => 'nullable|string',
            'content_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'nullable|string',
            'technologies_en' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');
        if (! Schema::hasColumn('projects', 'meta_title')) {
            unset($validated['meta_title'], $validated['meta_description']);
        }
        $validated = $this->onlyExistingTranslationColumns($validated);

        if ($request->hasFile('image')) {
            $validated['image'] = PublicMedia::storeImage(
                $request->file('image'),
                'projects',
                PublicMedia::blogImageResizeOptions()
            );
        }

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_title_en' => 'nullable|string|max:70',
            'meta_description_en' => 'nullable|string|max:160',
            'content' => 'nullable|string',
            'content_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'nullable|string',
            'technologies_en' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');
        if (! Schema::hasColumn('projects', 'meta_title')) {
            unset($validated['meta_title'], $validated['meta_description']);
        }
        $validated = $this->onlyExistingTranslationColumns($validated);

        if ($request->hasFile('image')) {
            PublicMedia::delete($project->image);
            $validated['image'] = PublicMedia::storeImage(
                $request->file('image'),
                'projects',
                PublicMedia::blogImageResizeOptions()
            );
        }

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        PublicMedia::delete($project->image);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ]);

        if ($request->hasFile('image')) {
            $path = PublicMedia::storeImage(
                $request->file('image'),
                'projects/content',
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

    private function onlyExistingTranslationColumns(array $validated): array
    {
        foreach ([
            'title_en',
            'description_en',
            'content_en',
            'technologies_en',
            'meta_title_en',
            'meta_description_en',
        ] as $column) {
            if (! Schema::hasColumn('projects', $column)) {
                unset($validated[$column]);
            }
        }

        return $validated;
    }
}
