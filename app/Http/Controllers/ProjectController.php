<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        try {
            $projects = Project::where('is_published', true)
                ->latest()
                ->paginate(9);
        } catch (\Throwable $e) {
            Log::warning('Project index query failed', ['error' => $e->getMessage()]);
            $projects = new LengthAwarePaginator([], 0, 9, 1, [
                'path' => request()->url(),
            ]);
        }

        return view('projects.index', compact('projects'))
            ->with('pageType', 'project_list');
    }

    public function show($slug)
    {
        $slug = request()->route('slug', $slug);
        $project = Project::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedProjects = Project::where('is_published', true)
            ->where('id', '!=', $project->id)
            ->latest()
            ->take(3)
            ->get();

        return view('projects.show', compact('project', 'relatedProjects'))
            ->with('pageType', 'project_detail')
            ->with('pageData', $project);
    }
}
