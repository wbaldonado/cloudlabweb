<?php

namespace App\Support;

use App\Models\CRM\Company;
use App\Models\CRM\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CrmCsvImporter
{
    public const MAX_ROWS = 25000;

    /**
     * @return list<array<string, string>>|null
     */
    public function parse(string $path): ?array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return null;
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return [];
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $firstLine = preg_replace('/^[^\wÁÉÍÓÚáéíóúÑñ]+/u', '', $firstLine) ?? $firstLine;
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $headers = str_getcsv($firstLine, $delimiter);
        $headers = array_map(fn ($h) => $this->normalizeHeader((string) $h), $headers);

        if ($headers === [] || ($headers[0] ?? '') === '') {
            fclose($handle);

            return null;
        }

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($this->rowIsEmpty($data)) {
                continue;
            }

            $row = [];
            foreach ($headers as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $row[$key] = isset($data[$i]) ? trim((string) $data[$i]) : '';
            }
            $rows[] = $this->mapChamberRow($row);
        }

        fclose($handle);

        return $rows;
    }

    public function normalizeHeader(string $header): string
    {
        $header = Str::lower(trim($header));
        $header = preg_replace('/^[^\wáéíóúñ]+/u', '', $header) ?? $header;
        $header = str_replace([' ', '-', '.'], '_', $header);
        $header = preg_replace('/_+/', '_', $header) ?? $header;

        $aliases = [
            'nombre' => 'name',
            'razon_social' => 'name',
            'razón_social' => 'name',
            'industria' => 'industry',
            'actividad' => 'industry',
            'sitio_web' => 'website',
            'web' => 'website',
            'telefono' => 'phone',
            'teléfono' => 'phone',
            'tel_com_1' => 'phone',
            'tel_com1' => 'phone',
            'tel_com_2' => 'phone2',
            'tel_com_3' => 'phone3',
            'direccion' => 'address',
            'dirección' => 'address',
            'dir_comercial' => 'address',
            'ciudad' => 'city',
            'mun_comercial' => 'municipio',
            'municipio' => 'municipio',
            'estado' => 'state',
            'departamento' => 'state',
            'pais' => 'country',
            'país' => 'country',
            'codigo_postal' => 'zip_code',
            'código_postal' => 'zip_code',
            'zip' => 'zip_code',
            'postal' => 'zip_code',
            'descripcion' => 'description',
            'descripción' => 'description',
            'matricula' => 'matricula',
            'nit' => 'nit',
            'email_comercial' => 'company_email',
            'correo_comercial' => 'company_email',
            'tam_empresa' => 'company_size',
            'tamaño_empresa' => 'company_size',
            'tamano_empresa' => 'company_size',
            'ciiu_1' => 'ciiu_1',
            'ciiu1' => 'ciiu_1',
            'nom_rep_legal' => 'rep_name',
            'nombre_rep_legal' => 'rep_name',
            'rep_legal' => 'rep_id',
            'nombre_contacto' => 'first_name',
            'primer_nombre' => 'first_name',
            'firstname' => 'first_name',
            'apellido' => 'last_name',
            'apellidos' => 'last_name',
            'lastname' => 'last_name',
            'correo' => 'email',
            'email_address' => 'email',
            'cargo' => 'job_title',
            'puesto' => 'job_title',
            'title' => 'job_title',
            'etapa' => 'lifecycle_stage',
            'stage' => 'lifecycle_stage',
            'estado_ciclo' => 'lifecycle_stage',
            'fuente' => 'lead_source',
            'origen' => 'lead_source',
            'source' => 'lead_source',
            'empresa_nombre' => 'company_name',
            'nombre_empresa' => 'company_name',
            'company' => 'company_name',
            'empresa' => 'company_name',
            'notas' => 'notes',
            'comentario' => 'notes',
            'comments' => 'notes',
        ];

        return $aliases[$header] ?? $header;
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, string>
     */
    public function mapChamberRow(array $row): array
    {
        if (! empty($row['municipio'])) {
            $mun = $row['municipio'];
            if (preg_match('/^(\d+)\s*[-–]\s*(.+)$/u', $mun, $m)) {
                if (empty($row['zip_code'])) {
                    $row['zip_code'] = $m[1];
                }
                if (empty($row['city'])) {
                    $row['city'] = trim($m[2]);
                }
            } elseif (empty($row['city'])) {
                $row['city'] = $mun;
            }
        }

        if (empty($row['country'])) {
            $row['country'] = 'Colombia';
        }

        if (empty($row['state']) && ! empty($row['city'])) {
            $city = Str::upper($row['city']);
            if (str_contains($city, 'ARMENIA') || str_contains($city, 'CALARCA') || str_contains($city, 'CALARC')
                || str_contains($city, 'MONTENEGRO') || str_contains($city, 'QUIMBAYA') || str_contains($city, 'CIRCASIA')
                || str_contains($city, 'LA TEBAIDA') || str_contains($city, 'FILANDIA')) {
                $row['state'] = 'Quindío';
            }
        }

        foreach (['phone', 'phone2', 'phone3', 'nit', 'matricula'] as $numField) {
            if (! empty($row[$numField])) {
                $row[$numField] = $this->cleanNumber($row[$numField]);
            }
        }

        if (empty($row['phone']) && ! empty($row['phone2'])) {
            $row['phone'] = $row['phone2'];
        }

        $meta = [];
        if (! empty($row['nit'])) {
            $meta[] = 'NIT: ' . $row['nit'];
        }
        if (! empty($row['matricula'])) {
            $meta[] = 'Matrícula: ' . $row['matricula'];
        }
        if (! empty($row['company_size'])) {
            $meta[] = 'Tamaño: ' . $row['company_size'];
        }
        if (! empty($row['ciiu_1'])) {
            $meta[] = 'CIIU: ' . Str::limit($row['ciiu_1'], 180);
        }
        if (! empty($row['company_email'])) {
            $meta[] = 'Email: ' . $row['company_email'];
        }
        if (! empty($row['rep_name'])) {
            $meta[] = 'Rep. legal: ' . $row['rep_name'];
        }

        if ($meta !== []) {
            $extra = implode(' | ', $meta);
            $prev = trim((string) ($row['description'] ?? ''));
            $row['description'] = $prev === '' ? $extra : $prev . "\n" . $extra;
        }

        if (! empty($row['industry'])) {
            $row['industry'] = Str::limit($row['industry'], 255, '');
        } elseif (! empty($row['ciiu_1'])) {
            $row['industry'] = Str::limit($row['ciiu_1'], 255, '');
        }

        if (! empty($row['address'])) {
            $row['address'] = Str::limit($row['address'], 255, '');
        }

        return $row;
    }

    public function cleanNumber(string $value): string
    {
        // Chamber exports often use thousand separators: 3,104,206,076 → 3104206076
        $value = str_replace([' ', "\xc2\xa0"], '', $value);
        if (preg_match('/^\d{1,3}(,\d{3})+$/', $value)) {
            return str_replace(',', '', $value);
        }

        return preg_replace('/[^\d+]/', '', $value) ?: $value;
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @return array{created:int,updated:int,skipped:int,errors:list<string>}
     */
    public function importCompanies(array $rows, bool $withContacts = false, ?int $ownerId = null): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $ownerId = $ownerId ?: (auth()->id() ? (int) auth()->id() : null);

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::transaction(function () use ($chunk, $withContacts, $ownerId, &$created, &$updated, &$skipped, &$errors) {
                foreach ($chunk as $index => $row) {
                    $line = $index + 2;
                    $payload = [
                        'name' => $row['name'] ?? '',
                        'industry' => $this->nullIfEmpty($row['industry'] ?? null),
                        'website' => $this->normalizeWebsite($row['website'] ?? null),
                        'phone' => $this->nullIfEmpty($row['phone'] ?? null),
                        'address' => $this->nullIfEmpty($row['address'] ?? null),
                        'city' => $this->nullIfEmpty($row['city'] ?? null),
                        'state' => $this->nullIfEmpty($row['state'] ?? null),
                        'country' => $this->nullIfEmpty($row['country'] ?? null) ?? 'Colombia',
                        'zip_code' => $this->nullIfEmpty($row['zip_code'] ?? null),
                        'description' => $this->nullIfEmpty($row['description'] ?? null),
                    ];

                    $validator = Validator::make($payload, [
                        'name' => 'required|string|max:255',
                        'industry' => 'nullable|string|max:255',
                        'website' => 'nullable|url|max:255',
                        'phone' => 'nullable|string|max:255',
                        'address' => 'nullable|string|max:255',
                        'city' => 'nullable|string|max:255',
                        'state' => 'nullable|string|max:255',
                        'country' => 'nullable|string|max:255',
                        'zip_code' => 'nullable|string|max:20',
                        'description' => 'nullable|string',
                    ]);

                    if ($validator->fails()) {
                        $skipped++;
                        if (count($errors) < 50) {
                            $errors[] = 'Empresa: ' . ($payload['name'] ?: 'sin nombre') . ' — ' . $validator->errors()->first();
                        }
                        continue;
                    }

                    $data = $validator->validated();
                    $existing = Company::query()
                        ->whereRaw('LOWER(name) = ?', [Str::lower($data['name'])])
                        ->first();

                    if ($existing) {
                        $existing->update($data);
                        $company = $existing;
                        $updated++;
                    } else {
                        $company = Company::create($data);
                        $created++;
                    }

                    if ($withContacts) {
                        $this->maybeCreateContactFromCompanyRow($row, $company->id, $ownerId, $created, $updated, $skipped, $errors);
                    }
                }
            });
        }

        return compact('created', 'updated', 'skipped', 'errors');
    }

    /**
     * @param  array<string, string>  $row
     * @param  list<string>  $errors
     */
    private function maybeCreateContactFromCompanyRow(
        array $row,
        int $companyId,
        ?int $ownerId,
        int &$created,
        int &$updated,
        int &$skipped,
        array &$errors
    ): void {
        $email = $this->nullIfEmpty($row['company_email'] ?? $row['email'] ?? null);
        $repName = trim((string) ($row['rep_name'] ?? ''));

        if ($email === null && $repName === '') {
            return;
        }

        $first = 'Contacto';
        $last = 'Empresa';
        if ($repName !== '') {
            $parts = preg_split('/\s+/', $repName) ?: [];
            $first = $parts[0] ?? $first;
            $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : $first;
        }

        $payload = [
            'first_name' => Str::limit($first, 255, ''),
            'last_name' => Str::limit($last, 255, ''),
            'email' => $email,
            'phone' => $this->nullIfEmpty($row['phone'] ?? null),
            'job_title' => 'Representante legal',
            'lifecycle_stage' => 'lead',
            'lead_source' => 'camara_comercio',
            'capture_method' => 'import',
            'owner_id' => $ownerId,
            'company_id' => $companyId,
            'notes' => 'Importado desde Cámara de Comercio',
            'captured_at' => now(),
        ];

        $validator = Validator::make($payload, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'lifecycle_stage' => 'required|in:lead,mql,sql,customer',
            'lead_source' => 'nullable|string|max:255',
            'capture_method' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:crm_companies,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $skipped++;
            if (count($errors) < 50) {
                $errors[] = 'Contacto de ' . ($row['name'] ?? '') . ': ' . $validator->errors()->first();
            }

            return;
        }

        $data = $validator->validated();
        $existing = null;
        if (! empty($data['email'])) {
            $existing = Contact::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower($data['email'])])
                ->first();
        }

        if ($existing) {
            unset($data['captured_at']);
            $existing->update($data);
            $updated++;
        } else {
            Contact::create($data);
            $created++;
        }
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @return array{created:int,updated:int,skipped:int,errors:list<string>}
     */
    public function importContacts(array $rows, int $ownerId): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::transaction(function () use ($chunk, $ownerId, &$created, &$updated, &$skipped, &$errors) {
                foreach ($chunk as $row) {
                    $stage = Str::lower(trim((string) ($row['lifecycle_stage'] ?? 'lead')));
                    $stageMap = [
                        'nuevo' => 'lead',
                        'lead' => 'lead',
                        'contactado' => 'mql',
                        'mql' => 'mql',
                        'calificado' => 'sql',
                        'sql' => 'sql',
                        'cliente' => 'customer',
                        'customer' => 'customer',
                    ];
                    $stage = $stageMap[$stage] ?? $stage;

                    $companyName = trim((string) ($row['company_name'] ?? ''));
                    $companyId = null;
                    if ($companyName !== '') {
                        $company = Company::query()
                            ->whereRaw('LOWER(name) = ?', [Str::lower($companyName)])
                            ->first();

                        if (! $company) {
                            $company = Company::create(['name' => $companyName, 'country' => 'Colombia']);
                        }
                        $companyId = $company->id;
                    }

                    $payload = [
                        'first_name' => $row['first_name'] ?? '',
                        'last_name' => $row['last_name'] ?? '',
                        'email' => $this->nullIfEmpty($row['email'] ?? null),
                        'phone' => $this->nullIfEmpty($row['phone'] ?? null),
                        'job_title' => $this->nullIfEmpty($row['job_title'] ?? null),
                        'lifecycle_stage' => $stage !== '' ? $stage : 'lead',
                        'lead_source' => $this->nullIfEmpty($row['lead_source'] ?? null) ?? 'csv_import',
                        'capture_method' => 'import',
                        'owner_id' => $ownerId,
                        'company_id' => $companyId,
                        'notes' => $this->nullIfEmpty($row['notes'] ?? null),
                    ];

                    $validator = Validator::make($payload, [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'nullable|email|max:255',
                        'phone' => 'nullable|string|max:255',
                        'job_title' => 'nullable|string|max:255',
                        'lifecycle_stage' => 'required|in:lead,mql,sql,customer',
                        'lead_source' => 'nullable|string|max:255',
                        'capture_method' => 'nullable|string|max:255',
                        'owner_id' => 'nullable|exists:users,id',
                        'company_id' => 'nullable|exists:crm_companies,id',
                        'notes' => 'nullable|string',
                    ]);

                    if ($validator->fails()) {
                        $skipped++;
                        if (count($errors) < 50) {
                            $errors[] = ($payload['first_name'] ?: 'fila') . ': ' . $validator->errors()->first();
                        }
                        continue;
                    }

                    $data = $validator->validated();
                    if ($data['lifecycle_stage'] === 'lead') {
                        $data['captured_at'] = now();
                    }

                    $existing = null;
                    if (! empty($data['email'])) {
                        $existing = Contact::query()
                            ->whereRaw('LOWER(email) = ?', [Str::lower($data['email'])])
                            ->first();
                    }

                    if ($existing) {
                        unset($data['captured_at']);
                        $existing->update($data);
                        $updated++;
                    } else {
                        Contact::create($data);
                        $created++;
                    }
                }
            });
        }

        return compact('created', 'updated', 'skipped', 'errors');
    }

    /**
     * @param  list<string|null>  $data
     */
    private function rowIsEmpty(array $data): bool
    {
        foreach ($data as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function nullIfEmpty(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeWebsite(?string $value): ?string
    {
        $value = $this->nullIfEmpty($value);
        if ($value === null) {
            return null;
        }

        if (! preg_match('#^https?://#i', $value)) {
            $value = 'https://' . $value;
        }

        return $value;
    }
}
