<?php

namespace App;

use App\User;
use App\Client;

use Illuminate\Database\Eloquent\Model;

class rebate extends Model
{
    protected $fillable = [
        'client_id', 'user_id', 'amount', 'date', 'description', 'tbl_name'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
