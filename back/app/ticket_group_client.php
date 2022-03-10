<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ticket_group_client extends Model
{
    protected $fillable = [
        'ticket_group_id', 'client_id'
    ];

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
