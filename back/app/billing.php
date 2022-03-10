<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;

class billing extends Model
{
    protected $fillable = [
        'id', 'client_id', 'date', 'item', 'description', 'price', 'balance'
    ];

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
