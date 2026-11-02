<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Company;
use App\Support\CrmPresenter;
use Illuminate\Http\Request;

class CompanyController extends Controller
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

        $company = Company::create($this->validatedCompany($request));

        return $this->jsonOrRedirect($request, 'Empresa creada correctamente.', $company);
    }

    public function show(Company $company)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function edit(Company $company)
    {
        return redirect()->route('admin.crm.dashboard');
    }

    public function update(Request $request, Company $company)
    {
        $company->update($this->validatedCompany($request));

        return $this->jsonOrRedirect($request, 'Empresa actualizada correctamente.', $company);
    }

    public function destroy(Request $request, Company $company)
    {
        $company->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Empresa eliminada.']);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', 'Company deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $all = $request->boolean('all');
        $ids = array_values(array_unique(array_map('intval', (array) $request->input('ids', []))));

        if (! $all && $ids === []) {
            $message = 'Selecciona al menos una empresa para eliminar.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->route('admin.crm.dashboard')->with('error', $message);
        }

        if ($all) {
            $deleted = Company::query()->delete();
        } else {
            $deleted = Company::whereIn('id', $ids)->delete();
        }

        $message = $deleted === 1 ? '1 empresa eliminada.' : "{$deleted} empresas eliminadas.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'deleted' => $deleted,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', $message);
    }

    protected function validatedCompany(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
        ]);
    }

    protected function jsonOrRedirect(Request $request, string $message, Company $company)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'company' => CrmPresenter::company($company->fresh()),
            ]);
        }

        return redirect()->route('admin.crm.dashboard')->with('success', $message);
    }
}
