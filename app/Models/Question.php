<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $content
 * @property string $type
 * @property string|null $options
 * @property string|null $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
