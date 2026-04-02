<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tentatives de quiz par élève.
     * Alignée avec mdl_quiz_attempts de Moodle.
     */
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('module_id')
                  ->constrained('modules')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Numéro de tentative (1, 2, 3…)
            $table->unsignedInteger('attempt')->default(1);

            // Statut : inprogress, finished, abandoned
            $table->string('state', 20)->default('inprogress');

            // Note obtenue (null si pas encore terminé)
            $table->decimal('sumgrades', 10, 5)->nullable();

            $table->timestamp('timestart')->nullable();
            $table->timestamp('timefinish')->nullable();

            // Pour synchro Moodle future
            $table->unsignedBigInteger('moodle_attempt_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};