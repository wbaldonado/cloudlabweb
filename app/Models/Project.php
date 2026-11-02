<?php

namespace App\Models;

use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, \App\Traits\SanitizesInput;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'meta_title',
        'meta_description',
        'meta_title_en',
        'meta_description_en',
        'content',
        'content_en',
        'image',
        'url',
        'github_url',
        'technologies',
        'technologies_en',
        'is_featured',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Spanish text, or the English text when the site language is English.
     * An empty English field keeps the Spanish text.
     */
    public function translated(string $field): string
    {
        $primary = $this->cleanStoredHtml(trim((string) ($this->attributes[$field] ?? '')));

        if (app()->getLocale() !== 'en' || ! array_key_exists($field . '_en', $this->attributes)) {
            return $primary;
        }

        $english = $this->cleanStoredHtml(trim((string) ($this->attributes[$field . '_en'] ?? '')));
        if ($english === '' || $this->isBlankHtml($english)) {
            return $primary;
        }

        return $english;
    }

    protected function cleanStoredHtml(string $html): string
    {
        return trim(preg_replace('/<\?xml[^>]*>/i', '', $html) ?? $html);
    }

    protected function isBlankHtml(string $html): bool
    {
        $html = $this->cleanStoredHtml($html);
        $text = html_entity_decode(strip_tags(str_replace('&nbsp;', ' ', $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim($text) === '';
    }

    /**
     * Public URL for the project cover image (Hostinger-safe under public/images).
     */
    public function getImageUrlAttribute(): ?string
    {
        $version = $this->updated_at ? $this->updated_at->timestamp : null;

        return PublicMedia::url($this->image, $version);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::saving(function ($project) {
            foreach (['content', 'content_en'] as $field) {
                if ($project->isDirty($field) && $project->{$field}) {
                    $project->{$field} = $project->sanitizeHtml($project->{$field});
                }
            }
        });
    }
}
