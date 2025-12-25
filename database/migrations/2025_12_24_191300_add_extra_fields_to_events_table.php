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
        Schema::table('events', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
            $table->string('location')->nullable()->after('description');
            $table->enum('duration_type', ['none', 'until', 'minutes'])->default('none')->after('location');
            $table->dateTime('end_date')->nullable()->after('duration_type');
            $table->integer('duration_minutes')->nullable()->after('end_date');
            $table->boolean('repeat_event')->default(false)->after('duration_minutes');
            $table->integer('repeat_count')->nullable()->after('repeat_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'location',
                'duration_type',
                'end_date',
                'duration_minutes',
                'repeat_event',
                'repeat_count'
            ]);
        });
    }
};