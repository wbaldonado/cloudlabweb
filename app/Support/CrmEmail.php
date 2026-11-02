<?php

namespace App\Support;

use Illuminate\Support\Str;

class CrmEmail
{
    /**
     * RFC Message-ID without surrounding angle brackets.
     * Symfony's addIdHeader() adds the <> wrappers itself.
     */
    public static function messageId(): string
    {
        $domain = (string) (config('crm.message_id_domain') ?: 'cloudlablatam.tech');
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain) ?? $domain;
        $domain = explode('/', $domain)[0] ?? $domain;
        $domain = explode(':', $domain)[0] ?? $domain;
        if ($domain === '' || $domain === 'localhost' || $domain === '127.0.0.1') {
            $domain = 'cloudlablatam.tech';
        }

        return 'crm-' . Str::uuid() . '@' . $domain;
    }

    public static function formatMessageId(string $id): string
    {
        $id = trim($id);

        return str_starts_with($id, '<') ? $id : '<' . $id . '>';
    }
}
