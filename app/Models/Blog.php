<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, \App\Traits\SanitizesInput;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(BlogRating::class);
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id')->whereNull('parent_id')->where('is_approved', true)->orderBy('created_at', 'desc');
    }

    public function allComments()
    {
        return $this->hasMany(BlogComment::class)->where('is_approved', true)->orderBy('created_at', 'desc');
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?: 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->ratings()->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            // Clean title from HTML tags before generating slug
            $cleanTitle = strip_tags((string) $blog->title);
            $cleanTitle = html_entity_decode($cleanTitle, ENT_QUOTES, 'UTF-8');
            $cleanTitle = trim($cleanTitle);
            
            if (empty(trim((string) $blog->slug))) {
                $baseSlug = Str::slug($cleanTitle);
                if ($baseSlug === '') {
                    $baseSlug = 'post-' . uniqid();
                }
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure unique slug
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $blog->slug = $slug;
            } else {
                // Clean existing slug from HTML tags
                $blog->slug = static::cleanSlug($blog->slug);
                if ($blog->slug === '') {
                    $blog->slug = 'post-' . uniqid();
                }
            }
        });

        static::updating(function ($blog) {
            // Clean title from HTML tags before generating slug
            $cleanTitle = strip_tags((string) $blog->title);
            $cleanTitle = html_entity_decode($cleanTitle, ENT_QUOTES, 'UTF-8');
            $cleanTitle = trim($cleanTitle);
            
            // Always clean the slug if it's being updated
            if ($blog->isDirty('slug')) {
                $blog->slug = static::cleanSlug($blog->slug);
                if ($blog->slug === '') {
                    $blog->slug = 'post-' . $blog->id . '-' . uniqid();
                }
            }
            
            // Only update slug if title changed and slug is empty or needs regeneration
            if ($blog->isDirty('title')) {
                if (empty(trim((string) $blog->slug))) {
                    $baseSlug = Str::slug($cleanTitle);
                    if ($baseSlug === '') {
                        $baseSlug = 'post-' . $blog->id . '-' . uniqid();
                    }
                    $slug = $baseSlug;
                    $counter = 1;
                    
                    // Ensure unique slug (excluding current blog)
                    while (static::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $blog->slug = $slug;
                }
            }
        });

        static::saving(function ($blog) {
            if ($blog->isDirty('content')) {
                $blog->content = $blog->sanitizeHtml($blog->content);
            }
            
            // Always ensure slug is clean (no HTML tags) and never empty
            $currentSlug = $blog->slug;
            if ($currentSlug !== null && $currentSlug !== '') {
                $blog->slug = static::cleanSlug($blog->slug);
            }
            if ($blog->slug === '' || $blog->slug === null) {
                $blog->slug = 'post-' . ($blog->id ?: uniqid()) . '-' . uniqid();
            }
        });
    }
    
    /**
     * Clean slug from HTML tags and ensure it's URL-friendly
     *
     * @param string $slug
     * @return string
     */
    public static function cleanSlug($slug)
    {
        if (empty($slug)) {
            return $slug;
        }
        
        // Remove HTML tags
        $slug = strip_tags($slug);
        
        // Decode HTML entities
        $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');
        
        // Remove any remaining special characters and convert to slug
        $slug = Str::slug($slug);
        
        return $slug;
    }
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    /**
     * Get the featured image URL with version parameter for cache busting.
     *
     * @return string|null
     */
    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return null;
        }
        
        $url = asset('storage/' . $this->featured_image);
        $version = $this->updated_at ? $this->updated_at->timestamp : time();
        
        return $url . '?v=' . $version;
    }
    
    /**
     * Get the featured image URL without version (for backward compatibility).
     *
     * @return string|null
     */
    public function getFeaturedImageUrlPlainAttribute()
    {
        if (!$this->featured_image) {
            return null;
        }
        
        return asset('storage/' . $this->featured_image);
    }
}
