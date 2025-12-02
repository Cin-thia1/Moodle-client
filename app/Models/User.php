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
        parent::boot();
        
        static::created(function ($user) {
            // Utiliser la méthode de Spatie pour assigner le rôle par défaut
            $user->assignRole('ROLE_USER');
        });
    }

    public function teacherCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }
}