<?php

namespace App\Modules\Core\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = ['token', 'user_id'];
}
