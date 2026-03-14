<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $moodle_id Identifiant unique de Moodle
 * @property int $course_id
 * @property string $item_name Nom du critère d'évaluation
 * @property string $item_type assignment, quiz, forum, etc.
 * @property numeric $grade_max Note maximale
 * @property int|null $sort_order
 * @property int $status 1=visible, 0=hidden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem forCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereGradeMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradeItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GradeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'moodle_id',
        'course_id',
        'item_name',
        'item_type',
        'grade_max',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'grade_max' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function statistics()
    {
        $grades = $this->grades()->pluck('final_grade')->filter();
        
        if ($grades->isEmpty()) {
            return [
                'mean' => 0,
                'median' => 0,
                'min' => 0,
                'max' => 0,
            ];
        }
        
        $sorted = $grades->sort()->values();
        $count = $sorted->count();
        $middle = intval($count / 2);
        
        if ($count % 2 === 0) {
            $median = ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        } else {
            $median = $sorted[$middle];
        }
        
        return [
            'mean' => $grades->average(),
            'median' => $median,
            'min' => $grades->min(),
            'max' => $grades->max(),
        ];
    }
}
