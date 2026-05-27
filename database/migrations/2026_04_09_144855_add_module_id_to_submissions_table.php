<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On vérifie si la colonne 'module_id' n'existe PAS encore
        if (!Schema::hasColumn('submissions', 'module_id')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->foreignId('module_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('modules')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // On vérifie si la colonne existe avant de tenter de la supprimer
        if (Schema::hasColumn('submissions', 'module_id')) {
            Schema::table('submissions', function (Blueprint $table) {
                // Important : Supprimer la clé étrangère d'abord, puis la colonne
                $table->dropForeign(['module_id']);
                $table->dropColumn('module_id');
            });
        }
    }
};