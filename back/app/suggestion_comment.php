<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class suggestion_comment extends Model
{
    protected $fillable = [
        'suggestion_id', 'user_id', 'comment', 'status'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
