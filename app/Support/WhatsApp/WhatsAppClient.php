<?php

namespace App\Support\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppClient
{
    public function sendText(string $toPhone, string $body): bool
    {
        if (! config('whatsapp.enabled')) {
            Log::warning('WhatsApp send skipped: WHATSAPP_ENABLED is false');

            return false;
        }

        $token = config('whatsapp.token');
        $phoneNumberId = config('whatsapp.phone_number_id');

        if (! $token || ! $phoneNumberId) {
            Log::error('WhatsApp send failed: missing token or phone_number_id');

            return false;
        }

        $to = preg_replace('/\D+/', '', $toPhone) ?: $toPhone;
        $url = rtrim((string) config('whatsapp.graph_url'), '/')
            . '/' . trim((string) config('whatsapp.api_version'), '/')
            . '/' . $phoneNumberId
            . '/messages';

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $body,
                    ],
                ]);

            if (! $response->successful()) {
                Log::error('WhatsApp API error', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                    'to' => $to,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);

            return false;
        }
    }
}
