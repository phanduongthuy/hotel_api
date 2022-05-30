<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;


class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "members";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'image'
    ];

    protected $appends = ['link_image'];

    public function getLinkImageAttribute()
    {
        $link = '';

        if ($this->image) {
            $link = env('APP_URL') . '/storage/' . $this->image;
        }

        return $link;
    }
}
