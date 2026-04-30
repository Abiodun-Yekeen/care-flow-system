<?php

namespace App\Modules\OfficeFiles\Movement\Models;

use App\Modules\Core\Iam\Models\User;
use App\Modules\OfficeFiles\File\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileAssignment extends Model
{
    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime:Y-m-d H:i'
        ];
    }
    /**
     * The file this assignment belongs to.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * The user who currently has the file.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * The user who sent the file.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}
