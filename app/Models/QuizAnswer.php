<?php

// ══════════════════════════════════════════════
// app/Models/QuizAnswer.php
// ══════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'answer',
        'fraction',
        'feedback',
    ];

    protected $casts = [
        'fraction' => 'float',
    ];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    /** Vrai si c'est la bonne réponse */
    public function isCorrect(): bool
    {
        return $this->fraction >= 1.0;
    }
}