<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Confirmation to the visitor. The subject is fixed so it cannot carry
     * anything the visitor typed.
     */
    public function build(): self
    {
        return $this->subject(__('messages.contact.confirm_subject'))
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.contact_confirmation')
            ->text('emails.contact_confirmation_plain');
    }
}
