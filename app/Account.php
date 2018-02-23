<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'city',
        'zipcode',
        'state' ,
        'email',
        'phone',
        'company'
    ];
    
    public $timestamps = false;
}

            