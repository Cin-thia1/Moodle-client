<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_id Identifiant unique de Moodle (file id)
 * @property int $course_id
 * @property int|null $user_id
 * @property string $filename
 * @property string $filepath Chemin virtuel dans Moodle
 * @property string|null $mimetype
 * @property int $filesize
 * @property string|null $file_url
 * @property int $status 1=visible, 0=hidden
 * @property \Illuminate\Support\Carbon|null $file_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document forCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFilepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereMimetype($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUserId($value)
 * @mixin \Eloquent
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'course_id',
        'user_id',
        'filename',
        'filepath',
        'mimetype',
        'filesize',
        'file_url',
        'status',
        'file_date',
    ];

    protected $casts = [
        'file_date' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
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
