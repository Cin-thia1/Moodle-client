<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $duedate
 * @property int $attemptnumber
 * @property bool $published
 * @property int $module_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Module $module
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereAttemptnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereDuedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assignment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Assignment extends Model
{
    protected $fillable = [
        'name',
        'duedate',
        'attemptnumber',
        'module_id',
        'question_ids',
        'published',
        'created_by',
    ];

    protected $casts = [
        'duedate' => 'datetime',
        'question_ids' => 'array',
        'published' => 'boolean',
    ];
    
    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
