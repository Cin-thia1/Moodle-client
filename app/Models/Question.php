<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'content',
        'choices',
        'correct_choice_id',
    ];

    // Définir que 'choices' est un tableau JSON
    protected $casts = [
        'choices' => 'array',
    ];

 
    // Vérifie si la proposition donnée est correcte
    public function isCorrect($choiceId)
    {
        return $this->correct_choice_id === $choiceId;
    }
}
