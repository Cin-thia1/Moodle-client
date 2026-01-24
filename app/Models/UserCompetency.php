<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'user_id',
        'competency_id',
        'proficiency',
        'grade',
        'reviewed_at',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public const PROFICIENCY_INCOMPLETE = 0;
    public const PROFICIENCY_COMPLETE = 1;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function scopeComplete($query)
    {
        return $query->where('proficiency', self::PROFICIENCY_COMPLETE);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('proficiency', self::PROFICIENCY_INCOMPLETE);
    }

    public function isComplete()
    {
        return $this->proficiency === self::PROFICIENCY_COMPLETE;
    }
}
