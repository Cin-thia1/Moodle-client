<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'moodle_id',
        'fullname',
        'shortname',
        'summary',
        'numsections',
        'startdate',
        'enddate',
        'teacher_id',
        'category_id',
        'image',
    ];

    protected $casts = [
        'startdate' => 'datetime',
        'enddate' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules(){
        return $this->hasMany(Module::class);
    }
}
