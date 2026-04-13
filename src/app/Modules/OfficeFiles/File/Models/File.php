<?php

namespace App\Modules\OfficeFiles\File\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\Document\Models\Document;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use App\Modules\OfficeFiles\Routing\Models\FileOpening;
use App\Modules\OfficeFiles\Routing\Models\FileTransfer;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'uuid',
        'official_file_number',
        'temp_file_number',
        'is_temporary',
        'subject',
        'description',
        'source_name',
        'source_reference_no',
        'status',
        'priority',
        'current_department_id',
        'current_holder_user_id',
        'created_by',
        'registered_by',
        'date_received',
        'last_movement_at',
        'closed_at',
    ];

    // RELATIONSHIPS
    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'current_department_id');
    }

    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_holder_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    // CHILD RELATIONS
    public function movements()
    {
        return $this->hasMany(FileMovement::class);
    }

    public function latestMovement()
    {
        return $this->hasOne(FileMovement::class)->latestOfMany('acted_at');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function transferRequests()
    {
        return $this->hasMany(FileTransfer::class);
    }

    public function openingRequests()
    {
        return $this->hasMany(FileOpening::class);
    }

    public function fileReceive()
    {
        return $this->hasMany(FileReceive::class);
    }
}
