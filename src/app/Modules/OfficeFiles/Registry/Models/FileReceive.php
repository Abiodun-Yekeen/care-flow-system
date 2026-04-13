<?php

namespace App\Modules\OfficeFiles\Registry\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Database\Eloquent\Model;

class FileReceive extends Model
{
    protected $fillable = [
        'file_id',
        'receive_department_id',
        'created_by',
        'received_from',
        'remark',
        'status',
        'date_received',
        'submitted_at',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'receive_department_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
