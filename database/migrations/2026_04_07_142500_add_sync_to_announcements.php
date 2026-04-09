<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Ajouter les colonnes de synchronisation si elles n'existent pas
            if (!Schema::hasColumn('announcements', 'sync_status')) {
                $table->string('sync_status')->default('synced')->after('published_at');
            }
            if (!Schema::hasColumn('announcements', 'sync_action')) {
                $table->enum('sync_action', ['create', 'update', 'delete'])->nullable()->after('sync_status');
            }
            if (!Schema::hasColumn('announcements', 'synced_at')) {
                $table->timestamp('synced_at')->nullable()->after('sync_action');
            }
            if (!Schema::hasColumn('announcements', 'dirty')) {
                $table->boolean('dirty')->default(0)->after('synced_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_action', 'synced_at', 'dirty']);
        });
    }
};
