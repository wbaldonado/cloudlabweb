<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_email_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('direction', ['outbound', 'inbound']);
            $table->string('subject');
            $table->longText('body');
            $table->string('from_email');
            $table->string('to_email');
            $table->string('message_id')->nullable()->unique();
            $table->string('in_reply_to')->nullable();
            $table->string('imap_uid')->nullable()->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'direction']);
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_email_messages');
    }
};
