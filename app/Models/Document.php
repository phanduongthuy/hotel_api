<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';

    const TYPE = [
        'REQUEST' => 0,
        'RESULT' => 1,
    ];

    protected $fillable = [
        'order_id',
        'name',
        'path',
        'total_page',
        'type',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
