<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'user_id',
        'status',
        'content',
        'file_path',
        'attempt_number',
        'submitted_at',
        'grade',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
  
public function module()
{
    return $this->belongsTo(Module::class);
}
public function student()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}

public function grader()
{
    return $this->belongsTo(\App\Models\User::class, 'graded_by');
}



}