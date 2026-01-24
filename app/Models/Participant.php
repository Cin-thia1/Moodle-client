<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_enrolment_id',
        'course_id',
        'user_id',
        'role',
        'status',
        'enrolled_at',
        'unenrolled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'unenrolled_at' => 'datetime',
    ];

    public const ROLE_TEACHER = 'ROLE_TEACHER';
    public const ROLE_STUDENT = 'ROLE_STUDENT';
    public const ROLE_USER = 'ROLE_USER';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function isTeacher()
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent()
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isGuest()
    {
        return $this->role === self::ROLE_USER;
    }
}
