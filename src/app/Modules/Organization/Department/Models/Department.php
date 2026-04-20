<?php

namespace App\Modules\Organization\Department\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Shared\Models\BaseModel;
use App\Modules\OfficeFiles\File\Models\File;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable =[
        'name',
        'code',
        'type',
        'description',
        'active',
        'created_by',
        'updated_by',
    ];


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'current_department_id');
    }
    public function getCurrentHodIdAttribute()
    {
        //  Find the user in this department who has the 'hod' role
        return $this->users()
            ->whereHas('roles', function($q) {
                $q->where('name', 'hod');
            })
            ->first()?->id;
    }

    public function scopeWithHod($query)
    {
        return $query->addSelect(['hod_id' => User::select('id')
            ->whereColumn('department_id', 'departments.id')
            ->whereHas('roles', fn($q) => $q->where('name', 'hod'))
            ->limit(1)
        ]);
    }



}

