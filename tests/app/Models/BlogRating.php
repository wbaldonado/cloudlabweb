<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'rating',
        'visitor_name',
        'visitor_email',
        'ip_address',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
