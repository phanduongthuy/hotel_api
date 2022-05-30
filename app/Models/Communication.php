<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Communication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'communications';

    protected $fillable = [
        'facebook',
        'phone',
        'email',
        'instagram',
        'address',
        'introduce'
    ];
}
