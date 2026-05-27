<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $moodle_id
 * @property string $fullname
 * @property string $shortname
 * @property string|null $summary
 * @property int $numsections
 * @property \Illuminate\Support\Carbon|null $startdate
 * @property \Illuminate\Support\Carbon|null $enddate
 * @property int|null $teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $category_id
 * @property string|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcements
 * @property-read int|null $announcements_count
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseCompetency> $courseCompetencies
 * @property-read int|null $course_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GradeItem> $gradeItems
 * @property-read int|null $grade_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Module> $modules
 * @property-read int|null $modules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Participant> $participants
 * @property-read int|null $participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $students
 * @property-read int|null $students_count
 * @property-read \App\Models\User|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereEnddate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereNumsections($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereStartdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'sync_status',
        'sync_action',
        'synced_at',
        'dirty',
        'visible',
        'idnumber',
        'format',
        'hiddensections',
        'coursedisplay',
        'lang',
        'newsitems',
        'showgrades',
        'showreports',
        'showactivitydates',
        'maxbytes',
        'enablecompletion',
        'showcompletionconditions',
        'groupmode',
        'groupmodeforce',
        'defaultgroupingid',
        'tags',
    ];

    protected $casts = [
        'startdate' => 'datetime',
        'enddate'   => 'datetime',
        'visible' => 'boolean',
        'showgrades' => 'boolean',
        'showreports' => 'boolean',
        'showactivitydates' => 'boolean',
        'enablecompletion' => 'boolean',
        'showcompletionconditions' => 'boolean',
        'groupmodeforce' => 'boolean',
        'hiddensections' => 'integer',
        'coursedisplay' => 'integer',
        'newsitems' => 'integer',
        'groupmode' => 'integer',
        'defaultgroupingid' => 'integer',
    ];

    // Professeur principal du cours
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Toutes les sections du cours
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // Catégorie du cours
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Tous les modules (devoirs, quizzes, etc.) du cours
    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id');
    }

   
    public function users()
{
    return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id');
}


    // Relations pour les 5 sections
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function gradeItems()
    {
        return $this->hasMany(GradeItem::class);
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'course_competencies');
    }

    public function courseCompetencies()
    {
        return $this->hasMany(CourseCompetency::class);
    }

    // ===================== SCOPES POUR SYNCHRONISATION =====================

    /**
     * Scope pour récupérer les cours en attente de synchronisation.
     */
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope pour récupérer les cours synchronisés.
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope pour récupérer les cours avec des modifications locales.
     */
    public function scopeDirty($query)
    {
        return $query->where('dirty', 1);
    }

    /**
     * Scope pour récupérer les cours en conflit.
     */
    public function scopeConflicts($query)
    {
        return $query->where('sync_status', 'conflict');
    }
}
