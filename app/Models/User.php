<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'moodle_id',
        'name',
        'email',
        'password',
        'profile_picture',
        'origin',
        'synced_at',
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
        ];
    }

    protected static function boot()
    {
        
        
       /* static::created(function ($user) {
            // Utiliser la méthode de Spatie pour assigner le rôle par défaut
            $user->assignRole('ROLE_USER');
        });*/
         parent::boot();

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
}