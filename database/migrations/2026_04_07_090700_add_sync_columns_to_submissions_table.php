<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'sync_status')) {
                $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('pending')->after('submitted_at');
                $table->index('sync_status');
            }
            if (!Schema::hasColumn('submissions', 'sync_action')) {
                $table->enum('sync_action', ['create', 'update', 'delete'])->nullable()->after('sync_status');
            }
            if (!Schema::hasColumn('submissions', 'synced_at')) {
                $table->timestamp('synced_at')->nullable()->after('sync_action');
            }
            if (!Schema::hasColumn('submissions', 'dirty')) {
                $table->tinyInteger('dirty')->default(0)->after('synced_at');
                $table->index('dirty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'sync_status')) {
                $table->dropIndex(['sync_status']);
                $table->dropColumn('sync_status');
            }
            if (Schema::hasColumn('submissions', 'sync_action')) {
                $table->dropColumn('sync_action');
            }
            if (Schema::hasColumn('submissions', 'synced_at')) {
                $table->dropColumn('synced_at');
            }
            if (Schema::hasColumn('submissions', 'dirty')) {
                $table->dropIndex(['dirty']);
                $table->dropColumn('dirty');
            }
        });
    }
};