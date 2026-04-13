<?php

namespace App\Modules\OfficeFiles\Routing\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Database\Eloquent\Model;

class FileTransfer extends Model
{
    protected $fillable = [
        'file_id',
        'requesting_department_id',
        'requesting_user_id',
        'holding_department_id',
        'holding_user_id',
        'status',
        'request_note',
        'response_note',
        'requested_at',
        'transferred_at',
        'responded_at',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function requestingDepartment()
    {
        return $this->belongsTo(Department::class, 'requesting_department_id'
        );
    }

    public function holdingDepartment()
    {
        return $this->belongsTo(Department::class, 'holding_department_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requesting_user_id');
    }
}
