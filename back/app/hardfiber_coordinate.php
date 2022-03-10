<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hardfiber_coordinate extends Model
{
    protected $fillable = [
        'hardfiber_id', 'lat', 'lng'
    ];
}
