<?php

namespace App\Modules\Core\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = ['name', 'created_by'];
}
