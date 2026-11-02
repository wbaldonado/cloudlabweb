<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class EmailUnsubscribe extends Model
{
    protected $table = 'crm_email_unsubscribes';

    protected $fillable = ['email', 'reason'];

    public static function isUnsubscribed(string $email): bool
    {
        return static::query()
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
            ->exists();
    }
}
