<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class role_group_role extends Model
{
    protected $fillable = [
        'role_group_id', "role_id"
    ];
}
