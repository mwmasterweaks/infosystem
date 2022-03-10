<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\splitter;
use App\node;

class olt extends Model
{
    protected $fillable = [
        'node_id', 'name', 'ip'
    ];

    public function splitter_lcp()
    {
        return $this->hasMany(splitter::class, 'parent_id', 'id')
            ->where("parent", "olt");
    }

    public function belongs_to_node()
    {
        return $this->belongsTo(node::class, 'node_id', 'id');
    }
}
