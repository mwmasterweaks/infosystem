<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name', 'description', 'package_type_id', 'max_speed', 'cir', 'mrr'
    ];

    public function package_type()
    {
        return $this->hasOne(Package_type::class, 'id', 'package_type_id');
    }
}
