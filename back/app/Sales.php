<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\sales_agent;

class Sales extends Model
{
    protected $fillable = [
        'user_id', 'quota', 'status'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function agent()
    {
        return $this->hasMany(sales_agent::class, 'sales_id', 'id');
    }
}
