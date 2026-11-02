<?php

namespace App\Models;

use App\Support\Branding;
use App\Support\PublicMedia;
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
     * Public URL for the technology icon (cache-busted).
     */
    public function getIconUrlAttribute(): ?string
    {
        $webPath = $this->publicIconPath();

        if (! $webPath) {
            return null;
        }

        $version = $this->updated_at ? $this->updated_at->timestamp : time();

        return Branding::assetUrl($webPath) . '?v=' . $version;
    }

    /**
     * Icon URL without cache-busting query string.
     */
    public function getIconUrlPlainAttribute(): ?string
    {
        $webPath = $this->publicIconPath();

        return $webPath ? Branding::assetUrl($webPath) : null;
    }

    public function iconFileExists(): bool
    {
        return $this->publicIconPath() !== null;
    }

    /**
     * Web path under public/ for this icon, only when the file exists on disk.
     */
    public function publicIconPath(): ?string
    {
        return PublicMedia::resolveWebPath($this->icon_path);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) >= 2) {
            return strtoupper(
                mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
            );
        }

        return strtoupper(mb_substr((string) $this->name, 0, 2));
    }
}
