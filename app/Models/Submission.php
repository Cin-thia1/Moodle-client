<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $moodle_submission_id
 * @property int $module_id
 * @property int $user_id
 * @property string $status
 * @property string $sync_status
 * @property string|null $synced_at
 * @property string|null $last_sync_error
 * @property string|null $content
 * @property string|null $file_path
 * @property \App\Models\Grade|null $grade
 * @property string $grade_sync_status
 * @property string|null $grade_synced_at
 * @property string|null $grade_last_sync_error
 * @property string|null $graded_at
 * @property int|null $graded_by
 * @property int $attempt_number
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Assignment|null $assignment
 * @property-read \App\Models\User|null $grader
 * @property-read \App\Models\Module $module
 * @property-read \App\Models\User $student
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereAttemptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereGradeLastSyncError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereGradeSyncStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereGradeSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereGradedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereGradedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereLastSyncError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereMoodleSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereSyncStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereUserId($value)
 * @mixin \Eloquent
 */
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
        'sync_status',
        'sync_action',
        'synced_at',
        'dirty',
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
    /**
     * Scope pour récupérer les soumissions en attente de synchronisation.
     */
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope pour récupérer les soumissions synchronisées.
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope pour récupérer les soumissions avec des modifications locales.
     */
    public function scopeDirty($query)
    {
        return $query->where('dirty', 1);
    }

    /**
     * Scope pour récupérer les soumissions en conflit.
     */
    public function scopeConflicts($query)
    {
        return $query->where('sync_status', 'conflict');
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