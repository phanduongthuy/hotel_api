<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'rooms';
    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'is_highlight',
        'is_active',
    ];
    protected $dates = ['deleted_at'];
    public function categories()
    {
        return $this->belongsToMany(Category::class)->withPivot('id', 'name');
    }
}
