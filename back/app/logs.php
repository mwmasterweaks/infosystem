<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    protected $fillable = [
        'user_id', 'controller', 'function_name', 'action',
        'source_table', 'source_id', 'data'
    ];
}
