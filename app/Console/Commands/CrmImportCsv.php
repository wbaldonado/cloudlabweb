<?php

namespace App\Console\Commands;

use App\Support\CrmCsvImporter;
use Illuminate\Console\Command;

class CrmImportCsv extends Command
{
    protected $signature = 'crm:import-csv
        {path : Ruta al archivo CSV}
        {--type=companies : companies|contacts}
        {--with-contacts : Al importar empresas, crear contactos con email/rep. legal}
        {--owner= : ID de usuario owner para contactos}';

    protected $description = 'Importa empresas o contactos CRM desde un CSV (incluye formato Cámara de Comercio)';

    public function handle(CrmCsvImporter $importer): int
    {
        $path = $this->argument('path');
        if (! is_file($path)) {
            $this->error("No existe el archivo: {$path}");

            return self::FAILURE;
        }

        @set_time_limit(0);
        $this->info('Leyendo CSV…');
        $rows = $importer->parse($path);
        if ($rows === null) {
            $this->error('No se pudo leer el CSV.');

            return self::FAILURE;
        }

        $this->info('Filas: ' . count($rows));
        $type = strtolower((string) $this->option('type'));
        $owner = $this->option('owner') ? (int) $this->option('owner') : (int) (\App\Models\User::query()->value('id') ?: 0);

        if ($type === 'companies') {
            $result = $importer->importCompanies($rows, (bool) $this->option('with-contacts'), $owner ?: null);
        } elseif ($type === 'contacts') {
            if (! $owner) {
                $this->error('Se requiere un usuario (--owner=ID) para importar contactos.');

                return self::FAILURE;
            }
            $result = $importer->importContacts($rows, $owner);
        } else {
            $this->error('Tipo inválido. Use companies o contacts.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Listo: %d creados, %d actualizados, %d omitidos.',
            $result['created'],
            $result['updated'],
            $result['skipped']
        ));

        foreach (array_slice($result['errors'], 0, 20) as $err) {
            $this->warn($err);
        }

        return self::SUCCESS;
    }
}
