<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_id Identifiant unique de Moodle
 * @property int $course_id
 * @property int $user_id
 * @property string $subject
 * @property string $message
 * @property int $status 1=visible, 0=hidden
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\Course $course
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement forCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereUserId($value)
 * @mixin \Eloquent
 */
class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'course_id',
        'user_id',
        'subject',
        'message',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}
