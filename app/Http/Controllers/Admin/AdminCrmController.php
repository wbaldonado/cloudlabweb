<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CRM\DashboardController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Backward compatibility for older routes / cached route files
 * that reference AdminCrmController instead of CRM\DashboardController.
 */
class AdminCrmController extends Controller
{
    public function index(Request $request)
    {
        return app(DashboardController::class)->index($request);
    }

    public function campaigns()
    {
        try {
            $controller = \App\Http\Controllers\Admin\CRM\CampaignController::class;
            if (! class_exists($controller)) {
                return view('admin.crm.setup', [
                    'missingTables' => ['crm_campaigns', 'crm_campaign_recipients', 'crm_campaign_events'],
                    'sqlFile' => 'database/sql/crm_campaigns.sql',
                ]);
            }

            return app($controller)->index();
        } catch (\Throwable $e) {
            Log::warning('CRM campaigns page failed', ['error' => $e->getMessage()]);

            return view('admin.crm.setup', [
                'missingTables' => ['crm_campaigns', 'crm_campaign_recipients', 'crm_campaign_events'],
                'sqlFile' => 'database/sql/crm_campaigns.sql',
            ]);
        }
    }

    public function campaignsCreate()
    {
        try {
            $controller = \App\Http\Controllers\Admin\CRM\CampaignController::class;
            if (! class_exists($controller)) {
                return view('admin.crm.setup', [
                    'missingTables' => ['crm_campaigns', 'crm_campaign_recipients', 'crm_campaign_events'],
                    'sqlFile' => 'database/sql/crm_campaigns.sql',
                ]);
            }

            return app($controller)->create();
        } catch (\Throwable $e) {
            Log::warning('CRM campaigns create failed', ['error' => $e->getMessage()]);

            return view('admin.crm.setup', [
                'missingTables' => ['crm_campaigns', 'crm_campaign_recipients', 'crm_campaign_events'],
                'sqlFile' => 'database/sql/crm_campaigns.sql',
            ]);
        }
    }
}
