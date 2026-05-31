<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'crm_contacts';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'job_title',
        'lifecycle_stage',
        'lead_source',
        'lead_score',
        'capture_method',
        'owner_id',
        'assigned_to',
        'company_id',
        'notes',
        'captured_at',
        'converted_at',
    ];

    protected $casts = [
        'lifecycle_stage' => 'string',
        'lead_score' => 'integer',
        'captured_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'contact_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isLead(): bool
    {
        return in_array($this->lifecycle_stage, ['lead', 'mql', 'sql']);
    }

    public function convertToCustomer(): void
    {
        if ($this->lifecycle_stage !== 'customer') {
            $this->lifecycle_stage = 'customer';
            $this->converted_at = now();
            $this->save();
        }
    }
}

