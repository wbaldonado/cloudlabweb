<?php

namespace App\Support\WhatsApp;

use App\Mail\WhatsAppLeadNotification;
use App\Models\CRM\Activity;
use App\Models\CRM\Company;
use App\Models\CRM\Contact;
use App\Models\WhatsApp\ConversationState;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadConversation
{
    public const STEP_GREETING = 'greeting';

    public const STEP_ASK_COMPANY = 'ask_company';

    public const STEP_ASK_EMAIL = 'ask_email';

    public const STEP_ASK_REQUEST = 'ask_request';

    public const STEP_COMPLETED = 'completed';

    public function __construct(
        protected WhatsAppClient $client
    ) {}

    public function handleIncoming(string $phone, string $text): void
    {
        $text = trim($text);
        if ($text === '') {
            return;
        }

        $state = ConversationState::firstOrCreate(
            ['phone' => $this->normalizePhone($phone)],
            ['step' => self::STEP_GREETING, 'payload' => []]
        );

        $lower = mb_strtolower($text);

        if ($this->isRestartCommand($lower)) {
            $state->step = self::STEP_GREETING;
            $state->payload = [];
            $state->save();
        }

        if ($state->step === self::STEP_COMPLETED) {
            if ($this->wantsNewLead($lower)) {
                $state->step = self::STEP_GREETING;
                $state->payload = [];
                $state->save();
            } else {
                $this->client->sendText(
                    $state->phone,
                    "Ya registramos tu solicitud. Si quieres dejar otra, escribe *nueva*."
                );

                return;
            }
        }

        match ($state->step) {
            self::STEP_GREETING => $this->startConversation($state),
            self::STEP_ASK_COMPANY => $this->captureCompany($state, $text),
            self::STEP_ASK_EMAIL => $this->captureEmail($state, $text),
            self::STEP_ASK_REQUEST => $this->captureRequest($state, $text),
            default => $this->startConversation($state),
        };
    }

    protected function startConversation(ConversationState $state): void
    {
        $state->step = self::STEP_ASK_COMPANY;
        $state->payload = [];
        $state->save();

        $this->client->sendText(
            $state->phone,
            "¡Hola! Soy el asistente de *CloudLab Latam*.\n\n"
            . "Para ayudarte mejor, necesito algunos datos.\n\n"
            . "¿Cuál es el *nombre de tu empresa*?"
        );
    }

    protected function captureCompany(ConversationState $state, string $text): void
    {
        if (mb_strlen($text) < 2) {
            $this->client->sendText($state->phone, "Por favor escribe el nombre de tu empresa.");

            return;
        }

        $state->putPayload('company', Str::limit($text, 255, ''));
        $state->step = self::STEP_ASK_EMAIL;
        $state->save();

        $this->client->sendText(
            $state->phone,
            "Gracias. Ahora indícame un *correo electrónico* de contacto."
        );
    }

    protected function captureEmail(ConversationState $state, string $text): void
    {
        $email = strtolower(trim($text));
        // Allow "Name <email@x.com>" style
        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $email, $m)) {
            $email = strtolower($m[0]);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->client->sendText(
                $state->phone,
                "Ese correo no parece válido. Ejemplo: *nombre@empresa.com*"
            );

            return;
        }

        $state->putPayload('email', $email);
        $state->step = self::STEP_ASK_REQUEST;
        $state->save();

        $this->client->sendText(
            $state->phone,
            "Perfecto. ¿En qué consiste tu *solicitud* o en qué podemos ayudarte?"
        );
    }

    protected function captureRequest(ConversationState $state, string $text): void
    {
        if (mb_strlen($text) < 5) {
            $this->client->sendText(
                $state->phone,
                "Cuéntame un poco más sobre tu solicitud (al menos una frase breve)."
            );

            return;
        }

        $state->putPayload('request', Str::limit($text, 5000, ''));
        $state->save();

        try {
            $this->persistLead($state);
        } catch (\Throwable $e) {
            Log::error('WhatsApp lead persist failed', [
                'phone' => $state->phone,
                'error' => $e->getMessage(),
            ]);
            $this->client->sendText(
                $state->phone,
                "Recibimos tu mensaje, pero hubo un problema al guardarlo. Un asesor te contactará igual. Gracias."
            );
            $state->step = self::STEP_COMPLETED;
            $state->save();

            return;
        }

        $state->step = self::STEP_COMPLETED;
        $state->save();

        $this->client->sendText(
            $state->phone,
            "¡Gracias! Registramos tu solicitud.\n\n"
            . "Un miembro del equipo de *CloudLab Latam* te contactará pronto."
        );

        $this->notifyTeam($state);
    }

    protected function persistLead(ConversationState $state): void
    {
        $companyName = (string) $state->getPayloadValue('company', 'Empresa WhatsApp');
        $email = (string) $state->getPayloadValue('email');
        $request = (string) $state->getPayloadValue('request', '');

        $company = Company::firstOrCreate(
            ['name' => $companyName],
            [
                'phone' => $state->phone,
                'description' => 'Lead capturado por WhatsApp',
                'country' => 'Colombia',
            ]
        );

        $local = Str::before($email, '@');
        $parts = preg_split('/[.\-_]+/', $local) ?: [$local];
        $firstName = Str::title($parts[0] ?? 'Lead');
        $lastName = isset($parts[1]) ? Str::title($parts[1]) : 'WhatsApp';

        $contact = Contact::query()
            ->where('email', $email)
            ->first();

        if ($contact) {
            $contact->fill([
                'phone' => $state->phone,
                'company_id' => $company->id,
                'lead_source' => 'whatsapp',
                'capture_method' => 'whatsapp',
                'notes' => trim(($contact->notes ? $contact->notes . "\n\n" : '') . 'WhatsApp: ' . $request),
            ]);
            if (! $contact->captured_at) {
                $contact->captured_at = now();
            }
            $contact->save();
        } else {
            $contact = Contact::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $state->phone,
                'company_id' => $company->id,
                'lifecycle_stage' => 'lead',
                'lead_source' => 'whatsapp',
                'capture_method' => 'whatsapp',
                'lead_score' => 10,
                'notes' => 'WhatsApp: ' . $request,
                'captured_at' => now(),
            ]);
        }

        Activity::create([
            'title' => 'Lead WhatsApp: ' . Str::limit($request, 80, ''),
            'type' => 'call',
            'status' => 'not_started',
            'due_date' => now()->addDay()->toDateString(),
            'contact_id' => $contact->id,
            'company_id' => $company->id,
            'notes' => "Teléfono WhatsApp: {$state->phone}\nEmail: {$email}\nEmpresa: {$companyName}\n\nSolicitud:\n{$request}",
        ]);
    }

    protected function notifyTeam(ConversationState $state): void
    {
        $to = config('whatsapp.notify_email');
        if (! $to) {
            return;
        }

        try {
            Mail::to($to)->send(new WhatsAppLeadNotification([
                'company' => $state->getPayloadValue('company'),
                'email' => $state->getPayloadValue('email'),
                'request' => $state->getPayloadValue('request'),
                'phone' => $state->phone,
            ]));
        } catch (\Throwable $e) {
            Log::warning('WhatsApp lead notify email failed', ['error' => $e->getMessage()]);
        }
    }

    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?: $phone;
    }

    protected function isRestartCommand(string $lower): bool
    {
        return in_array($lower, ['hola', 'hi', 'hello', 'buenas', 'buen dia', 'buen día', 'menu', 'menú', 'inicio', 'start'], true);
    }

    protected function wantsNewLead(string $lower): bool
    {
        return in_array($lower, ['nueva', 'nuevo', 'otra', 'reiniciar', 'si', 'sí', 'yes'], true)
            || $this->isRestartCommand($lower);
    }
}
