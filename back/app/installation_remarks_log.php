<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class installation_remarks_log extends Model
{
    protected $fillable = [
        'client_detail_id', 'user_id',  'remarks',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
