<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mac_table extends Model
{

    protected $fillable = [
        'pmx_ip', 'ip', 'mac_desc', 'port_desc', 'mac_address_main', 'mac_address_first', 'mac_address_second'
    ];
}
