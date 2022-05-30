<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class GeneralPloblem extends Model
{
    use HasFactory;
    protected $table = 'general_ploblems';

    protected $fillable = [
        'title',
        'content',
    ];

}
