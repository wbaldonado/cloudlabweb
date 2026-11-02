<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Support\CrmCsvImporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function template(string $type): StreamedResponse
    {
        $type = strtolower($type);

        if ($type === 'companies') {
            $filename = 'crm-empresas-plantilla.csv';
            $headers = ['name', 'industry', 'website', 'phone', 'address', 'city', 'state', 'country', 'zip_code', 'description'];
            $sample = ['Acme Corp', 'Technology', 'https://acme.com', '+57 300 000 0000', 'Calle 100 #10-20', 'Bogota', 'Cundinamarca', 'Colombia', '110111', 'Cliente potencial'];
        } elseif ($type === 'contacts') {
            $filename = 'crm-contactos-plantilla.csv';
            $headers = ['first_name', 'last_name', 'email', 'phone', 'job_title', 'lifecycle_stage', 'lead_source', 'company_name', 'notes'];
            $sample = ['Juan', 'Perez', 'juan.perez@acme.com', '+57 300 111 2233', 'CEO', 'lead', 'referral', 'Acme Corp', 'Importado desde CSV'];
        } else {
            abort(404);
        }

        return response()->streamDownload(function () use ($headers, $sample) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, $headers);
            fputcsv($out, $sample);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function store(Request $request, CrmCsvImporter $importer)
    {
        @set_time_limit(300);

        $request->validate([
            'type' => 'required|in:companies,contacts',
            'with_contacts' => 'nullable|boolean',
            'csv_file' => [
                'required',
                'file',
                'max:15360',
                function (string $attribute, $value, $fail) {
                    $ext = strtolower((string) $value->getClientOriginalExtension());
                    if (! in_array($ext, ['csv', 'txt'], true)) {
                        $fail('El archivo debe ser .csv o .txt');
                    }
                },
            ],
        ]);

        $type = $request->input('type');
        $withContacts = $request->boolean('with_contacts');
        $path = $request->file('csv_file')->getRealPath();
        $rows = $importer->parse($path);

        if ($rows === null) {
            return $this->fail($request, 'No se pudo leer el archivo CSV. Use UTF-8 y separador coma o punto y coma.');
        }

        if (count($rows) === 0) {
            return $this->fail($request, 'El archivo no tiene filas de datos.');
        }

        if (count($rows) > CrmCsvImporter::MAX_ROWS) {
            return $this->fail($request, 'El archivo supera el máximo de ' . CrmCsvImporter::MAX_ROWS . ' filas.');
        }

        $result = $type === 'companies'
            ? $importer->importCompanies($rows, $withContacts, (int) auth()->id())
            : $importer->importContacts($rows, (int) auth()->id());

        $message = sprintf(
            'Importación completada: %d creados, %d actualizados, %d omitidos.',
            $result['created'],
            $result['updated'],
            $result['skipped']
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'created' => $result['created'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors'],
            ]);
        }

        return redirect()
            ->route('admin.crm.dashboard')
            ->with('success', $message)
            ->with('import_errors', $result['errors']);
    }

    private function fail(Request $request, string $message)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return redirect()->route('admin.crm.dashboard')->with('error', $message);
    }
}
