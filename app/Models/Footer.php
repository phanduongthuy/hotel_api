<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;
    protected $fillable = [
        'company',
        'legal_representation',
        'business_license',
        'address',
        'email',
        'phone',
        'facebook',
        'zalo',
    ];
}
