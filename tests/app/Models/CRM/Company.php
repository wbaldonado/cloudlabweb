<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'crm_companies';

    protected $fillable = [
        'name',
        'industry',
        'website',
        'description',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'company_id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'company_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'company_id');
    }
}

