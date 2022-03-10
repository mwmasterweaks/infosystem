<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\area;

class Region extends Model
{
    protected $fillable = [
        'name', 'user_id_rm', 'user_id_visor'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id_rm');
    }

    public function visor()
    {
        return $this->hasOne(User::class, 'id', 'user_id_visor');
    }

    public function area()
    {
        return $this->hasMany(area::class, 'region_id', 'id');
    }
}
