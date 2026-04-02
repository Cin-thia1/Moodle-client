<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes spécifiques aux quiz dans la table modules.
     * Les quiz partagent la même table que les devoirs (modname = 'quiz').
     * Compatible avec la structure Moodle server pour la synchronisation future.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // Dates d'ouverture et fermeture du quiz
            $table->timestamp('timeopen')->nullable()->after('duedate');
            $table->timestamp('timeclose')->nullable()->after('timeopen');

            // Durée en secondes (null = illimité) — Moodle utilise timelimit en secondes
            $table->unsignedInteger('timelimit')->nullable()->after('timeclose');

            // Nombre de tentatives autorisées (0 = illimité)
            $table->unsignedInteger('attempts')->default(1)->after('timelimit');

            // Méthode de calcul de la note finale si plusieurs tentatives
            // 0=highest, 1=average, 2=first, 3=last — même enum que Moodle
            $table->unsignedTinyInteger('grademethod')->default(0)->after('attempts');

            // Mélanger les questions et les réponses
            $table->boolean('shuffleanswers')->default(true)->after('grademethod');

            // Questions par page (0 = toutes sur une page)
            $table->unsignedInteger('questionsperpage')->default(0)->after('shuffleanswers');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn([
                'timeopen',
                'timeclose',
                'timelimit',
                'attempts',
                'grademethod',
                'shuffleanswers',
                'questionsperpage',
            ]);
        });
    }
};