<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->integer('assignment_id')->nullable()->after('section_id');
            $table->text('intro')->nullable();
            $table->text('activity')->nullable();
            $table->timestamp('duedate')->nullable();
            $table->timestamp('allowsubmissionsfromdate')->nullable();
            $table->timestamp('cutoffdate')->nullable();
            $table->timestamp('gradingduedate')->nullable();
            $table->integer('maxattempts')->default(1);
            $table->integer('grade')->default(100);
            $table->string('pdf_filename')->nullable()->after('gradingduedate');
            $table->text('pdf_url')->nullable()->after('pdf_filename');
        });

    }

    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn([
                'assignment_id',
                'intro',
                'activity',
                'duedate',
                'allowsubmissionsfromdate',
                'cutoffdate',
                'gradingduedate',
                'maxattempts',
                'grade',
                'pdf_filename',
                'pdf_url'
            ]);
        });
    }
};