<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class wht extends Model
{
    protected $fillable = [
        'client_id', 'date', 'description', 'amount'
    ];

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
