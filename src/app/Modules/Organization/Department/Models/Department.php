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

}

