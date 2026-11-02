<?php

namespace App\Support;

use App\Mail\CrmReplyNotification;
use App\Models\CRM\Activity;
use App\Models\CRM\Contact;
use App\Models\CRM\EmailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CrmImapInbox
{
    public function fetch(): int
    {
        if (! config('crm.imap.enabled')) {
            return 0;
        }

        if (! extension_loaded('imap')) {
            Log::warning('CRM IMAP: PHP imap extension is not enabled.');

            return 0;
        }

        $config = config('crm.imap');
        $mailbox = sprintf(
            '{%s:%d/imap/%s}%s',
            $config['host'],
            $config['port'],
            $config['encryption'] ?: 'ssl',
            $config['folder']
        );

        $connection = @imap_open($mailbox, $config['username'], $config['password']);
        if (! $connection) {
            Log::error('CRM IMAP: connection failed.', ['error' => imap_last_error()]);

            return 0;
        }

        $processed = 0;
        $uids = imap_search($connection, 'UNSEEN', SE_UID) ?: [];

        foreach ($uids as $uid) {
            if ($this->processMessage($connection, (int) $uid, $config['folder'])) {
                $processed++;
            }
        }

        imap_close($connection);

        return $processed;
    }

    protected function processMessage($connection, int $uid, string $folder): bool
    {
        $imapUid = $folder . ':' . $uid;
        if (EmailMessage::where('imap_uid', $imapUid)->exists()) {
            return false;
        }

        $overview = imap_fetch_overview($connection, (string) $uid, FT_UID);
        if (empty($overview[0])) {
            return false;
        }

        $meta = $overview[0];
        $fromEmail = $this->extractEmail($meta->from ?? '');
        $toEmail = $this->extractEmail($meta->to ?? '');
        $subject = isset($meta->subject) ? $this->decodeMime($meta->subject) : '(sin asunto)';
        $messageId = isset($meta->message_id) ? trim($meta->message_id) : null;
        $inReplyTo = isset($meta->in_reply_to) ? trim($meta->in_reply_to) : null;
        $body = $this->extractBody($connection, $uid);

        if ($fromEmail === '' || $this->isOwnAddress($fromEmail)) {
            imap_setflag_full($connection, (string) $uid, '\\Seen', ST_UID);

            return false;
        }

        $contact = $this->resolveContact($fromEmail, $inReplyTo);
        if (! $contact) {
            return false;
        }

        $inbound = EmailMessage::create([
            'contact_id' => $contact->id,
            'direction' => 'inbound',
            'subject' => Str::limit($subject, 255, ''),
            'body' => $body,
            'from_email' => $fromEmail,
            'to_email' => $toEmail ?: (string) config('mail.from.address'),
            'message_id' => $messageId,
            'in_reply_to' => $inReplyTo,
            'imap_uid' => $imapUid,
            'sent_at' => isset($meta->date) ? date('Y-m-d H:i:s', strtotime($meta->date)) : now(),
        ]);

        Activity::create([
            'title' => 'Respuesta: ' . Str::limit($subject, 100, ''),
            'type' => 'email',
            'status' => 'not_started',
            'contact_id' => $contact->id,
            'company_id' => $contact->company_id,
            'notes' => Str::limit($body, 1000, ''),
            'due_date' => now()->toDateString(),
        ]);

        $notify = config('crm.notify_email');
        if ($notify) {
            Mail::to($notify)->send(new CrmReplyNotification($inbound, $contact));
        }

        imap_setflag_full($connection, (string) $uid, '\\Seen', ST_UID);

        return true;
    }

    protected function resolveContact(string $fromEmail, ?string $inReplyTo): ?Contact
    {
        if ($inReplyTo) {
            $needle = trim($inReplyTo);
            $parent = EmailMessage::where(function ($q) use ($needle) {
                $q->where('message_id', $needle)
                    ->orWhere('message_id', trim($needle, '<>'))
                    ->orWhere('message_id', '<' . trim($needle, '<>') . '>');
            })->first();

            if ($parent?->contact) {
                return $parent->contact;
            }
        }

        return Contact::where('email', $fromEmail)->first();
    }

    protected function isOwnAddress(string $email): bool
    {
        $own = array_filter([
            strtolower((string) config('mail.from.address')),
            strtolower((string) config('crm.imap.username')),
        ]);

        return in_array(strtolower($email), $own, true);
    }

    protected function extractEmail(string $raw): string
    {
        if (preg_match('/<([^>]+)>/', $raw, $m)) {
            return strtolower(trim($m[1]));
        }

        return strtolower(trim($raw));
    }

    protected function decodeMime(string $value): string
    {
        $decoded = imap_utf8($value);

        return $decoded !== false ? $decoded : $value;
    }

    protected function extractBody($connection, int $uid): string
    {
        $structure = imap_fetchstructure($connection, $uid, FT_UID);
        if (! $structure) {
            return trim(imap_body($connection, (string) $uid, FT_UID) ?: '');
        }

        $text = $this->extractPart($connection, $uid, $structure, '');
        $text = trim(strip_tags($text));
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return Str::limit($text, 20000, '');
    }

    protected function extractPart($connection, int $uid, $structure, string $partNumber): string
    {
        if ($structure->type === TYPETEXT) {
            $body = imap_fetchbody($connection, (string) $uid, $partNumber ?: '1', FT_UID);
            $body = $this->decodeBody($body, (int) ($structure->encoding ?? 0));

            if (strtolower($structure->subtype ?? '') === 'plain') {
                return $body;
            }

            if (strtolower($structure->subtype ?? '') === 'html' && $body !== '') {
                return $body;
            }
        }

        if (! empty($structure->parts)) {
            foreach ($structure->parts as $index => $sub) {
                $prefix = $partNumber === '' ? (string) ($index + 1) : $partNumber . '.' . ($index + 1);
                $plain = $this->extractPart($connection, $uid, $sub, $prefix);
                if ($plain !== '') {
                    return $plain;
                }
            }
        }

        return '';
    }

    protected function decodeBody(string $body, int $encoding): string
    {
        return match ($encoding) {
            ENCBASE64 => base64_decode($body) ?: $body,
            ENCQUOTEDPRINTABLE => quoted_printable_decode($body),
            default => $body,
        };
    }
}
