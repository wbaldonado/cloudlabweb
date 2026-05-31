<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color_class',
        'icon_path',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];
    
    /**
     * Get the icon URL with version parameter for cache busting.
     *
     * @return string|null
     */
    public function getIconUrlAttribute()
    {
        if (!$this->icon_path) {
            return null;
        }
        
        $url = asset('storage/' . $this->icon_path);
        $version = $this->updated_at ? $this->updated_at->timestamp : time();
        
        return $url . '?v=' . $version;
    }
    
    /**
     * Get the icon URL without version (for backward compatibility).
     *
     * @return string|null
     */
    public function getIconUrlPlainAttribute()
    {
        if (!$this->icon_path) {
            return null;
        }
        
        return asset('storage/' . $this->icon_path);
    }
}
