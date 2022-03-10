<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class banking_payment_code extends Model
{
    protected $fillable = [
        'id', 'code', 'amount', 'date', 'status'
    ];
}
