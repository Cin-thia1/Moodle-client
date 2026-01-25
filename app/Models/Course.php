<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'moodle_id',
        'fullname',
        'shortname',
        'summary',
        'numsections',
        'startdate',
        'enddate',
        'teacher_id',
        'category_id',
        'image',
    ];

    protected $casts = [
        'startdate' => 'datetime',
        'enddate'   => 'datetime',
    ];

    // Professeur principal du cours
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Toutes les sections du cours
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // Catégorie du cours
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Tous les modules (devoirs, quizzes, etc.) du cours
    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    // Relations pour les 5 sections
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function gradeItems()
    {
        return $this->hasMany(GradeItem::class);
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'course_competencies');
    }

    public function courseCompetencies()
    {
        return $this->hasMany(CourseCompetency::class);
    }
}
