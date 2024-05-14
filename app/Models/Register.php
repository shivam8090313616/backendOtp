<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    protected $table = 'registers';

    protected $fillable = [
        'email',
        'f_name',
        'l_name',
        'mobile',
        'messenger',
        'password',
        'confirmpassword',
        'otp',
        'status',
    ];
}

