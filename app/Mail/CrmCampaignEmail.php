<?php

namespace App\Mail;

use App\Models\CRM\Campaign;
use App\Models\CRM\CampaignRecipient;
use App\Support\CampaignMailer;
use App\Support\CrmEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmCampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign,
        public CampaignRecipient $recipient,
    ) {}

    public function build(): self
    {
        $fromAddress = $this->campaign->from_email ?: (string) config('mail.from.address');
        $fromName = $this->campaign->from_name ?: (string) (config('mail.from.name') ?: config('app.name'));
        $html = CampaignMailer::buildHtml($this->campaign, $this->recipient);
        $text = CampaignMailer::buildText($this->campaign, $this->recipient);
        $unsub = route('crm.track.unsubscribe', $this->recipient->token);
        $messageId = CrmEmail::messageId();

        $mailable = $this->subject($this->campaign->subject)
            ->view('emails.crm_campaign', ['htmlBody' => $html])
            ->text('emails.crm_campaign_text', ['textBody' => $text]);

        if (filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            $mailable->from($fromAddress, $fromName)
                ->replyTo($fromAddress, $fromName);
        }

        try {
            $mailable->withSymfonyMessage(function ($message) use ($unsub, $messageId, $fromAddress) {
                $headers = $message->getHeaders();
                $headers->addTextHeader('List-Unsubscribe', '<' . $unsub . '>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $headers->addTextHeader('X-Campaign-ID', (string) $this->campaign->id);
                $headers->addTextHeader('Precedence', 'bulk');
                $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
                if ($fromAddress) {
                    $headers->addTextHeader('X-Mailer', 'CloudLab CRM');
                }
                $id = trim($messageId, "<> \t\n\r");
                if ($id !== '') {
                    if ($headers->has('Message-ID')) {
                        $headers->remove('Message-ID');
                    }
                    $headers->addIdHeader('Message-ID', $id);
                }
            });
        } catch (\Throwable) {
            // ignore header issues
        }

        return $mailable;
    }
}
