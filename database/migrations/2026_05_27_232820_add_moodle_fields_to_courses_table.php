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
        Schema::table('courses', function (Blueprint $table) {
            // Général
            $table->boolean('visible')->default(true);
            $table->string('idnumber')->nullable();

            // Format du cours
            $table->string('format')->default('topics');
            $table->tinyInteger('hiddensections')->default(0);
            $table->tinyInteger('coursedisplay')->default(0);

            // Apparence
            $table->string('lang')->nullable();
            $table->integer('newsitems')->default(5);
            $table->boolean('showgrades')->default(true);
            $table->boolean('showreports')->default(false);
            $table->boolean('showactivitydates')->default(true);

            // Fichiers
            $table->bigInteger('maxbytes')->default(0);

            // Suivi d'achèvement
            $table->boolean('enablecompletion')->default(true);
            $table->boolean('showcompletionconditions')->default(true);

            // Groupes
            $table->tinyInteger('groupmode')->default(0);
            $table->boolean('groupmodeforce')->default(false);
            $table->integer('defaultgroupingid')->default(0);

            // Tags
            $table->text('tags')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'visible', 'idnumber', 'format', 'hiddensections', 'coursedisplay',
                'lang', 'newsitems', 'showgrades', 'showreports', 'showactivitydates',
                'maxbytes', 'enablecompletion', 'showcompletionconditions',
                'groupmode', 'groupmodeforce', 'defaultgroupingid', 'tags'
            ]);
        });
    }
};
