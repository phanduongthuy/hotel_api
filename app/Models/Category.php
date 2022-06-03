<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'description',
        'is_highlight',
    ];
    protected $dates = ['deleted_at'];

    public function rooms()
    {
        return $this->hasMany(
            Room::class
        );
    }

}
