<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewRequest extends Model
{
    protected $fillable = ['user_id', 'isapproved', 'post_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function Post()
    {
        return $this->belongsTo('App\Models\Post');
    }
}
