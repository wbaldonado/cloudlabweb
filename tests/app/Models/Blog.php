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
            if (empty($blog->slug)) {
                $baseSlug = Str::slug($blog->title);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure unique slug
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $blog->slug = $slug;
            }
        });

        static::updating(function ($blog) {
            // Only update slug if title changed and slug is empty or needs regeneration
            if ($blog->isDirty('title')) {
                if (empty($blog->slug)) {
                    $baseSlug = Str::slug($blog->title);
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
        });
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
}
