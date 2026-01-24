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
        Schema::create('grade_items', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique()->nullable()->comment('Identifiant unique de Moodle');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('item_name')->comment('Nom du critère d\'évaluation');
            $table->string('item_type')->comment('assignment, quiz, forum, etc.');
            $table->decimal('grade_max', 10, 2)->default(100)->comment('Note maximale');
            $table->integer('sort_order')->nullable();
            $table->integer('status')->default(1)->comment('1=visible, 0=hidden');
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
        Schema::dropIfExists('grade_items');
    }
};
