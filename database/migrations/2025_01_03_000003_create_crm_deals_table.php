<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('stage', ['prospecting', 'qualified', 'proposal', 'negotiation', 'won', 'lost'])->default('prospecting');
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('crm_companies')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('expected_close_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('probability')->default(0)->comment('Success probability 0-100');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};

