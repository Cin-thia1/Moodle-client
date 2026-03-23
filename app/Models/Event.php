<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $date
 * @property string $type
 * @property string|null $description
 * @property string|null $location
 * @property string $duration_type
 * @property string|null $end_date
 * @property int|null $duration_minutes
 * @property int $repeat_event
 * @property int|null $repeat_count
 * @property int|null $course_id
 * @property int|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDurationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereRepeatCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereRepeatEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'date', 'type', 'course_id', 'category_id','module_id',
        'description', 'location', 'duration_type', 'end_date',
        'duration_minutes', 'repeat_event', 'repeat_count'
    ];
    protected $attributes = [
    'repeat_count' => 1,];

    /*Relation vers le devoir(module) qui a crée cet évènement */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}