<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes de synchronisation à la table quiz_attempts.
     */
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('pending')->after('moodle_attempt_id');
            $table->enum('sync_action', ['create', 'update', 'delete'])->nullable()->after('sync_status');
            $table->timestamp('synced_at')->nullable()->after('sync_action');
            $table->tinyInteger('dirty')->default(0)->after('synced_at');
            
            $table->index('sync_status');
            $table->index('dirty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex(['sync_status']);
            $table->dropIndex(['dirty']);
            $table->dropColumn(['sync_status', 'sync_action', 'synced_at', 'dirty']);
        });
    }
};
