<?php
 
// ══════════════════════════════════════════════
// app/Models/QuizAttempt.php
// ══════════════════════════════════════════════
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class QuizAttempt extends Model
{
    protected $fillable = [
        'module_id',
        'user_id',
        'attempt',
        'state',       // inprogress | finished | abandoned
        'sumgrades',
        'timestart',
        'timefinish',
        'moodle_attempt_id',
        'sync_status',
        'sync_action',
        'synced_at',
        'dirty',
    ];
 
    protected $casts = [
        'timestart'  => 'datetime',
        'timefinish' => 'datetime',
        'sumgrades'  => 'float',
    ];
 
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'attempt_id');
    }
 
    /** Calcule et retourne la note sur la note max du module */
    public function gradeOutOf(int $maxGrade): float
    {
        $module = $this->module;
        $totalMarks = $module->quizQuestions()->sum('defaultmark');
        if ($totalMarks == 0) return 0;
        return round(($this->sumgrades / $totalMarks) * $maxGrade, 2);
    }
 
    public function isFinished(): bool
    {
        return $this->state === 'finished';
    }

    /**
     * Scope pour récupérer les tentatives de quiz en attente de synchronisation.
     */
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope pour récupérer les tentatives de quiz synchronisées.
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope pour récupérer les tentatives de quiz avec des modifications locales.
     */
    public function scopeDirty($query)
    {
        return $query->where('dirty', 1);
    }

    /**
     * Scope pour récupérer les tentatives de quiz en conflit.
     */
    public function scopeConflicts($query)
    {
        return $query->where('sync_status', 'conflict');
    }
}
 