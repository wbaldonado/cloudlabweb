<?php

namespace App\Services;

use Illuminate\Support\Str;

class SEOService
{
    /**
     * Get default meta tags for the site
     */
    public static function getDefaultMeta(): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        
        return [
            'title' => 'CloudLab Latam - Cloud Automation & Digital Transformation',
            'description' => $locale === 'es' 
                ? 'CloudLab Latam: Automatización inteligente en la nube y transformación digital para empresas en América Latina. Potencia la productividad y reduce costos con nuestras soluciones de automatización empresarial.'
                : 'CloudLab Latam: Intelligent cloud automation and digital transformation for businesses in Latin America. Boost productivity and reduce costs with our enterprise automation solutions.',
            'keywords' => $locale === 'es'
                ? 'automatización en la nube, transformación digital, cloud automation, inteligencia artificial, IA, procesos empresariales, América Latina, Latam, eficiencia operacional, trabajo colaborativo'
                : 'cloud automation, digital transformation, artificial intelligence, AI, business processes, Latin America, Latam, operational efficiency, collaborative work',
            'author' => 'CloudLab Latam',
            'og_type' => 'website',
            'og_site_name' => 'CloudLab Latam',
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '@cloudlablatam',
            'twitter_creator' => '@cloudlablatam',
            'revisit_after' => '7 days',
            'rating' => 'general',
            'distribution' => 'global',
            'language' => $locale,
            'geo_region' => 'LATAM',
            'geo_placename' => 'Latin America',
            'copyright' => 'CloudLab Latam',
            'reply_to' => 'info@cloudlablatam.tech',
            'category' => 'Technology, Cloud Computing, Digital Transformation',
        ];
    }

    /**
     * Get meta tags for a specific page
     */
    public static function getPageMeta(string $type, $data = null): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        $default = self::getDefaultMeta();

        switch ($type) {
            case 'home':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'CloudLab Latam - Automatización Inteligente en la Nube'
                        : 'CloudLab Latam - Intelligent Cloud Automation',
                    'canonical' => $baseUrl,
                ]);

            case 'blog_list':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Blog - CloudLab Latam'
                        : 'Blog - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Artículos sobre automatización en la nube, transformación digital, inteligencia artificial y mejores prácticas tecnológicas para empresas.'
                        : 'Articles about cloud automation, digital transformation, artificial intelligence, and technology best practices for businesses.',
                    'canonical' => $baseUrl . '/blogs',
                ]);

            case 'blog_post':
                if (!$data) break;
                $content = strip_tags($data->content ?? '');
                $excerpt = $data->excerpt ?? Str::limit($content, 160);
                return array_merge($default, [
                    'title' => $data->title . ' - CloudLab Latam Blog',
                    'description' => $excerpt,
                    'keywords' => $default['keywords'] . ', ' . Str::limit($data->title, 100),
                    'og_type' => 'article',
                    'og_image' => ($data->featured_image ? $baseUrl . '/storage/' . $data->featured_image : $baseUrl . '/images/logo.png'),
                    'article_published_time' => $data->created_at?->toIso8601String(),
                    'article_modified_time' => $data->updated_at?->toIso8601String(),
                    'article_author' => $data->user?->name ?? 'CloudLab Latam',
                    'article_section' => 'Technology',
                    'article_tag' => 'Cloud Computing, Digital Transformation, Automation',
                    'canonical' => $baseUrl . '/blogs/' . $data->slug,
                    'alternate_lang_es' => $baseUrl . '/blogs/' . $data->slug . '?lang=es',
                    'alternate_lang_en' => $baseUrl . '/blogs/' . $data->slug . '?lang=en',
                ]);

            case 'project_list':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Proyectos - CloudLab Latam'
                        : 'Projects - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Descubre nuestros proyectos de automatización en la nube y transformación digital para empresas líderes en América Latina.'
                        : 'Discover our cloud automation and digital transformation projects for leading companies in Latin America.',
                    'canonical' => $baseUrl . '/projects',
                ]);

            case 'project_detail':
                if (!$data) break;
                $description = $data->description ?: strip_tags(Str::limit($data->content ?? '', 160));
                $technologies = $data->technologies ? ', ' . $data->technologies : '';
                return array_merge($default, [
                    'title' => $data->title . ' - CloudLab Latam',
                    'description' => $description,
                    'keywords' => $default['keywords'] . $technologies,
                    'og_type' => 'article',
                    'og_image' => $data->image ? $baseUrl . '/storage/' . $data->image : $baseUrl . '/images/logo.png',
                    'article_published_time' => $data->created_at?->toIso8601String(),
                    'article_modified_time' => $data->updated_at?->toIso8601String(),
                    'article_section' => 'Projects',
                    'canonical' => $baseUrl . '/projects/' . $data->slug,
                ]);

            case 'about':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Sobre Nosotros - CloudLab Latam'
                        : 'About Us - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Conoce más sobre CloudLab Latam, líderes en automatización en la nube y transformación digital para empresas en América Latina.'
                        : 'Learn more about CloudLab Latam, leaders in cloud automation and digital transformation for businesses in Latin America.',
                    'canonical' => $baseUrl . '/about',
                ]);

            case 'contact':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Contacto - CloudLab Latam'
                        : 'Contact - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Contáctanos para conocer cómo podemos ayudarte con la automatización en la nube y transformación digital de tu empresa.'
                        : 'Contact us to learn how we can help you with cloud automation and digital transformation for your business.',
                    'canonical' => $baseUrl . '/contact',
                ]);

            case 'technologies':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Tecnologías - CloudLab Latam'
                        : 'Technologies - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Las mejores tecnologías para potenciar tu negocio. Descubre las herramientas y plataformas que utilizamos en CloudLab Latam.'
                        : 'The best technologies to power your business. Discover the tools and platforms we use at CloudLab Latam.',
                    'canonical' => $baseUrl . '/technologies',
                ]);

            case 'documentation':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Documentación - CloudLab Latam'
                        : 'Documentation - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Política de seguridad y protección de datos de CloudLab Latam. Comprometidos con la privacidad y protección de información.'
                        : 'CloudLab Latam security and data protection policy. Committed to privacy and information protection.',
                    'canonical' => $baseUrl . '/documentation',
                ]);
        }

        return $default;
    }

    /**
     * Generate Organization schema (JSON-LD)
     */
    public static function generateOrganizationSchema(): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'CloudLab Latam',
            'alternateName' => 'CloudLab Latam',
            'url' => $baseUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $baseUrl . '/images/logo.png',
                'width' => 512,
                'height' => 512,
            ],
            'description' => $locale === 'es'
                ? 'Automatización inteligente en la nube y transformación digital para empresas en América Latina'
                : 'Intelligent cloud automation and digital transformation for businesses in Latin America',
            'foundingDate' => '2024',
            'address' => [
                '@type' => 'PostalAddress',
                'addressRegion' => 'Latin America',
                'addressCountry' => 'LATAM',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => 'info@cloudlablatam.tech',
                'telephone' => '+57-3208156396',
                'contactType' => 'Customer Service',
                'areaServed' => ['LATAM', 'Latin America'],
                'availableLanguage' => ['Spanish', 'English', 'es', 'en'],
            ],
            'sameAs' => [
                // Add your social media profiles here when available
                // 'https://twitter.com/cloudlablatam',
                // 'https://linkedin.com/company/cloudlablatam',
                // 'https://facebook.com/cloudlablatam',
            ],
            'knowsAbout' => [
                'Cloud Computing',
                'Digital Transformation',
                'Automation',
                'Artificial Intelligence',
                'Business Process Optimization',
            ],
        ];
    }

    /**
     * Generate WebSite schema (JSON-LD)
     */
    public static function generateWebSiteSchema(): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'CloudLab Latam',
            'alternateName' => 'CloudLab Latam',
            'url' => $baseUrl,
            'description' => $locale === 'es'
                ? 'Automatización inteligente en la nube y transformación digital para empresas en América Latina'
                : 'Intelligent cloud automation and digital transformation for businesses in Latin America',
            'inLanguage' => $locale === 'es' ? 'es-ES' : 'en-US',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $baseUrl . '/blogs?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate BlogPosting schema (JSON-LD)
     */
    public static function generateBlogPostSchema($blog): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $blog->title,
            'description' => $blog->excerpt ?? strip_tags(Str::limit($blog->content ?? '', 200)),
            'image' => ($blog->featured_image ? $baseUrl . '/storage/' . $blog->featured_image : $baseUrl . '/images/logo.png'),
            'author' => [
                '@type' => $blog->user ? 'Person' : 'Organization',
                'name' => $blog->user?->name ?? 'CloudLab Latam',
                'url' => $baseUrl,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'CloudLab Latam',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $baseUrl . '/images/logo.png',
                    'width' => 512,
                    'height' => 512,
                ],
            ],
            'datePublished' => $blog->created_at?->toIso8601String(),
            'dateModified' => $blog->updated_at?->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $baseUrl . '/blogs/' . $blog->slug,
            ],
            'inLanguage' => $locale === 'es' ? 'es-ES' : 'en-US',
            'wordCount' => str_word_count(strip_tags($blog->content ?? '')),
            'articleSection' => 'Technology',
            'keywords' => 'Cloud Computing, Digital Transformation, Automation, Technology',
        ];
        
        // Add articleBody if content exists
        if ($blog->content) {
            $schema['articleBody'] = strip_tags($blog->content);
        }
        
        return $schema;
    }

    /**
     * Generate Project schema (JSON-LD)
     */
    public static function generateProjectSchema($project): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => $project->description ?? strip_tags(Str::limit($project->content ?? '', 200)),
            'image' => $project->image ? $baseUrl . '/storage/' . $project->image : $baseUrl . '/images/logo.png',
            'creator' => [
                '@type' => 'Organization',
                'name' => 'CloudLab Latam',
                'url' => $baseUrl,
            ],
            'dateCreated' => $project->created_at?->toIso8601String(),
            'dateModified' => $project->updated_at?->toIso8601String(),
            'url' => $baseUrl . '/projects/' . $project->slug,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $baseUrl . '/projects/' . $project->slug,
            ],
            'inLanguage' => $locale === 'es' ? 'es-ES' : 'en-US',
        ];
        
        // Add technologies as keywords if available
        if ($project->technologies) {
            $schema['keywords'] = $project->technologies;
        }
        
        // Add URL if available
        if ($project->url) {
            $schema['codeRepository'] = $project->url;
        }
        
        // Add GitHub URL if available
        if ($project->github_url) {
            $schema['codeRepository'] = $project->github_url;
        }
        
        return $schema;
    }
    
    /**
     * Generate BreadcrumbList schema (JSON-LD)
     */
    public static function generateBreadcrumbSchema(array $items): array
    {
        $baseUrl = config('app.url');
        
        $breadcrumbItems = [];
        foreach ($items as $index => $item) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? $baseUrl,
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];
    }
}
