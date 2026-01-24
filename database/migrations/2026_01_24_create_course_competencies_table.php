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
        Schema::create('course_competencies', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable()->comment('Identifiant unique de Moodle');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'competency_id']);
            $table->index('moodle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_competencies');
    }
};
