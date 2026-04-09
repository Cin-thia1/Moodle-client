<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $moodle_id
 * @property string $name
 * @property int $course_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Module> $modules
 * @property-read int|null $modules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Section extends Model
{
    protected $fillable = [
        'name',
        'course_id',
        'moodle_id',
        'summary',
        'position',
        'visible',
        'sync_status',
        'sync_action',
        'synced_at',
        'dirty',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    // ===================== SCOPES POUR SYNCHRONISATION =====================

    /**
     * Scope pour récupérer les sections en attente de synchronisation.
     */
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope pour récupérer les sections synchronisées.
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope pour récupérer les sections avec des modifications locales.
     */
    public function scopeDirty($query)
    {
        return $query->where('dirty', 1);
    }

    /**
     * Scope pour récupérer les sections en conflit.
     */
    public function scopeConflicts($query)
    {
        return $query->where('sync_status', 'conflict');
    }
}
