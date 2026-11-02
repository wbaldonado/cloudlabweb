<?php

namespace App\Http\Controllers;

use App\Support\WhatsApp\LeadConversation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && hash_equals((string) config('whatsapp.verify_token'), (string) $token)) {
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token_match' => $token === config('whatsapp.verify_token'),
        ]);

        return response('Forbidden', 403);
    }

    public function handle(Request $request, LeadConversation $conversation): Response
    {
        $payload = $request->all();

        try {
            $entries = $payload['entry'] ?? [];
            foreach ($entries as $entry) {
                $changes = $entry['changes'] ?? [];
                foreach ($changes as $change) {
                    if (($change['field'] ?? '') !== 'messages') {
                        continue;
                    }

                    $value = $change['value'] ?? [];
                    $messages = $value['messages'] ?? [];

                    foreach ($messages as $message) {
                        if (($message['type'] ?? '') !== 'text') {
                            $from = $message['from'] ?? null;
                            if ($from) {
                                app(\App\Support\WhatsApp\WhatsAppClient::class)->sendText(
                                    $from,
                                    'Por ahora solo puedo leer mensajes de texto. Por favor escribe tu respuesta.'
                                );
                            }

                            continue;
                        }

                        $from = (string) ($message['from'] ?? '');
                        $text = (string) ($message['text']['body'] ?? '');

                        if ($from === '' || $text === '') {
                            continue;
                        }

                        $conversation->handleIncoming($from, $text);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook handling failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Always 200 so Meta does not retry aggressively
        return response('EVENT_RECEIVED', 200);
    }
}
