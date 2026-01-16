<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('content');                    // question text
            $table->string('type')->default('text');    // text, multiple_choice, true_false, etc.
            $table->json('options')->nullable();        // for QCM/multiple choice
            $table->text('answer')->nullable();         // correct answer or explanation
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};