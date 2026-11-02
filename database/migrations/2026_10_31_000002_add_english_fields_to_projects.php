<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        $columns = [
            'title_en' => fn (Blueprint $table) => $table->string('title_en')->nullable()->after('title'),
            'description_en' => fn (Blueprint $table) => $table->text('description_en')->nullable()->after('description'),
            'content_en' => fn (Blueprint $table) => $table->longText('content_en')->nullable()->after('content'),
            'technologies_en' => fn (Blueprint $table) => $table->string('technologies_en')->nullable()->after('technologies'),
            'meta_title_en' => fn (Blueprint $table) => $table->string('meta_title_en')->nullable(),
            'meta_description_en' => fn (Blueprint $table) => $table->string('meta_description_en', 320)->nullable(),
        ];

        $missing = array_filter(
            $columns,
            fn (string $column) => ! Schema::hasColumn('projects', $column),
            ARRAY_FILTER_USE_KEY
        );

        if ($missing === []) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) use ($missing) {
            foreach ($missing as $add) {
                $add($table);
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        $columns = array_values(array_filter([
            'title_en',
            'description_en',
            'content_en',
            'technologies_en',
            'meta_title_en',
            'meta_description_en',
        ], fn (string $column) => Schema::hasColumn('projects', $column)));

        if ($columns !== []) {
            Schema::table('projects', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
