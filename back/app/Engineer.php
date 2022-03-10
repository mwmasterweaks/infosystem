<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Engineer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'position', 'signature'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
