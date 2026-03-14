<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_enrolment_id Identifiant unique de l'enrôlement Moodle
 * @property int $course_id
 * @property int $user_id
 * @property string $role ROLE_TEACHER, ROLE_STUDENT, ROLE_USER
 * @property int $status 1=active, 0=suspended
 * @property \Illuminate\Support\Carbon|null $enrolled_at
 * @property \Illuminate\Support\Carbon|null $unenrolled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant byRole($role)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant forCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereEnrolledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereMoodleEnrolmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereUnenrolledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereUserId($value)
 * @mixin \Eloquent
 */
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
