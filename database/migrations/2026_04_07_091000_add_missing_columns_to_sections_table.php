<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes manquantes à la table sections.
     * summary: Description de la section
     * position: Ordre d'affichage
     * visible: Visibilité (1=visible, 0=hidden)
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('name');
            $table->integer('position')->default(0)->after('summary');
            $table->tinyInteger('visible')->default(1)->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['summary', 'position', 'visible']);
        });
    }
};
