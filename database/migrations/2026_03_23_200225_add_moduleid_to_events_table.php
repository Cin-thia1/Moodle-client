<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id')->nullable()->after('course_id');
            $table->foreign('module_id')
                  ->references('id')
                  ->on('modules')
                  ->onDelete('cascade'); // suppression automatique si le module est supprimé
        });
    }
 
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });
    }
};