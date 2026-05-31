<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Contact;
use App\Models\CRM\Company;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['company', 'owner'])->latest()->paginate(15);
        return view('admin.crm.contacts.index', compact('contacts'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.crm.contacts.create', compact('companies', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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

        if (empty($validated['captured_at']) && $validated['lifecycle_stage'] === 'lead') {
            $validated['captured_at'] = now();
        }

        Contact::create($validated);

        return redirect()->route('admin.crm.contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load(['company', 'owner', 'assignedTo', 'deals']);
        return view('admin.crm.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $companies = Company::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.crm.contacts.edit', compact('contact', 'companies', 'users'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
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

        $contact->update($validated);

        return redirect()->route('admin.crm.contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.crm.contacts.index')->with('success', 'Contact deleted successfully.');
    }
}

