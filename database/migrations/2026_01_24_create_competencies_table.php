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
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable()->comment('Identifiant unique de Moodle');
            $table->string('shortname')->unique();
            $table->string('idnumber')->nullable()->comment('Numéro d\'identité personnalisé');
            $table->string('description')->nullable();
            $table->text('description_long')->nullable();
            $table->integer('status')->default(1)->comment('1=active, 0=archived');
            $table->timestamps();
            
            $table->index('moodle_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies');
    }
};
