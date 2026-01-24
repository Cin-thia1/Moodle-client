<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
