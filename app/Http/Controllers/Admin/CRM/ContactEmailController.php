<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Mail\CrmOutboundEmail;
use App\Models\CRM\Activity;
use App\Models\CRM\Contact;
use App\Models\CRM\EmailMessage;
use App\Support\CrmEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactEmailController extends Controller
{
    public function store(Request $request, Contact $contact)
    {
        if (! $contact->email) {
            return response()->json([
                'success' => false,
                'message' => 'Este contacto no tiene email.',
            ], 422);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:20000',
        ]);

        $smtpHost = (string) config('mail.mailers.smtp.host');
        $smtpUser = (string) config('mail.mailers.smtp.username');
        $smtpPass = (string) config('mail.mailers.smtp.password');
        $from = (string) config('mail.from.address');

        if ($smtpHost === '' || $smtpHost === 'mailpit' || $smtpUser === '' || $smtpPass === '' || $smtpPass === 'null') {
            return response()->json([
                'success' => false,
                'message' => 'SMTP no está configurado. En .env define MAIL_HOST=smtp.hostinger.com, MAIL_PORT=587, MAIL_ENCRYPTION=tls, MAIL_USERNAME, MAIL_PASSWORD (entre comillas) y MAIL_FROM_ADDRESS=info@cloudlablatam.com. Luego: php artisan config:clear',
            ], 422);
        }

        if (! filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'MAIL_FROM_ADDRESS no es válido en .env.',
            ], 422);
        }

        $messageId = CrmEmail::messageId();

        try {
            Mail::mailer('smtp')->to($contact->email)->send(new CrmOutboundEmail(
                $contact,
                $validated['subject'],
                $validated['body'],
                $messageId,
            ));
        } catch (\Throwable $e) {
            report($e);

            Log::error('CRM outbound email failed', [
                'contact_id' => $contact->id,
                'to' => $contact->email,
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username'),
                'from' => config('mail.from.address'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $this->friendlySmtpError($e),
            ], 500);
        }

        try {
            $record = EmailMessage::create([
                'contact_id' => $contact->id,
                'user_id' => auth()->id(),
                'direction' => 'outbound',
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'from_email' => config('mail.from.address'),
                'to_email' => $contact->email,
                'message_id' => CrmEmail::formatMessageId($messageId),
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado a ' . $contact->email . ' (no se pudo guardar el historial en CRM).',
                'email' => [
                    'id' => null,
                    'subject' => $validated['subject'],
                    'sent_at' => now()->format('Y-m-d H:i'),
                ],
            ]);
        }

        try {
            Activity::create([
                'title' => 'Email enviado: ' . Str::limit($validated['subject'], 100, ''),
                'type' => 'email',
                'status' => 'completed',
                'contact_id' => $contact->id,
                'company_id' => $contact->company_id,
                'owner_id' => auth()->id(),
                'notes' => Str::limit($validated['body'], 1000, ''),
                'due_date' => now()->toDateString(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'message' => 'Correo enviado a ' . $contact->email,
            'email' => [
                'id' => $record->id,
                'subject' => $record->subject,
                'sent_at' => $record->sent_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    protected function friendlySmtpError(\Throwable $e): string
    {
        $raw = $e->getMessage();
        $lower = strtolower($raw);

        if (str_contains($lower, 'failed to authenticate')
            || str_contains($lower, 'authentication failed')
            || str_contains($lower, '535')
            || str_contains($lower, 'invalid login')) {
            return 'SMTP rechazó el login. En .env pon MAIL_PASSWORD entre comillas dobles (por el carácter [), guarda, y ejecuta php artisan config:clear.';
        }

        if (str_contains($lower, 'connection could not be established')
            || str_contains($lower, 'timed out')
            || str_contains($lower, 'connection refused')) {
            return 'No hay conexión SMTP. Prueba MAIL_PORT=587 y MAIL_ENCRYPTION=tls (o 465/ssl), luego php artisan config:clear.';
        }

        if (str_contains($lower, 'stream_socket') || str_contains($lower, 'ssl') || str_contains($lower, 'certificate')) {
            return 'Error SSL/TLS con el servidor de correo. Usa MAIL_PORT=587 y MAIL_ENCRYPTION=tls, luego php artisan config:clear.';
        }

        // Always return a short technical hint so production can be fixed without APP_DEBUG.
        $short = Str::limit(preg_replace('/\s+/', ' ', $raw) ?: 'error desconocido', 180, '…');

        return 'No se pudo enviar el correo: ' . $short;
    }
}
