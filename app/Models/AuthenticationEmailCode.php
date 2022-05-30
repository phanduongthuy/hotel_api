<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class AuthenticationEmailCode extends Model
{
    use HasFactory;
    protected $table = 'authentication_email_codes';

    protected $fillable = [
        'code',
        'email',
        'expired',
    ];

}
