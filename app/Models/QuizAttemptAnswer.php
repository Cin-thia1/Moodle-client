<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
        'fraction',
    ];
 
    protected $casts = [
        'fraction' => 'float',
    ];
 
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }
 
    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
 
    public function answer()
    {
        return $this->belongsTo(QuizAnswer::class, 'answer_id');
    }
}