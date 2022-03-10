<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class device_type extends Model
{
    protected $fillable = [
        'name', 'hex_number'
    ];
}
