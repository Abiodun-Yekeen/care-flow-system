<?php

namespace App\Modules\Core\Iam\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Traits\HasIamRoles;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use App\Modules\Organization\Department\Models\Department;
use Database\Factories\Modules\Access\Models\UserFactory;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    /** @use HasFactory<\UserFactory\UserFactory> */
    use HasFactory, Notifiable,HasIamRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'staff_id',
        'mobile_no',
        'email',
        'department_id',
        'role',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function assignRole($role)
    {
        return $this->roles()->syncWithoutDetaching($role);

    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function getRoleNameAttribute()
    {
        return $this->roles->first()?->name ?? 'Staff';
    }
    public function files()
    {
        return $this->hasMany(File::class, 'current_holder_user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function createdFiles()
    {
        return $this->hasMany(File::class, 'created_by');
    }

    public function movements()
    {
        return $this->hasMany(FileMovement::class, 'acted_by_user_id');
    }
    protected static function newFactory()
    {
        return UserFactory::new();
    }

}
