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
        Schema::create('user_competencies', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable()->comment('Identifiant unique de Moodle');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->integer('proficiency')->default(0)->comment('0=incomplete, 1=complete');
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'competency_id']);
            $table->index('moodle_id');
            $table->index('proficiency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_competencies');
    }
};
