<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate robots.txt dynamically
     */
    public function index(): Response
    {
        $baseUrl = config('app.url');
        
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n\n";
        $robots .= "# Sitemap\n";
        $robots .= "Sitemap: {$baseUrl}/sitemap.xml\n";
        
        return response($robots, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
