<?php

namespace App\Modules\Core\Iam\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Traits\HasIamRoles;
use App\Modules\Core\Shared\Models\FcmToken;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
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
        'password',

    ];
    protected $appends = ['primary_department','role_name'];

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
            'created_at' => 'datetime:Y-m-d H:i'
        ];
    }


    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }
    public function assignRole($role)
    {
         $this->roles()->syncWithoutDetaching($role);

    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function getRoleNameAttribute()
    {
        if ($this->roles->isEmpty()) {
            return 'Guest';
        }
        // Prioritize specialized roles over the 'user' role
        $role = $this->roles->first(fn($r) => $r->name !== 'user') ?? $this->roles->first();

        return $role->display_name;
    }
    public function files()
    {
        return $this->hasMany(File::class, 'current_holder_user_id');
    }

    public function department()
    {

        return $this->belongsToMany(Department::class, 'department_user')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
    public function primaryDepartment()
    {
        return $this->belongsToMany(Department::class, 'department_user')
            ->wherePivot('is_primary', true)
            ->withPivot('is_primary')
            ->limit(1);
    }
    public function getPrimaryDepartmentAttribute()
    {
        if ($this->relationLoaded('primaryDepartment')) {
            return $this->primaryDepartment;
        }

        // Otherwise, fall back to the query
        return $this->primaryDepartment()->first();
    }



    public function createdFiles()
    {
        return $this->hasMany(File::class, 'created_by');
    }

    public function movements()
    {
        return $this->hasMany(FileMovement::class, 'acted_by_user_id');
    }

    // In User.php model
    public function flushIamCache()
    {
        app(IamAuthorizationService::class)->clearUserCache($this);
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'user.'.$this->id;
    }


    protected static function newFactory()
    {
        return UserFactory::new();
    }

}
