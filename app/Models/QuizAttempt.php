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
}
 