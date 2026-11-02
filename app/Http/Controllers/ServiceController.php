<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Schema;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index', [
            'services' => config('seo_services.pages'),
            'pageType' => 'services_index',
        ]);
    }

    public function show(string $slug)
    {
        $slug = (string) request()->route('slug', $slug);
        $service = collect(config('seo_services.pages'))->firstWhere('slug', $slug);
        abort_unless($service, 404);

        $locale = app()->getLocale() === 'en' ? 'en' : 'es';
        $copy = $service[$locale];

        return view('services.show', [
            'service' => $service,
            'copy' => $copy,
            'relatedProject' => $this->relatedProject($service['keywords'] ?? []),
            'pageType' => 'service',
            'pageData' => array_merge($copy, [
                'slug' => $service['slug'],
                'faqs' => $copy['faqs'] ?? [],
            ]),
            'breadcrumbs' => [
                ['name' => $locale === 'es' ? 'Inicio' : 'Home', 'url' => route('home')],
                ['name' => $locale === 'es' ? 'Servicios' : 'Services', 'url' => route('services.index')],
                ['name' => $copy['nav'], 'url' => route('services.show', ['slug' => $service['slug']])],
            ],
        ]);
    }

    private function relatedProject(array $keywords): ?Project
    {
        if ($keywords === []) {
            return null;
        }

        try {
            if (! Schema::hasTable('projects')) {
                return null;
            }

            return Project::query()
                ->where('is_published', true)
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $like = '%' . $keyword . '%';
                        $query->orWhere('title', 'like', $like)
                            ->orWhere('description', 'like', $like)
                            ->orWhere('technologies', 'like', $like)
                            ->orWhere('content', 'like', $like);
                    }
                })
                ->latest()
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
