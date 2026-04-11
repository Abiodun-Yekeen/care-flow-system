<?php

namespace App\Modules\Organization\Department\Models;

use App\Modules\Billing\service\Models\Service;
use App\Modules\Core\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Department extends BaseModel
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


    public function services()
    {
        return $this->hasMany(Service::class);
    }
}

