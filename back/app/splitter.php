<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\splitter_type;
use App\splitter_port;
use App\closure;
use App\olt;

class splitter extends Model
{
    protected $fillable = [
        'attach_to', 'attach_id', 'splitter_type_id', 'parent', 'parent_id',
        'port_type', 'attached_port', 'status'
    ];

    public function splitter_type()
    {
        return $this->hasOne(splitter_type::class, 'id', 'splitter_type_id');
    }

    public function splitter_port()
    {
        return $this->hasMany(splitter_port::class, 'splitter_id', 'id');
    }

    public function splitter_nap()
    {
        return $this->hasMany(splitter::class, 'parent_id', 'id')
            ->where("parent", "splitter");
    }


    public function belongs_to_splitter_lcp()
    {
        return $this->belongsTo(splitter::class, 'parent_id', 'id')
            ->where("parent", "olt");
    }

    public function belongs_to_olt()
    {
        return $this->belongsTo(olt::class, 'parent_id', 'id');
    }

    public function closure()
    {
        return $this->hasOne(closure::class, 'id', 'attach_id');
    }
}
