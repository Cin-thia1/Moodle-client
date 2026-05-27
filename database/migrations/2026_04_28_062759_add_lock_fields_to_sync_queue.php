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
        Schema::table('sync_queue', function (Blueprint $table) {
            $table->string('processed_by')->nullable()->after('status');
            $table->timestamp('locked_at')->nullable()->after('processed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_queue', function (Blueprint $table) {
            $table->dropColumn(['processed_by', 'locked_at']);
        });
    }
};
