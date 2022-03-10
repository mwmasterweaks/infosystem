<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class suggestion extends Model
{
    protected $fillable = [
        'title', 'message', 'user_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
