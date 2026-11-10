<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Hostinger rejects some notices when Reply-To is another domain.
     * The visitor address stays in the body either way.
     */
    public bool $includeReplyTo = true;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $subject = trim((string) ($this->data['subject'] ?? ''));
        if ($subject === '') {
            $subject = __('messages.contact.default_subject');
        }
        if (! str_starts_with($subject, '[CloudLab]')) {
            $subject = '[CloudLab] ' . $subject;
        }

        $mail = $this->subject($subject)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.contact_message_simple')
            ->text('emails.contact_message_plain');

        $replyTo = trim((string) ($this->data['email'] ?? ''));
        if ($this->includeReplyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($replyTo);
        }

        return $mail;
    }
}

