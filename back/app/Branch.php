<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\area;

class Branch extends Model
{
    protected $fillable = [
        'name', 'area_id'
    ];

    public function area()
    {
        return $this->hasOne(area::class, 'id', 'area_id');
    }
}
