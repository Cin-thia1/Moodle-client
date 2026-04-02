<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $moodle_id
 * @property string $name
 * @property string $modname
 * @property string $modplural
 * @property bool $downloadcontent
 * @property string $file_path
 * @property int $section_id
 * @property int|null $assignment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $intro
 * @property string|null $activity
 * @property \Illuminate\Support\Carbon|null $duedate
 * @property \Illuminate\Support\Carbon|null $allowsubmissionsfromdate
 * @property \Illuminate\Support\Carbon|null $cutoffdate
 * @property \Illuminate\Support\Carbon|null $gradingduedate
 * @property string|null $pdf_filename
 * @property string|null $pdf_url
 * @property int $maxattempts
 * @property int $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Assignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \App\Models\Section $section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereAllowsubmissionsfromdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCutoffdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereDownloadcontent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereDuedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereGradingduedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereIntro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereMaxattempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereModname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereModplural($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module wherePdfFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module wherePdfUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Module extends Model
{
    protected $fillable = [
        'moodle_id',
        'name',
        'modname',  // 'assign' | 'quiz'
        'modplural',
        'downloadcontent',
        'file_path',
        'section_id',
        // Nouveaux champs pour les assignments
        'assignment_id',
        'intro',
        'activity',
        'duedate',
        'allowsubmissionsfromdate',
        'cutoffdate',
        'gradingduedate',
        'maxattempts',
        'grade',
        'pdf_filename',
        'pdf_url',

        //champs quiz
        'timeopen',
        'timeclose',
        'timelimit', // en secondes, null=illimité
        'attempts', //nb  tentatives, 0=illimité
        'grademethod', //0=highest, 1=average, 2=first, 3=last
        'shuffleanswers',
        'questionsperpage',
    ];

    protected $casts = [
        'downloadcontent' => 'boolean',
        'duedate' => 'datetime',
        'allowsubmissionsfromdate' => 'datetime',
        'cutoffdate' => 'datetime',
        'gradingduedate' => 'datetime',
        'shuffleanswers' => 'boolean',
        'timeopen' => 'datetime',
        'timeclose' => 'datetime'
    ];

    



    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
 
public function section()
{
    return $this->belongsTo(Section::class);
}

public function submissions()
{
    return $this->hasMany(Submission::class);
}


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    /*Evènements calendrier crées par ce devoir*/
    public function events()
    {
        return $this->hasMany(Event::class, 'module_id');
    }

    //Questions du quiz, triées par slot
    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('slot');
    }

    //Tentative de quiz
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);

    }

    //Tentatives d'un utilisateur spécifique
    public function quizAttemptsForUser(int $userId)
    {
        return $this->hasMany(QuizAttempt::class)
                    ->where('user_id', $userId)
                    ->orderBy('attempt');
    }

    //Helpers
    public function isQuiz(): bool
    {
        return $this->modname === 'quiz';
    }
 
    public function isAssignment(): bool
    {
        return $this->modname === 'assign';
    }
 
    /** Libellé de la méthode de notation */
    public function gradeMethodLabel(): string
    {
        return match((int)$this->grademethod) {
            0 => 'Note la plus haute',
            1 => 'Moyenne',
            2 => 'Première tentative',
            3 => 'Dernière tentative',
            default => 'Note la plus haute',
        };
    }
 
    /** Durée formatée lisiblement */
    public function timelimitFormatted(): string
    {
        if (!$this->timelimit) return 'Illimité';
        $minutes = intdiv($this->timelimit, 60);
        $seconds = $this->timelimit % 60;
        return $seconds > 0 ? "{$minutes} min {$seconds} s" : "{$minutes} min";
    }
      
}