<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $table = 'crm_deals';

    protected $fillable = [
        'title',
        'amount',
        'stage',
        'contact_id',
        'company_id',
        'owner_id',
        'expected_close_date',
        'description',
        'probability',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_close_date' => 'date',
        'probability' => 'integer',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isWon(): bool
    {
        return $this->stage === 'won';
    }

    public function isLost(): bool
    {
        return $this->stage === 'lost';
    }

    public function isOpen(): bool
    {
        return !$this->isWon() && !$this->isLost();
    }

    public function weightedValue(): float
    {
        return (float) $this->amount * ($this->probability / 100);
    }

    public function stageLabel(): string
    {
        return match ($this->stage) {
            'prospecting' => 'Prospección',
            'qualified' => 'Calificación',
            'proposal' => 'Propuesta',
            'negotiation' => 'Negociación',
            'won' => 'Ganada',
            'lost' => 'Perdida',
            default => ucfirst($this->stage),
        };
    }

    public function stageBadgeClass(): string
    {
        return match ($this->stage) {
            'won' => 'crm-badge--won',
            'lost' => 'crm-badge--lost',
            'negotiation' => 'crm-badge--negotiation',
            'proposal' => 'crm-badge--proposal',
            'qualified' => 'crm-badge--qualified',
            default => 'crm-badge--prospecting',
        };
    }
}

