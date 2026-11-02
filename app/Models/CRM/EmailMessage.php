<?php

namespace App\Models\CRM;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailMessage extends Model
{
    protected $table = 'crm_email_messages';

    protected $fillable = [
        'contact_id',
        'user_id',
        'direction',
        'subject',
        'body',
        'from_email',
        'to_email',
        'message_id',
        'in_reply_to',
        'imap_uid',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isInbound(): bool
    {
        return $this->direction === 'inbound';
    }

    public function isUnread(): bool
    {
        return $this->isInbound() && $this->read_at === null;
    }
}
