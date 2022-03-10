<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class client_status_history extends Model
{
    protected $fillable = [
        'client_id', 'status', 'date_change', 'user_id'
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
