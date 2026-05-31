<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('is_published', true)
            ->latest()
            ->paginate(9);

        return view('projects.index', compact('projects'))
            ->with('pageType', 'project_list');
    }

    public function show($slug)
    {
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
