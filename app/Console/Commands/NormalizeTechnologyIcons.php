<?php

namespace App\Console\Commands;

use App\Models\Technology;
use App\Support\PublicMedia;
use Illuminate\Console\Command;

class NormalizeTechnologyIcons extends Command
{
    protected $signature = 'technologies:normalize-icons';

    protected $description = 'Fix technology icon_path values to match files in public/images/technologies';

    public function handle(): int
    {
        $updated = 0;
        $missing = 0;

        foreach (Technology::orderBy('id')->get() as $technology) {
            if (! $technology->icon_path) {
                $this->warn("No icon_path for: {$technology->name} (id {$technology->id})");
                $missing++;

                continue;
            }

            if (PublicMedia::resolveWebPath($technology->icon_path)) {
                $this->line("OK: {$technology->name} → {$technology->icon_path}");

                continue;
            }

            $basename = basename(str_replace('\\', '/', $technology->icon_path));
            $newPath = 'images/technologies/' . $basename;

            if (is_file(public_path($newPath))) {
                $technology->update(['icon_path' => $newPath]);
                $this->info("Fixed: {$technology->name} → {$newPath}");
                $updated++;

                continue;
            }

            $this->error("File not found for: {$technology->name} (icon_path: {$technology->icon_path})");
            $missing++;
        }

        $this->newLine();
        $this->info("Updated: {$updated}, missing: {$missing}");

        return self::SUCCESS;
    }
}
