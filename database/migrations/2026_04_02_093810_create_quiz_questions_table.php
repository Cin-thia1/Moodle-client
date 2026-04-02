<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des questions de quiz.
     * Structure alignée avec mdl_question de Moodle pour la synchro future.
     */
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();

            // Lien vers le module quiz
            $table->foreignId('module_id')
                  ->constrained('modules')
                  ->onDelete('cascade');

            // Type : 'multichoice' ou 'truefalse' (noms Moodle)
            $table->string('qtype', 50)->default('multichoice');

            // Énoncé de la question
            $table->text('questiontext');

            // Points pour cette question
            $table->unsignedInteger('defaultmark')->default(1);

            // Ordre d'affichage
            $table->unsignedInteger('slot')->default(1);

            // Pour synchro Moodle future
            $table->unsignedBigInteger('moodle_question_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};