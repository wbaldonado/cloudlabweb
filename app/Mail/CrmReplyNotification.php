<?php

namespace App\Mail;

use App\Models\CRM\Contact;
use App\Models\CRM\EmailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailMessage $inbound,
        public Contact $contact,
    ) {}

    public function build(): self
    {
        return $this->subject('[CRM] Respuesta de ' . $this->contact->full_name . ': ' . $this->inbound->subject)
            ->view('emails.crm_reply_notification');
    }
}
