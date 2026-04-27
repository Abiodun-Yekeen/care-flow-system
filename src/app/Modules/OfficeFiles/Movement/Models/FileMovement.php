<?php

namespace App\Modules\OfficeFiles\Movement\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Str;

class FileMovement extends Model
{
    protected $fillable = [
        'file_id',
        'from_department_id',
        'from_user_id',
        'to_department_id',
        'to_user_id',
        'acted_by_user_id',
        'received_by_user_id',
        'movement_type',
        'movement_status',
        'remarks',
        'minute',
        'acted_at',
        'received_at',
    ];

    protected $appends = [
    'from_user_name', 
    'to_user_name', 
    'from_dept_name', 
    'to_dept_name'
];


protected $casts = [
        'acted_at' => 'datetime',
    
    ];
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id'
        );
    }
    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function actedBy()
    {
        return $this->belongsTo(User::class, 'acted_by_user_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    // Accessor for From User Name
    public function getFromUserNameAttribute()
    {
      $name = $this->fromUser?->name ?? 'system';
      return Str::title(strtolower($name));

    }

    // Accessor for To User Name
    public function getToUserNameAttribute()
    {
        return $this->toUser?->name ?? 'Pending';
    }

    // Accessor for From Dept Name
    public function getFromDeptNameAttribute()
    {
        return $this->fromDepartment?->name;
    }

    // Accessor for To Dept Name
    public function getToDeptNameAttribute()
    {
        return $this->toDepartment?->name;
    }
}
