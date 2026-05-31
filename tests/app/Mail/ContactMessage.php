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
        $subject = $this->data['subject'] ?? __('messages.contact.default_subject');
        
        // Use company name as "From" name, or person's name if no company
        $fromName = !empty($this->data['company']) 
            ? $this->data['company'] 
            : ($this->data['name'] ?? config('mail.from.name'));

        return $this->subject($subject)
            ->from(config('mail.from.address'), $fromName)
            ->replyTo($this->data['email'], $this->data['name'])
            ->view('emails.contact_message_simple')
            ->text('emails.contact_message_plain');
    }
}

