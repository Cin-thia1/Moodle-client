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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable()->comment('Identifiant unique de Moodle');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Auteur');
            $table->string('subject');
            $table->longText('message');
            $table->integer('status')->default(1)->comment('1=visible, 0=hidden');
            $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('announcements');
    }
};
