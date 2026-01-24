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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_enrolment_id')->unique()->nullable()->comment('Identifiant unique de l\'enrôlement Moodle');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->comment('ROLE_TEACHER, ROLE_STUDENT, ROLE_USER');
            $table->integer('status')->default(1)->comment('1=active, 0=suspended');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('unenrolled_at')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'user_id']);
            $table->index('moodle_enrolment_id');
            $table->index(['course_id', 'role', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
