<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_id Identifiant unique de Moodle
 * @property string $shortname
 * @property string|null $idnumber Numéro d'identité personnalisé
 * @property string|null $description
 * @property string|null $description_long
 * @property int $status 1=active, 0=archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseCompetency> $courseCompetencies
 * @property-read int|null $course_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserCompetency> $userCompetencies
 * @property-read int|null $user_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereDescriptionLong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
