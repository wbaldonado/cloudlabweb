<?php

namespace App\Support;

class ContactFormGuard
{
    public static function token(): string
    {
        $issued = (string) time();

        return $issued.'.'.hash_hmac('sha256', $issued, self::key());
    }

    /**
     * Null when the token is valid. Otherwise a translation key for the visitor.
     */
    public static function rejection(?string $token): ?string
    {
        if (! is_string($token) || ! preg_match('/^(\d{10})\.([a-f0-9]{64})$/', $token, $matches)) {
            return 'messages.contact.form_expired';
        }

        $expected = hash_hmac('sha256', $matches[1], self::key());
        if (! hash_equals($expected, $matches[2])) {
            return 'messages.contact.form_expired';
        }

        $age = time() - (int) $matches[1];
        if ($age < 3) {
            return 'messages.contact.form_too_fast';
        }
        if ($age > 7200) {
            return 'messages.contact.form_expired';
        }

        return null;
    }

    private static function key(): string
    {
        $key = (string) config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $key;
    }
}
