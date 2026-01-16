<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
