<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile
{
    /**
     * Null when the visitor passed, or when Turnstile is not configured yet.
     * Otherwise a translation key.
     */
    public static function rejection(Request $request): ?string
    {
        $secret = config('services.turnstile.secret');
        if (! is_string($secret) || $secret === '') {
            return null;
        }

        $token = $request->input('cf-turnstile-response');
        if (! is_string($token) || $token === '') {
            return 'messages.contact.human_required';
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $exception) {
            Log::warning('Turnstile verification could not reach Cloudflare');

            return 'messages.contact.human_failed';
        }

        if (! $response->ok() || $response->json('success') !== true) {
            Log::info('Turnstile verification rejected', [
                'codes' => $response->json('error-codes'),
            ]);

            return 'messages.contact.human_failed';
        }

        return null;
    }
}
