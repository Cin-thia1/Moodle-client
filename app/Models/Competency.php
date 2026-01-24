<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'shortname',
        'idnumber',
        'description',
        'description_long',
        'status',
    ];

    public function courseCompetencies()
    {
        return $this->hasMany(CourseCompetency::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_competencies');
    }

    public function userCompetencies()
    {
        return $this->hasMany(UserCompetency::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_competencies');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
