<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Deal;
use App\Support\CrmPresenter;
use Illuminate\Http\Request;

class DealController extends Controller
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
        $deal = Deal::create($this->validatedDeal($request));

        return $this->jsonOrRedirect($request, 'Oportunidad creada correctamente.', $deal);
    }

    public function show(Deal $deal)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function edit(Deal $deal)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function update(Request $request, Deal $deal)
    {
        $deal->update($this->validatedDeal($request));

        return $this->jsonOrRedirect($request, 'Oportunidad actualizada correctamente.', $deal);
    }

    public function destroy(Request $request, Deal $deal)
    {
        $deal->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Oportunidad eliminada.']);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', 'Deal deleted.');
    }

    protected function validatedDeal(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'stage' => 'required|in:prospecting,qualified,proposal,negotiation,won,lost',
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'company_id' => 'nullable|exists:crm_companies,id',
            'owner_id' => 'nullable|exists:users,id',
            'expected_close_date' => 'nullable|date',
            'description' => 'nullable|string',
            'probability' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['amount'] = $validated['amount'] ?? 0;
        $validated['probability'] = $validated['probability'] ?? 0;

        return $validated;
    }

    protected function jsonOrRedirect(Request $request, string $message, Deal $deal)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deal' => CrmPresenter::deal($deal->fresh(['company', 'contact', 'owner'])),
            ]);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', $message);
    }
}
