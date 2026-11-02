<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = \App\Support\LocalizedUrl::origin();
        $locales = config('seo.locales', ['es', 'en']);

        $entries = [
            ['path' => '', 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => now()],
            ['path' => '/servicios', 'priority' => '0.9', 'changefreq' => 'weekly', 'lastmod' => now()],
            ['path' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => now()->subDays(7)],
            ['path' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => now()->subDays(7)],
            ['path' => '/technologies', 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => now()->subDays(7)],
            ['path' => '/documentation', 'priority' => '0.5', 'changefreq' => 'yearly', 'lastmod' => now()->subDays(30)],
            ['path' => '/cookies', 'priority' => '0.3', 'changefreq' => 'yearly', 'lastmod' => now()->subDays(30)],
            ['path' => '/blogs', 'priority' => '0.9', 'changefreq' => 'daily', 'lastmod' => now()],
            ['path' => '/projects', 'priority' => '0.9', 'changefreq' => 'weekly', 'lastmod' => now()],
        ];

        foreach (config('seo_services.pages', []) as $service) {
            $entries[] = [
                'path' => '/servicios/' . $service['slug'],
                'priority' => '0.9',
                'changefreq' => 'monthly',
                'lastmod' => now(),
            ];
        }

        try {
            if (Schema::hasTable('blogs')) {
                foreach (Blog::query()->where('is_published', true)->latest()->get() as $blog) {
                    $entries[] = [
                        'path' => '/blogs/' . $blog->slug,
                        'priority' => '0.8',
                        'changefreq' => 'weekly',
                        'lastmod' => $blog->updated_at ?? $blog->created_at ?? now(),
                        'image' => $blog->featured_image ?? null,
                        'title' => $blog->title,
                        'caption' => $blog->excerpt ?? null,
                    ];
                }
            }

            if (Schema::hasTable('projects')) {
                foreach (Project::query()->where('is_published', true)->latest()->get() as $project) {
                    $entries[] = [
                        'path' => '/projects/' . $project->slug,
                        'priority' => '0.8',
                        'changefreq' => 'monthly',
                        'lastmod' => $project->updated_at ?? $project->created_at ?? now(),
                        'image' => $project->image ?? null,
                        'title' => $project->title,
                        'caption' => $project->description ?? null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Sitemap still lists static pages when the database is unavailable.
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($entries as $entry) {
            foreach ($locales as $locale) {
                $xml .= $this->urlNode($baseUrl, $locale, $entry, $locales);
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    private function urlNode(string $baseUrl, string $locale, array $entry, array $locales): string
    {
        $loc = $baseUrl . '/' . $locale . $entry['path'];
        $xml = "  <url>\n";
        $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";

        foreach ($locales as $alt) {
            $href = $baseUrl . '/' . $alt . $entry['path'];
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . $alt . '" href="' . htmlspecialchars($href, ENT_XML1) . "\" />\n";
        }
        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($baseUrl . '/es' . $entry['path'], ENT_XML1) . "\" />\n";

        if (! empty($entry['lastmod'])) {
            $xml .= '    <lastmod>' . $entry['lastmod']->copy()->utc()->format('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
        }

        $xml .= '    <changefreq>' . htmlspecialchars($entry['changefreq'], ENT_XML1) . "</changefreq>\n";
        $xml .= '    <priority>' . htmlspecialchars($entry['priority'], ENT_XML1) . "</priority>\n";

        if (! empty($entry['image'])) {
            $imagePath = ltrim((string) $entry['image'], '/');
            $imageUrl = str_starts_with($imagePath, 'images/')
                ? $baseUrl . '/' . $imagePath
                : $baseUrl . '/storage/' . $imagePath;
            $xml .= "    <image:image>\n";
            $xml .= '      <image:loc>' . htmlspecialchars($imageUrl, ENT_XML1) . "</image:loc>\n";
            if (! empty($entry['title'])) {
                $xml .= '      <image:title>' . htmlspecialchars($entry['title'], ENT_XML1) . "</image:title>\n";
            }
            if (! empty($entry['caption'])) {
                $xml .= '      <image:caption>' . htmlspecialchars(strip_tags($entry['caption']), ENT_XML1) . "</image:caption>\n";
            }
            $xml .= "    </image:image>\n";
        }

        $xml .= "  </url>\n";

        return $xml;
    }
}
