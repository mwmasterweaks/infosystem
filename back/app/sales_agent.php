<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sales;

class sales_agent extends Model
{
    protected $fillable = [
        'sales_id', 'name', 'status', 'quota'
    ];

    public function sales()
    {
        return $this->hasOne(Sales::class, 'id', 'sales_id');
    }
}
