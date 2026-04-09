<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Marquer les courses/sections/modules pullés de Moodle (moodle_id non-null + pas dirtyés) comme synced
     */
    public function up(): void
    {
        // Courses pullés du serveur (moodle_id non null, dirty=0) → synced
        DB::table('courses')
            ->whereNotNull('moodle_id')
            ->where('dirty', 0)
            ->where('sync_status', '!=', 'synced')
            ->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);

        // Sections pullées (via getCourseContents)
        DB::table('sections')
            ->whereNotNull('moodle_id')
            ->where('dirty', 0)
            ->where('sync_status', '!=', 'synced')
            ->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);

        // Modules pullés
        DB::table('modules')
            ->whereNotNull('moodle_id')
            ->where('dirty', 0)
            ->where('sync_status', '!=', 'synced')
            ->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);

        // Categories pullées
        DB::table('categories')
            ->whereNotNull('moodle_id')
            ->where('dirty', 0)
            ->where('sync_status', '!=', 'synced')
            ->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);

        // Participants pullés
        DB::table('participants')
            ->whereNotNull('moodle_enrolment_id')
            ->where('dirty', 0)
            ->where('sync_status', '!=', 'synced')
            ->update([
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback nécessaire pour ce fix de data
    }
};
