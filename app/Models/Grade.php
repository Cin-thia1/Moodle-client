<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $grade
 * @property string|null $comment
 * @property int $submission_id
 * @property int $teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GradeItem|null $gradeItem
 * @property-read \App\Models\Submission $submission
 * @property-read \App\Models\User $teacher
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Grade extends Model
{
    protected $fillable = [
        'grade',
        'comment',
        'submission_id',
        'teacher_id',
        'sync_status',
        'sync_action',
        'synced_at',
        'dirty',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'grade_value' => 'decimal:2',
    ];

    // Relations pour l'ancien système (submissions)
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relations pour le nouveau système (grade items)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gradeItem()
    {
        return $this->belongsTo(GradeItem::class);
    }

    // Méthode pour vérifier si un grade est associé à une soumission
    public function isGraded()
    {
        return !is_null($this->grade) || !is_null($this->grade_value);
    }

    /**
     * Scope pour récupérer les grades en attente de synchronisation.
     */
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope pour récupérer les grades synchronisés.
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope pour récupérer les grades avec des modifications locales.
     */
    public function scopeDirty($query)
    {
        return $query->where('dirty', 1);
    }

    /**
     * Scope pour récupérer les grades en conflit.
     */
    public function scopeConflicts($query)
    {
        return $query->where('sync_status', 'conflict');
    }
}
