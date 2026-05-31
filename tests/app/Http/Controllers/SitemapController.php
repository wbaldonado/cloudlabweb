<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap
     */
    public function index(): Response
    {
        $baseUrl = config('app.url');
        $locales = ['en', 'es']; // Add more locales if needed
        
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $sitemap .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml"' . "\n";
        $sitemap .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' . "\n";
        $sitemap .= ' xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        // Homepage - highest priority
        $sitemap .= $this->generateUrl($baseUrl, '1.0', 'daily', now());
        foreach ($locales as $locale) {
            if ($locale !== app()->getLocale()) {
                $sitemap .= $this->generateUrl($baseUrl, '1.0', 'daily', now(), $locale);
            }
        }

        // Static pages
        $staticPages = [
            ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/technologies', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/documentation', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/blogs', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/projects', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->generateUrl(
                $baseUrl . $page['url'],
                $page['priority'],
                $page['changefreq'],
                now()->subDays(7)
            );
        }

        // Blog posts
        $blogs = Blog::where('is_published', true)->latest()->get();
        foreach ($blogs as $blog) {
            $sitemap .= $this->generateUrl(
                $baseUrl . '/blogs/' . $blog->slug,
                '0.8',
                'weekly',
                $blog->updated_at ?? $blog->created_at,
                null,
                $blog
            );
        }

        // Projects
        $projects = Project::where('is_published', true)->latest()->get();
        foreach ($projects as $project) {
            $sitemap .= $this->generateUrl(
                $baseUrl . '/projects/' . $project->slug,
                '0.8',
                'monthly',
                $project->updated_at ?? $project->created_at,
                null,
                $project,
                'project'
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Generate a single URL entry for sitemap
     */
    private function generateUrl(
        string $url,
        string $priority,
        string $changefreq,
        $lastmod,
        ?string $locale = null,
        $model = null,
        string $type = 'blog'
    ): string {
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        
        if ($locale) {
            $baseUrl = config('app.url');
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($locale) . '" href="' . htmlspecialchars($baseUrl) . '" />' . "\n";
        }
        
        if ($lastmod) {
            $xml .= '    <lastmod>' . $lastmod->format('Y-m-d\TH:i:s\Z') . '</lastmod>' . "\n";
        }
        
        $xml .= '    <changefreq>' . htmlspecialchars($changefreq) . '</changefreq>' . "\n";
        $xml .= '    <priority>' . htmlspecialchars($priority) . '</priority>' . "\n";
        
        // Add image if available
        if ($model) {
            $imagePath = null;
            if ($type === 'blog' && isset($model->featured_image)) {
                $imagePath = $model->featured_image;
            } elseif ($type === 'project' && isset($model->image)) {
                $imagePath = $model->image;
            }
            
            if ($imagePath) {
                $baseUrl = config('app.url');
                $imageUrl = $baseUrl . '/storage/' . $imagePath;
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . htmlspecialchars($imageUrl) . '</image:loc>' . "\n";
                $xml .= '      <image:title>' . htmlspecialchars($model->title ?? '') . '</image:title>' . "\n";
                $caption = $model->description ?? $model->excerpt ?? '';
                if ($caption) {
                    $xml .= '      <image:caption>' . htmlspecialchars(strip_tags($caption)) . '</image:caption>' . "\n";
                }
                $xml .= '    </image:image>' . "\n";
            }
        }
        
        $xml .= '  </url>' . "\n";
        return $xml;
    }
}
