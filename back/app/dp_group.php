<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dp_group extends Model
{
    protected $fillable = [
        'parent_group_id', 'name'
    ];
}
