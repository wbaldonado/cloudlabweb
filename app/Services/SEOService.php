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
            'title' => $locale === 'es'
                ? 'CloudLab Latam | Power Automate, Power BI y Microsoft Fabric'
                : 'CloudLab Latam | Power Automate, Power BI and Microsoft Fabric',
            'description' => $locale === 'es'
                ? 'Automatizamos procesos con Power Automate, analítica en Power BI e ingeniería de datos con Microsoft Fabric para tu empresa. Solicita hoy una consulta gratuita.'
                : 'We automate business processes with Power Automate, analytics in Power BI and data engineering on Microsoft Fabric. Request your free consultation today.',
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
            'language' => $locale === 'es' ? 'es-CO' : 'en',
            'geo_placename' => config('seo.address_locality') . ', ' . config('seo.address_country'),
            'copyright' => 'CloudLab Latam',
            'reply_to' => config('seo.contact_email'),
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
                    'title' => $default['title'],
                    'description' => $default['description'],
                    'canonical' => $baseUrl . '/' . $locale,
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
                $excerpt = trim((string) ($data->meta_description ?? ''));
                if ($excerpt === '') {
                    $excerpt = $data->excerpt ?? Str::limit($content, 160, '');
                }
                $seoTitle = trim((string) ($data->meta_title ?? ''));
                return array_merge($default, [
                    'title' => $seoTitle !== '' ? $seoTitle : ($data->title . ' - CloudLab Latam Blog'),
                    'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($excerpt))), 160, ''),
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
                $description = trim((string) $data->translated('meta_description'));
                if ($description === '') {
                    $description = $data->translated('description') ?: strip_tags(Str::limit($data->translated('content'), 160, ''));
                }
                $technologies = $data->translated('technologies');
                $technologies = $technologies !== '' ? ', ' . $technologies : '';
                $title = trim((string) $data->translated('meta_title'));
                return array_merge($default, [
                    'title' => $title !== ''
                        ? $title
                        : ($data->translated('title') . ' - CloudLab Latam'),
                    'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($description))), 160, ''),
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

            case 'services_index':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Servicios de Power Platform | CloudLab Latam'
                        : 'Power Platform Services | CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Servicios de Power Automate, Power BI, Microsoft Fabric y RPA para automatizar procesos y ordenar tus datos. Pide una consulta gratuita.'
                        : 'Power Automate, Power BI, Microsoft Fabric, and RPA services to automate processes and organize your data. Request a free consultation.',
                ]);

            case 'service':
                if (! $data) {
                    break;
                }
                return array_merge($default, [
                    'title' => $data['meta_title'] ?? $default['title'],
                    'description' => $data['meta_description'] ?? $default['description'],
                    'og_type' => 'website',
                ]);

            case 'cookies':
                return array_merge($default, [
                    'title' => $locale === 'es'
                        ? 'Política de Cookies - CloudLab Latam'
                        : 'Cookie Policy - CloudLab Latam',
                    'description' => $locale === 'es'
                        ? 'Información sobre el uso de cookies en CloudLab Latam y cómo gestionar tus preferencias de consentimiento.'
                        : 'Information about cookie use on CloudLab Latam and how to manage your consent preferences.',
                    'canonical' => rtrim((string) $baseUrl, '/') . '/cookies',
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
            'url' => rtrim((string) $baseUrl, '/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => rtrim((string) $baseUrl, '/') . '/images/logo.png',
                'width' => 512,
                'height' => 512,
            ],
            'description' => $locale === 'es'
                ? 'Automatización de procesos y analítica de datos con Microsoft Power Platform'
                : 'Process automation and data analytics with Microsoft Power Platform',
            'foundingDate' => '2024',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => config('seo.address_locality'),
                'addressCountry' => config('seo.address_country_code'),
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => config('seo.contact_email'),
                'telephone' => config('seo.phone_e164'),
                'contactType' => 'Customer Service',
                'areaServed' => config('seo.address_country_code'),
                'availableLanguage' => ['Spanish', 'English'],
            ],
            'knowsAbout' => [
                'Cloud Computing',
                'Digital Transformation',
                'Automation',
                'Artificial Intelligence',
                'Business Process Optimization',
                'Microsoft Power Platform',
                'Power Automate',
                'Power BI',
                'Microsoft Fabric',
                'RPA',
            ],
        ];
    }

    /**
     * Generate ProfessionalService schema (JSON-LD) for local/regional service identity.
     */
    public static function generateProfessionalServiceSchema(): array
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $locale = app()->getLocale();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'CloudLab Latam',
            'url' => $baseUrl,
            'telephone' => config('seo.phone_e164'),
            'email' => config('seo.contact_email'),
            'image' => $baseUrl . '/images/logo.png',
            'logo' => $baseUrl . '/images/logo.png',
            'description' => $locale === 'es'
                ? 'Automatización de procesos y analítica de datos con Microsoft Power Platform'
                : 'Process automation and data analytics with Microsoft Power Platform',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => config('seo.address_locality'),
                'addressCountry' => config('seo.address_country_code'),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => config('seo.address_country'),
            ],
            'knowsAbout' => [
                'Microsoft Power Platform',
                'Power Automate',
                'Microsoft Fabric',
                'Python Data Analytics',
                'Cloud Computing',
                'Digital Transformation',
            ],
            'availableLanguage' => ['Spanish', 'English'],
            'priceRange' => '$$',
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
            'inLanguage' => $locale === 'es' ? 'es-CO' : 'en-US',
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
                '@id' => self::localizedPublicUrl('/blogs/' . $blog->slug),
            ],
            'inLanguage' => $locale === 'es' ? 'es-CO' : 'en-US',
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
            'name' => $project->translated('title'),
            'description' => $project->translated('description') ?: strip_tags(Str::limit($project->translated('content'), 200)),
            'image' => $project->image_url ?: ($baseUrl . '/images/logo.png'),
            'creator' => [
                '@type' => 'Organization',
                'name' => 'CloudLab Latam',
                'url' => $baseUrl,
            ],
            'dateCreated' => $project->created_at?->toIso8601String(),
            'dateModified' => $project->updated_at?->toIso8601String(),
            'url' => self::localizedPublicUrl('/projects/' . $project->slug),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => self::localizedPublicUrl('/projects/' . $project->slug),
            ],
            'inLanguage' => $locale === 'es' ? 'es-CO' : 'en-US',
        ];
        
        // Add technologies as keywords if available
        if ($project->translated('technologies') !== '') {
            $schema['keywords'] = $project->translated('technologies');
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

    public static function generateServiceSchema(array $service): array
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'es';
        $baseUrl = \App\Support\LocalizedUrl::origin();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service['h1'] ?? 'Service',
            'serviceType' => $service['nav'] ?? $service['h1'] ?? 'Service',
            'description' => $service['meta_description'] ?? $service['excerpt'] ?? '',
            'url' => self::localizedPublicUrl('/servicios/' . ($service['slug'] ?? '')),
            'provider' => [
                '@type' => 'Organization',
                'name' => 'CloudLab Latam',
                'url' => $baseUrl,
            ],
            'areaServed' => config('seo.address_country'),
            'inLanguage' => $locale === 'es' ? 'es-CO' : 'en',
        ];
    }

    public static function generateFaqSchema(array $faqs): array
    {
        $questions = [];
        foreach ($faqs as $faq) {
            if (empty($faq['question']) || empty($faq['answer'])) {
                continue;
            }
            $questions[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }

    protected static function localizedPublicUrl(string $path): string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'es';
        $base = \App\Support\LocalizedUrl::origin();
        $path = '/' . ltrim($path, '/');

        return $base . '/' . $locale . ($path === '/' ? '' : $path);
    }
}
