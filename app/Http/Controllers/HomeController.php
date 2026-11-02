<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProjects = collect();
        $recentBlogs = collect();
        $technologies = collect();

        try {
            if (Schema::hasTable('projects')) {
                $featuredProjects = Project::query()
                    ->where('is_published', true)
                    ->where('is_featured', true)
                    ->latest()
                    ->take(6)
                    ->get();
            }
        } catch (\Throwable $e) {
            Log::warning('Home projects query failed', ['error' => $e->getMessage()]);
        }

        try {
            if (Schema::hasTable('blogs')) {
                $recentBlogs = Blog::query()
                    ->where('is_published', true)
                    ->latest()
                    ->take(3)
                    ->get();
            }
        } catch (\Throwable $e) {
            Log::warning('Home blogs query failed', ['error' => $e->getMessage()]);
        }

        try {
            if (Schema::hasTable('technologies')) {
                $techQuery = Technology::query()->where('is_active', true);
                if (Schema::hasColumn('technologies', 'display_order')) {
                    $techQuery->orderBy('display_order');
                }
                $technologies = $techQuery->orderBy('name')->get();
            }
        } catch (\Throwable $e) {
            Log::warning('Home technologies query failed', ['error' => $e->getMessage()]);
        }

        $data = [
            'featuredProjects' => $featuredProjects,
            'recentBlogs' => $recentBlogs,
            'technologies' => $technologies,
            'pageType' => 'home',
        ];

        try {
            return response(View::make('home', $data)->render());
        } catch (\Throwable $e) {
            Log::error('Home view render failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Prefer full home without tech icons / optional includes if main render fails.
            try {
                $data['technologies'] = collect();
                $data['homeRenderError'] = $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();

                return response(View::make('home', $data)->render());
            } catch (\Throwable $e2) {
                Log::error('Home view render failed again', ['message' => $e2->getMessage()]);
            }

            if (View::exists('home-fallback')) {
                try {
                    return response(View::make('home-fallback', [
                        'pageType' => 'home',
                        'errorHint' => $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine(),
                    ])->render());
                } catch (\Throwable $e3) {
                    Log::error('Home fallback failed', ['message' => $e3->getMessage()]);
                }
            }

            return response(
                '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>CloudLab Latam</title></head><body style="font-family:system-ui;max-width:800px;margin:2rem auto;padding:1rem;"><h1>CloudLab Latam</h1><p>Home temporarily unavailable.</p><pre style="white-space:pre-wrap;background:#f5f5f5;padding:1rem;">'
                . e($e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine())
                . '</pre><p><a href="/about">About</a> · <a href="/contact">Contact</a></p></body></html>',
                200
            )->header('Content-Type', 'text/html; charset=UTF-8');
        }
    }
}
