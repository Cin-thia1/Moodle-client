<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'grade',
        'comment',
        'submission_id',
        'teacher_id',
        'user_id',
        'grade_item_id',
        'grade_value',
        'feedback',
        'graded_at',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'grade_value' => 'decimal:2',
    ];

    // Relations pour l'ancien système (submissions)
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relations pour le nouveau système (grade items)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gradeItem()
    {
        return $this->belongsTo(GradeItem::class);
    }

    // Méthode pour vérifier si un grade est associé à une soumission
    public function isGraded()
    {
        return !is_null($this->grade) || !is_null($this->grade_value);
    }
}
