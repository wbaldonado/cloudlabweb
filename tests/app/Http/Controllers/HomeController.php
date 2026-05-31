<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProjects = Project::where('is_published', true)
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $recentBlogs = Blog::where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $technologies = Technology::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('home', compact('featuredProjects', 'recentBlogs', 'technologies'))
            ->with('pageType', 'home');
    }
}
