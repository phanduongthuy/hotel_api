<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'order_id',
        'rate_star',
        'content',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class,'order_id','order_id');
    }
}
