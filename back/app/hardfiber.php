<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hardfiber extends Model
{
    protected $fillable = [
        'type', 'end1', 'end1_id', 'end2', 'end2_id'
    ];
}
