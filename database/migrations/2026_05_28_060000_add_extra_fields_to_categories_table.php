<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les champs Moodle enrichis à la table categories :
     * parent_id, idnumber, description, descriptionformat.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('moodle_id');
            $table->string('idnumber', 100)->nullable()->after('name');
            $table->text('description')->nullable()->after('idnumber');
            $table->tinyInteger('descriptionformat')->default(1)->after('description');

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'idnumber', 'description', 'descriptionformat']);
        });
    }
};
