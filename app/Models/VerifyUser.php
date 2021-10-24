<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    //
    protected $fillable = ['user_id', 'token', 'active'];
}
