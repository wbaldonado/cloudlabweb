<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Deal;
use App\Models\CRM\Contact;
use App\Models\CRM\Company;
use App\Models\User;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::with(['contact', 'company', 'owner'])->latest()->paginate(15);
        return view('admin.crm.deals.index', compact('deals'));
    }

    public function create()
    {
        $contacts = Contact::orderBy('first_name')->get();
        $companies = Company::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.crm.deals.create', compact('contacts', 'companies', 'users'));
    }

    public function store(Request $request)
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

        Deal::create($validated);

        return redirect()->route('admin.crm.deals.index')->with('success', 'Deal created successfully.');
    }

    public function show(Deal $deal)
    {
        $deal->load(['contact', 'company', 'owner']);
        return view('admin.crm.deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        $contacts = Contact::orderBy('first_name')->get();
        $companies = Company::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.crm.deals.edit', compact('deal', 'contacts', 'companies', 'users'));
    }

    public function update(Request $request, Deal $deal)
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

        $deal->update($validated);

        return redirect()->route('admin.crm.deals.index')->with('success', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();

        return redirect()->route('admin.crm.deals.index')->with('success', 'Deal deleted successfully.');
    }
}

