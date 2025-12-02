<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'moodle_id',
        'name',
        'modname',
        'modplural',
        'downloadcontent',
        'file_path',
        'section_id',
        // Nouveaux champs pour les assignments
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
        'pdf_url',
    ];

    protected $casts = [
        'downloadcontent' => 'boolean',
        'duedate' => 'datetime',
        'allowsubmissionsfromdate' => 'datetime',
        'cutoffdate' => 'datetime',
        'gradingduedate' => 'datetime',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}