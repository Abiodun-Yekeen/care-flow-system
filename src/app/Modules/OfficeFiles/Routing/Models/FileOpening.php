<?php

namespace App\Modules\OfficeFiles\Routing\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\File\Models\File;
use Illuminate\Database\Eloquent\Model;

class FileOpening extends Model
{
    protected $fillable = [
        'file_id',
        'requested_by_user_id',
        'requested_from_department_id',
        'opening_by_user_id',
        'official_file_number',
        'status',
        'request_note',
        'response_note',
        'processed_at',
        'returned_at',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opening_by_user_id');
    }
}
