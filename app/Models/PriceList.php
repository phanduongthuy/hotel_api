<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image'
    ];
    protected $table = 'price_list';
    protected $appends = ['image_src'];
    public function getImageSrcAttribute()
    {
        $link = '';

        if ($this->image) {
            $link = env('APP_URL') . '/storage/' . $this->image;
        } elseif ($this->avatar_link) {
            $link = $this->avatar_link;
        }

        return $link;
    }
}
