<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'course_id',
        'competency_id',
        'sort_order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }
}
