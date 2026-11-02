<?php

namespace App\Models\WhatsApp;

use Illuminate\Database\Eloquent\Model;

class ConversationState extends Model
{
    protected $table = 'whatsapp_conversation_states';

    protected $fillable = [
        'phone',
        'step',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function getPayloadValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->payload ?? [], $key, $default);
    }

    public function putPayload(string $key, mixed $value): void
    {
        $payload = $this->payload ?? [];
        $payload[$key] = $value;
        $this->payload = $payload;
    }
}
