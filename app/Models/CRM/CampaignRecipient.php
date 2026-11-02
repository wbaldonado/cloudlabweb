<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignRecipient extends Model
{
    protected $table = 'crm_campaign_recipients';

    protected $fillable = [
        'campaign_id', 'contact_id', 'email', 'name', 'token', 'status', 'error',
        'sent_at', 'opened_at', 'clicked_at', 'open_count', 'click_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(CampaignEvent::class, 'recipient_id');
    }

    public function statusLabel(): string
    {
        return self::labelFor((string) $this->status);
    }

    public function statusBadgeClass(): string
    {
        return self::badgeClassFor((string) $this->status);
    }

    public static function labelFor(string $status): string
    {
        return match ($status) {
            'pending' => 'Pendiente',
            'sent' => 'Enviado',
            'failed' => 'Fallido',
            'skipped' => 'Omitido',
            'unsubscribed' => 'Baja',
            default => ucfirst($status),
        };
    }

    public static function badgeClassFor(string $status): string
    {
        return match ($status) {
            'sent' => 'bg-success',
            'failed' => 'bg-danger',
            'skipped' => 'bg-warning text-dark',
            'unsubscribed' => 'bg-dark',
            default => 'bg-secondary',
        };
    }
}
