<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PolicyRegulations extends Model
{
    use HasFactory;
    protected $fillable = [
        'content'
    ];
}
