<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_id Identifiant unique de Moodle
 * @property int $user_id
 * @property int $competency_id
 * @property int $proficiency 0=incomplete, 1=complete
 * @property numeric|null $grade
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency $competency
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency complete()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency incomplete()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereProficiency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCompetency whereUserId($value)
 * @mixin \Eloquent
 */
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
