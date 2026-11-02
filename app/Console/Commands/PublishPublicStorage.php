<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class PublishPublicStorage extends Command
{
    protected $signature = 'storage:publish';

    protected $description = 'Copy storage/app/public to public/storage (for Hostinger when symlink fails)';

    public function handle(): int
    {
        $source = storage_path('app/public');
        $target = public_path('storage');

        if (! is_dir($source)) {
            $this->error('Source not found: storage/app/public');

            return self::FAILURE;
        }

        if (! is_dir($target) && ! mkdir($target, 0755, true) && ! is_dir($target)) {
            $this->error('Could not create: public/storage');

            return self::FAILURE;
        }

        $copied = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            $relative = substr($item->getPathname(), strlen($source) + 1);
            $dest = $target . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                if (! is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                continue;
            }

            if (! is_dir(dirname($dest))) {
                mkdir(dirname($dest), 0755, true);
            }

            if (! is_file($dest) || filemtime($item->getPathname()) > filemtime($dest)) {
                copy($item->getPathname(), $dest);
                $copied++;
            }
        }

        $this->info("Published {$copied} file(s) to public/storage.");
        $this->line('Run this after uploads if php artisan storage:link does not work on your host.');

        return self::SUCCESS;
    }
}
