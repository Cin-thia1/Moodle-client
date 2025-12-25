<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'date', 'type', 'course_id', 'category_id',
        'description', 'location', 'duration_type', 'end_date',
        'duration_minutes', 'repeat_event', 'repeat_count'
    ];
}