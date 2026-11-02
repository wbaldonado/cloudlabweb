<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WhatsAppLeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build(): self
    {
        $company = $this->data['company'] ?? 'Empresa';

        return $this->subject('Nuevo lead WhatsApp: ' . $company)
            ->view('emails.whatsapp_lead_notification');
    }
}
