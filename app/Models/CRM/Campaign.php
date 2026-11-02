<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $table = 'crm_campaigns';

    protected $fillable = [
        'name', 'subject', 'html_body', 'text_body', 'status',
        'from_name', 'from_email', 'cta_label', 'cta_url', 'created_by',
        'sent_at', 'recipients_count', 'sent_count', 'failed_count',
        'open_count', 'unique_open_count', 'click_count', 'unique_click_count',
        'unsubscribe_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class, 'campaign_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(CampaignEvent::class, 'campaign_id');
    }

    public function openRate(): float
    {
        if ($this->sent_count < 1) {
            return 0;
        }

        return round(($this->unique_open_count / $this->sent_count) * 100, 1);
    }

    public function clickRate(): float
    {
        if ($this->sent_count < 1) {
            return 0;
        }

        return round(($this->unique_click_count / $this->sent_count) * 100, 1);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Borrador',
            'sending' => 'Enviando',
            'sent' => 'Enviada',
            'cancelled' => 'Cancelada',
            default => $this->status,
        };
    }
}
