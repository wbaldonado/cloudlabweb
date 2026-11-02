<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\CampaignRecipient;
use App\Models\CRM\Contact;
use App\Models\CRM\Company;
use App\Support\CrmPresenter;
use Illuminate\Http\Request;

class ContactController extends Controller
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
        if ($request->boolean('_bulk')) {
            return $this->bulkDestroy($request);
        }

        $validated = $this->validatedContact($request);

        if (empty($validated['captured_at']) && $validated['lifecycle_stage'] === 'lead') {
            $validated['captured_at'] = now();
        }

        $contact = Contact::create($validated);

        return $this->jsonOrRedirect($request, 'Contacto creado correctamente.', $contact);
    }

    public function show(Contact $contact)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function edit(Contact $contact)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function update(Request $request, Contact $contact)
    {
        $contact->update($this->validatedContact($request));

        return $this->jsonOrRedirect($request, 'Contacto actualizado correctamente.', $contact);
    }

    /**
     * Campaign history for one contact (JSON for the dashboard modal).
     */
    public function campaigns(Contact $contact)
    {
        try {
            $recipients = CampaignRecipient::query()
                ->with('campaign:id,name,subject,status,sent_at')
                ->where('contact_id', $contact->id)
                ->orderByRaw('sent_at IS NULL')
                ->orderByDesc('sent_at')
                ->orderByDesc('id')
                ->get();
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'La tabla de campañas no está disponible.',
            ], 500);
        }

        $items = $recipients->map(function (CampaignRecipient $r) {
            return [
                'campaign_id' => $r->campaign_id,
                'campaign' => $r->campaign?->name ?? 'Campaña eliminada',
                'subject' => $r->campaign?->subject,
                'campaign_url' => $r->campaign ? route('admin.crm.campaigns.show', $r->campaign) : null,
                'email' => $r->email,
                'status' => $r->status,
                'status_label' => $r->statusLabel(),
                'status_class' => $r->statusBadgeClass(),
                'sent_at' => $r->sent_at?->format('d/m/Y H:i'),
                'opened_at' => $r->opened_at?->format('d/m/Y H:i'),
                'open_count' => (int) $r->open_count,
                'clicked_at' => $r->clicked_at?->format('d/m/Y H:i'),
                'click_count' => (int) $r->click_count,
                'error' => $r->error,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'contact' => [
                'id' => $contact->id,
                'name' => $contact->full_name,
                'email' => $contact->email,
            ],
            'summary' => [
                'total' => $items->count(),
                'sent' => $items->where('status', 'sent')->count(),
                'opened' => $items->whereNotNull('opened_at')->count(),
                'clicked' => $items->whereNotNull('clicked_at')->count(),
            ],
            'items' => $items,
        ]);
    }

    public function destroy(Request $request, Contact $contact)
    {
        $contact->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Contacto eliminado.']);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', 'Contact deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $all = $request->boolean('all');
        $ids = array_values(array_unique(array_map('intval', (array) $request->input('ids', []))));

        if (! $all && $ids === []) {
            $message = 'Selecciona al menos un contacto para eliminar.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->route('admin.crm.dashboard')->with('error', $message);
        }

        if ($all) {
            $deleted = Contact::query()
                ->filtered($request->input('q'), $request->input('stage'))
                ->delete();
        } else {
            $deleted = Contact::whereIn('id', $ids)->delete();
        }

        $message = $deleted === 1 ? '1 contacto eliminado.' : "{$deleted} contactos eliminados.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'deleted' => $deleted,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', $message);
    }

    protected function validatedContact(Request $request): array
    {
        return $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'lifecycle_stage' => 'required|in:lead,mql,sql,customer',
            'lead_source' => 'nullable|string|max:255',
            'lead_score' => 'nullable|integer|min:0|max:100',
            'capture_method' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:crm_companies,id',
            'notes' => 'nullable|string',
        ]);
    }

    protected function jsonOrRedirect(Request $request, string $message, Contact $contact)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'contact' => CrmPresenter::contact($contact->fresh(['company', 'owner'])),
            ]);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', $message);
    }
}
