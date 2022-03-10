<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\trouble_type;

class tickets_trouble_type extends Model
{
    protected $fillable = [
        'ticket_id', 'trouble_type_id'
    ];

    public function type()
    {
        return $this->hasOne(trouble_type::class, 'id', 'trouble_type_id');
    }
}
