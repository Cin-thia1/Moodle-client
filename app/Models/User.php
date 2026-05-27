<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property int|null $moodle_id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $profile_picture
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcements
 * @property-read int|null $announcements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Participant> $participants
 * @property-read int|null $participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $teacherCourses
 * @property-read int|null $teacher_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserCompetency> $userCompetencies
 * @property-read int|null $user_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $userGrades
 * @property-read int|null $user_grades_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMoodleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'moodle_id',
        'name',
        'username',
        'email',
        'password',
        'profile_picture',
        'origin',
        'sync_status',
        'sync_action',
        'synced_at',
        'dirty',
        'moodle_token',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dirty' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

   /* protected static function boot()
    {
        parent::boot();

        // Assigner le rôle par défaut à la création
        static::created(function ($user) {
            if (!$user->roles()->exists()) {
                $user->assignRole('ROLE_USER');
            }
        });
    }*/
protected static function boot()
{
    parent::boot();

    // Uniquement l'assignation du rôle par défaut
    static::created(function ($user) {
        if (!$user->roles()->exists()) {
            $user->assignRole('ROLE_USER');
        }
    });
}
    public function teacherCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /*public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }*/

    public function grades()
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }

   /* public function courses()
    {
        return $this->belongsToMany(Course::class, 'participants', 'user_id', 'course_id');
    }
*/
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }
    public function courses()
{
    return $this->belongsToMany(\App\Models\Course::class, 'course_user', 'user_id', 'course_id');
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

    public function userGrades()
    {
        return $this->hasMany(Grade::class);
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'user_competencies');
    }

    public function userCompetencies()
    {
        return $this->hasMany(UserCompetency::class);
    }

    // Scopes de synchronisation
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    public function scopeDirty($query)
    {
        return $query->where('dirty', true);
    }

    public function scopeConflicts($query)
    {
        return $query->where('sync_status', 'conflict');
    }
}