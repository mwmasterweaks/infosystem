<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\olt;

class pon extends Model
{
    protected $fillable = [
        'olt_id', 'pon', 'area'
    ];

    public function olt()
    {
        return $this->hasOne(olt::class, 'id', 'olt_id');
    }
}
