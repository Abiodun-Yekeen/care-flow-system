<?php

namespace App\Modules\OfficeFiles\File\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\Document\Models\Document;
use App\Modules\OfficeFiles\Movement\Models\FileAssignment;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use App\Modules\OfficeFiles\Routing\Models\FileOpening;
use App\Modules\OfficeFiles\Routing\Models\FileTransfer;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'remark',
        'priority',
        'current_department_id',
        'current_holder_user_id',
        'created_by',
        'registered_by',
        'date_received',
        'last_movement_at',
        'closed_at',
    ];

    protected $casts = [
        'date_received' => 'datetime',
        'last_movement_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_temporary' => 'boolean',
    ];

    // RELATIONSHIPS

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'current_department_id');
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

    public function assignments(): HasMany
    {
        return $this->hasMany(FileAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(FileAssignment::class)
            ->where('status', 'active')
            ->latestOfMany();
    }
    public function currentHolder()
    {
        // This goes through the assignment to get the User model
        return $this->hasOneThrough(
            User::class,
            FileAssignment::class,
            'file_id',
            'id',
            'id',
            'assigned_to_user_id'
        )->where('file_assignments.status', 'active');
    }
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
