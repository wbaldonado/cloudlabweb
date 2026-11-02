<?php

namespace App\Jobs;

use App\Models\CRM\Campaign;
use App\Support\CampaignSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queue worker for campaign sends (Redis / Horizon / database).
 * On Hostinger (QUEUE_CONNECTION=sync) CampaignController runs CampaignSender directly.
 */
class SendCampaignBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 240;

    public int $tries = 1;

    public function __construct(
        public int $campaignId,
        public int $limit = 50,
    ) {
        $this->onQueue('campaigns');
    }

    public function handle(CampaignSender $sender): array
    {
        $campaign = Campaign::query()->find($this->campaignId);
        if (! $campaign) {
            return [
                'ok' => false,
                'message' => 'Campaña no encontrada.',
                'sent' => 0,
                'failed' => 0,
                'remaining' => 0,
            ];
        }

        return $sender->sendBatch($campaign, $this->limit);
    }
}
