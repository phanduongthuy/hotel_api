<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ServiceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'content'
    ];
}
