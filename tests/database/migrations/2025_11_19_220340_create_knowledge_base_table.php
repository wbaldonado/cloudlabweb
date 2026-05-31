<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->text('question_es')->nullable();
            $table->text('question_en')->nullable();
            $table->text('answer_es');
            $table->text('answer_en');
            $table->json('keywords_es')->nullable(); // Array of keywords in Spanish
            $table->json('keywords_en')->nullable(); // Array of keywords in English
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_base');
    }
};
