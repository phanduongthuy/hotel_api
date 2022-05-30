<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
class EmailTemplate extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'subject',
        'group_name',
        'content',
    ];

}
