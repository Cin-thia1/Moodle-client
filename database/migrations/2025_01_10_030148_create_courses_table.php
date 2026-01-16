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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable();
            $table->string('fullname');
            $table->string('shortname');
            $table->text('summary')->nullable();
            $table->integer('numsections');
            $table->timestamp('startdate')->nullable();;
            $table->timestamp('enddate')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
