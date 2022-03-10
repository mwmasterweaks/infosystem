<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Region;
use App\Branch;

class area extends Model
{
    protected $fillable = [
        'name', 'region_id'
    ];

    public function region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'area_id', 'id');
    }
}
