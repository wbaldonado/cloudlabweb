<?php

namespace App\Mail;

use App\Models\CRM\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmOutboundEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public string $emailSubject,
        public string $emailBody,
        public string $crmMessageId,
    ) {}

    public function build(): self
    {
        $fromAddress = (string) config('mail.from.address');
        $fromName = (string) (config('mail.from.name') ?: config('app.name'));

        $mailable = $this->subject($this->emailSubject)
            ->view('emails.crm_outbound');

        if (filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            $mailable->from($fromAddress, $fromName)
                ->replyTo($fromAddress, $fromName);
        }

        // Custom Message-ID helps match replies later; never block sending if it fails.
        try {
            $id = trim($this->crmMessageId, "<> \t\n\r");
            if ($id !== '') {
                $mailable->withSymfonyMessage(function ($message) use ($id) {
                    $headers = $message->getHeaders();
                    if ($headers->has('Message-ID')) {
                        $headers->remove('Message-ID');
                    }
                    $headers->addIdHeader('Message-ID', $id);
                });
            }
        } catch (\Throwable) {
            // ignore — Symfony will generate a default Message-ID
        }

        return $mailable;
    }
}
