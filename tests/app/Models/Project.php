<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, \App\Traits\SanitizesInput;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'url',
        'github_url',
        'technologies',
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
            if ($project->isDirty('content')) {
                $project->content = $project->sanitizeHtml($project->content);
            }
        });
    }
}
