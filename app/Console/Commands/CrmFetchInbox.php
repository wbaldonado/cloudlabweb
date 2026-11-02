<?php

namespace App\Console\Commands;

use App\Support\CrmImapInbox;
use Illuminate\Console\Command;

class CrmFetchInbox extends Command
{
    protected $signature = 'crm:fetch-inbox';

    protected $description = 'Poll Hostinger IMAP inbox for CRM lead replies (free, no API cost)';

    public function handle(CrmImapInbox $inbox): int
    {
        if (! config('crm.imap.enabled')) {
            $this->warn('CRM IMAP is disabled. Set CRM_IMAP_ENABLED=true in .env');

            return self::SUCCESS;
        }

        if (! extension_loaded('imap')) {
            $this->error('PHP imap extension is not enabled on this server.');

            return self::FAILURE;
        }

        $count = $inbox->fetch();
        $this->info("Processed {$count} new reply(ies).");

        return self::SUCCESS;
    }
}
