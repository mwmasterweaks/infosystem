<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mac_deployment_identifier extends Model
{
    protected $fillable = [
        'name', 'hex_number'
    ];
}
