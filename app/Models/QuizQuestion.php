<?php

// ══════════════════════════════════════════════
// app/Models/QuizQuestion.php
// ══════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'module_id',
        'qtype',         // 'multichoice' | 'truefalse'
        'questiontext',
        'defaultmark',
        'slot',
        'moodle_question_id',
    ];

    /** Le module quiz auquel appartient cette question */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /** Les réponses possibles */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }

    /** La bonne réponse (fraction = 1) */
    public function correctAnswer()
    {
        return $this->hasOne(QuizAnswer::class, 'question_id')
                    ->where('fraction', 1.0);
    }
}