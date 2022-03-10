<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hardfiber_core extends Model
{
    protected $fillable = [
        'hardfiber_id', 'buffer_color', 'core_color', 'going', 'going_id', 'end_type', 'los'
    ];
}
