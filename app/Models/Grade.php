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
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Méthode pour vérifier si un grade est associé à une soumission
    public function isGraded()
    {
        return !is_null($this->grade);
    }
}
