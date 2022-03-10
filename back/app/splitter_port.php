<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\splitter;

class splitter_port extends Model
{
    protected $fillable = [
        'splitter_id', 'port_number', 'going', 'going_id', 'los', 'port_status'
    ];

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'going_id');
    }

    public function belongs_to_splitter_nap()
    {
        return $this->belongsTo(splitter::class, 'splitter_id', 'id');
    }
}
