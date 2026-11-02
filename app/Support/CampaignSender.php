<?php

namespace App\Support;

use App\Mail\CrmCampaignEmail;
use App\Models\CRM\Campaign;
use App\Models\CRM\EmailUnsubscribe;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CampaignSender
{
    /**
     * Send a batch of pending campaign emails (Hostinger-safe: works with QUEUE=sync).
     *
     * @return array{ok: bool, message: string, sent: int, failed: int, remaining: int}
     */
    public function sendBatch(Campaign $campaign, int $limit = 50): array
    {
        if (! in_array($campaign->status, ['draft', 'sending'], true)) {
            return [
                'ok' => false,
                'message' => 'Esta campaña ya fue enviada o cancelada.',
                'sent' => 0,
                'failed' => 0,
                'remaining' => 0,
            ];
        }

        $smtpHost = (string) config('mail.mailers.smtp.host');
        $smtpPass = (string) config('mail.mailers.smtp.password');
        if ($smtpHost === '' || $smtpHost === 'mailpit' || $smtpPass === '' || $smtpPass === 'null') {
            return [
                'ok' => false,
                'message' => 'SMTP no configurado. Revisa MAIL_* en .env.',
                'sent' => 0,
                'failed' => 0,
                'remaining' => 0,
            ];
        }

        @set_time_limit(300);

        $campaign->update(['status' => 'sending']);

        $limit = min(100, max(1, $limit));
        $pending = $campaign->recipients()->where('status', 'pending')->limit($limit)->get();

        $sent = 0;
        $failed = 0;

        foreach ($pending as $recipient) {
            if (EmailUnsubscribe::isUnsubscribed($recipient->email)) {
                $recipient->update(['status' => 'skipped', 'error' => 'unsubscribed']);
                continue;
            }

            try {
                Mail::mailer('smtp')->to($recipient->email, $recipient->name)
                    ->send(new CrmCampaignEmail($campaign, $recipient));

                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error' => null,
                ]);
                $sent++;
                usleep(120000);
            } catch (\Throwable $e) {
                report($e);
                Log::error('Campaign email failed', [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
                $recipient->update([
                    'status' => 'failed',
                    'error' => Str::limit($e->getMessage(), 500, ''),
                ]);
                $failed++;
            }
        }

        $campaign->increment('sent_count', $sent);
        $campaign->increment('failed_count', $failed);

        $remaining = $campaign->recipients()->where('status', 'pending')->count();
        if ($remaining === 0) {
            $campaign->update([
                'status' => 'sent',
                'sent_at' => $campaign->sent_at ?: now(),
            ]);
            $msg = "Envío completado: {$sent} enviados, {$failed} fallidos.";
        } else {
            $msg = "Lote enviado: {$sent} ok, {$failed} fallidos. Quedan {$remaining}. Pulsa Enviar de nuevo para continuar.";
        }

        return [
            'ok' => true,
            'message' => $msg,
            'sent' => $sent,
            'failed' => $failed,
            'remaining' => $remaining,
        ];
    }
}
