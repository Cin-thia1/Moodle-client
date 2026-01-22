<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedInteger('grade')->nullable()->after('file_path');
            $table->timestamp('graded_at')->nullable()->after('grade');
            $table->unsignedBigInteger('graded_by')->nullable()->after('graded_at');

            $table->index('graded_by');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['grade', 'graded_at', 'graded_by']);
        });
    }
};
