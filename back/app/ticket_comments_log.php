<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ticket_comments_log extends Model
{
    protected $fillable = [
        'remarks_id', 'user_id', 'comments', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
