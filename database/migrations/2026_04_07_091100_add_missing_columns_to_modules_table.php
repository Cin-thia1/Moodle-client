<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes manquantes à la table modules.
     * position: Ordre d'affichage dans la section
     * visible: Visibilité (1=visible, 0=hidden)
     * completion: Statut de complétude (0=non requis, 1=requis, 2=manuel)
     * 
     * Note: 'intro' existe déjà (ajouté par migration 2025_06_14_014532)
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('modplural');
            $table->tinyInteger('visible')->default(1)->after('position');
            $table->tinyInteger('completion')->default(0)->after('visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['intro', 'position', 'visible', 'completion']);
        });
    }
};
