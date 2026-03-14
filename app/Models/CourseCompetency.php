<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_id Identifiant unique de Moodle
 * @property int $course_id
 * @property int $competency_id
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency $competency
 * @property-read \App\Models\Course $course
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCompetency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'course_id',
        'competency_id',
        'sort_order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }
}
