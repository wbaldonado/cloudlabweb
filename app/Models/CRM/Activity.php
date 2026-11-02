<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'crm_activities';

    protected $fillable = [
        'title',
        'type',
        'status',
        'due_date',
        'contact_id',
        'company_id',
        'deal_id',
        'owner_id',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isPending(): bool
    {
        return $this->status !== 'completed';
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'email' => 'Correo',
            'meeting' => 'Reunión',
            'call' => 'Llamada',
            default => 'Tarea',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'in_progress' => 'En progreso',
            'blocked' => 'Bloqueada',
            'completed' => 'Completada',
            default => 'No iniciada',
        };
    }
}
