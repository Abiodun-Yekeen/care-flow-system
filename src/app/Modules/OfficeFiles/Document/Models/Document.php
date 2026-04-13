<?php

namespace App\Modules\OfficeFiles\Document\Models;

use App\Modules\Core\Iam\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'document_type',
        'original_name',
        'stored_name',
        'file_path',
        'mime_type',
        'file_size',
        'created_by',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
