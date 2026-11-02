<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Activity;
use App\Models\CRM\CampaignRecipient;
use App\Models\CRM\Company;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\EmailMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (! $this->crmTablesReady()) {
            return view('admin.crm.setup', [
                'missingTables' => $this->missingCrmTables(),
            ]);
        }

        $search = $request->string('q')->trim();
        $stageFilter = $request->string('stage')->trim();

        $contactsQuery = Contact::with(['company', 'owner'])
            ->filtered($search, $stageFilter)
            ->latest();

        $stats = [
            'contacts' => Contact::count(),
            'companies' => Company::count(),
            'pending_activities' => $this->pendingActivitiesCount(),
            'total_payments' => (float) Deal::where('stage', 'won')->sum('amount'),
            'pipeline_total' => (float) Deal::whereNotIn('stage', ['won', 'lost'])->sum('amount'),
            'pipeline_weighted' => (float) Deal::whereNotIn('stage', ['won', 'lost'])
                ->selectRaw('COALESCE(SUM(amount * probability / 100), 0) as weighted')
                ->value('weighted'),
            'email_replies' => $this->hasTable('crm_email_messages')
                ? EmailMessage::where('direction', 'inbound')->whereNull('read_at')->count()
                : 0,
        ];

        $recentReplies = $this->hasTable('crm_email_messages')
            ? EmailMessage::with('contact')->where('direction', 'inbound')->latest()->take(5)->get()
            : collect();

        $contacts = $contactsQuery->paginate(10)->withQueryString();
        $campaignStats = $this->campaignStatsFor($contacts->pluck('id')->all());

        return view('admin.crm.dashboard', [
            'stats' => $stats,
            'recentReplies' => $recentReplies,
            'contacts' => $contacts,
            'campaignStats' => $campaignStats,
            'companies' => Company::orderBy('name')->paginate(15, ['*'], 'companies_page')->withQueryString(),
            'deals' => Deal::with(['company', 'contact', 'owner'])->orderByDesc('updated_at')->get(),
            'activities' => $this->activitiesTableExists()
                ? Activity::with(['company', 'contact'])->orderBy('due_date')->get()
                : collect(),
            'companiesList' => Company::orderBy('name')->get(),
            'contactsList' => Contact::orderBy('first_name')->get(),
            'users' => User::orderBy('name')->get(),
            'search' => $search,
            'stageFilter' => $stageFilter,
        ]);
    }

    /**
     * Campaign counters for the contacts shown on the current page, keyed by contact id.
     *
     * @param  array<int, int>  $contactIds
     */
    protected function campaignStatsFor(array $contactIds): array
    {
        if ($contactIds === [] || ! $this->hasTable('crm_campaign_recipients')) {
            return [];
        }

        try {
            return CampaignRecipient::query()
                ->whereIn('contact_id', $contactIds)
                ->selectRaw('contact_id')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent")
                ->selectRaw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened')
                ->selectRaw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked')
                ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed")
                ->groupBy('contact_id')
                ->get()
                ->mapWithKeys(fn ($row) => [(int) $row->contact_id => [
                    'total' => (int) $row->total,
                    'sent' => (int) $row->sent,
                    'opened' => (int) $row->opened,
                    'clicked' => (int) $row->clicked,
                    'failed' => (int) $row->failed,
                ]])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    protected function crmTablesReady(): bool
    {
        return empty($this->missingCrmTables());
    }

    protected function missingCrmTables(): array
    {
        $required = ['crm_companies', 'crm_contacts', 'crm_deals'];

        return array_values(array_filter($required, fn ($table) => ! $this->hasTable($table)));
    }

    protected function pendingActivitiesCount(): int
    {
        if (! $this->activitiesTableExists()) {
            return 0;
        }

        return Activity::where('status', '!=', 'completed')->count();
    }

    protected function activitiesTableExists(): bool
    {
        return $this->hasTable('crm_activities');
    }

    protected function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
