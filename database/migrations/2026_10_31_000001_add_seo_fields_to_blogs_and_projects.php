<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('blogs') && ! Schema::hasColumn('blogs', 'meta_title')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->string('meta_title')->nullable()->after('excerpt');
                $table->string('meta_description', 320)->nullable()->after('meta_title');
            });
        }

        if (Schema::hasTable('projects') && ! Schema::hasColumn('projects', 'meta_title')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('meta_title')->nullable()->after('description');
                $table->string('meta_description', 320)->nullable()->after('meta_title');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('blogs') && Schema::hasColumn('blogs', 'meta_title')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn(['meta_title', 'meta_description']);
            });
        }

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'meta_title')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn(['meta_title', 'meta_description']);
            });
        }
    }
};
