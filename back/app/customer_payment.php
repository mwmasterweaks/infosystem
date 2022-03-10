<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\User;
use App\payment_method;

class customer_payment extends Model
{
    protected $fillable = [
        'id', 'client_id', 'user_id', 'payment_method_id', 'banking_payment_code_id', 'amount', 'date', 'or_number', 'remarks'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select("id", "name", "email");
    }

    public function payment_method()
    {
        return $this->belongsTo(payment_method::class, 'payment_method_id', 'id');
    }
}
