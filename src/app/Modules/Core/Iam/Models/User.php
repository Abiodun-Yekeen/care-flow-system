<?php

namespace App\Modules\Core\Iam\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Traits\HasIamRoles;
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
        'email',
        'password',
        'username',
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

     protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->username)) {
                //convert name to small letter
                $base = Str::slug($user->name) ?: 'user';
                $username = $base;

                $counter = 1;
                // Check if the base name is already taken
                while (self::where('username', $username)->exists()) {
                    $username = $base . '-' . $counter;
                    $counter++;
                }

                $user->username = $username;
            }
        });
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


    protected static function newFactory()
    {
        return UserFactory::new();
    }

}
