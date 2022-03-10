<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bucket extends Model
{
    protected $fillable = [
        'name', 'description', 'IP', 'username',
        'password', 'user_id', 'role_id'
    ];
}
