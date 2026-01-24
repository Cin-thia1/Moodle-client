<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
