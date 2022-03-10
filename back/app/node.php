<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\olt;

class node extends Model
{
    protected $fillable = [
        'name', 'lat', 'lng'
    ];

    public function olts()
    {
        return $this->hasMany(olt::class, 'node_id', 'id');
    }
}
