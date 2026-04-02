<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Réponses données par chaque élève pour chaque question d'une tentative.
     */
    public function up(): void
    {
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attempt_id')
                  ->constrained('quiz_attempts')
                  ->onDelete('cascade');

            $table->foreignId('question_id')
                  ->constrained('quiz_questions')
                  ->onDelete('cascade');

            // Réponse choisie (null = pas encore répondu)
            $table->foreignId('answer_id')
                  ->nullable()
                  ->constrained('quiz_answers')
                  ->onDelete('set null');

            // Points obtenus pour cette question
            $table->decimal('fraction', 10, 7)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
    }
};