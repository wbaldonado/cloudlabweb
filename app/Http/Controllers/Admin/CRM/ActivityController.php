<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Activity;
use App\Models\CRM\Company;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Support\CrmPresenter;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function create()
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function store(Request $request)
    {
        $activity = Activity::create($this->validated($request));

        return $this->jsonOrRedirect($request, 'Actividad creada correctamente.', $activity);
    }

    public function show(Activity $activity)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function edit(Activity $activity)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function update(Request $request, Activity $activity)
    {
        $activity->update($this->validated($request));

        return $this->jsonOrRedirect($request, 'Actividad actualizada correctamente.', $activity);
    }

    public function destroy(Request $request, Activity $activity)
    {
        $activity->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Actividad eliminada.']);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', 'Activity deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:email,meeting,task,call',
            'status' => 'required|in:not_started,in_progress,blocked,completed',
            'due_date' => 'nullable|date',
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'company_id' => 'nullable|exists:crm_companies,id',
            'deal_id' => 'nullable|exists:crm_deals,id',
            'owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
    }

    protected function jsonOrRedirect(Request $request, string $message, Activity $activity)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'activity' => CrmPresenter::activity($activity->fresh(['company', 'contact'])),
            ]);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', $message);
    }
}
