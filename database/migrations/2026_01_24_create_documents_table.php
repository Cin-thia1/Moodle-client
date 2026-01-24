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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable()->comment('Identifiant unique de Moodle (file id)');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('Créateur du document');
            $table->string('filename');
            $table->string('filepath')->comment('Chemin virtuel dans Moodle');
            $table->string('mimetype')->nullable();
            $table->bigInteger('filesize')->default(0);
            $table->string('file_url')->nullable();
            $table->integer('status')->default(1)->comment('1=visible, 0=hidden');
            $table->timestamp('file_date')->nullable();
            $table->timestamps();
            
            $table->index(['course_id', 'status']);
            $table->index('moodle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
