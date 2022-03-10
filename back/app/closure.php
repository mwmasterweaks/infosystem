<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\closure_type;
use App\splitter;

class closure extends Model
{
    protected $fillable = [
        'closure_type_id', 'name', 'lat', 'lng', 'dp_group_id'
    ];

    public function closure_type()
    {
        return $this->hasOne(closure_type::class, 'id', 'closure_type_id');
    }

    public function splitter_closure()
    {
        return $this->hasMany(splitter::class, 'attach_id', 'id')
            ->where("attach_to", "closure");
    }
}
