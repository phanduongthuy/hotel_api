<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PaymentMomo extends Model
{
    use HasFactory;
    protected $table = 'payments_momo';

    protected $fillable = [
        'user_id',
        'order_ids',
        'code',
        'store_id',
        'money',
        'content',
        'request_id',
        'result_code',
        'trans_id',
        'time',
        'verification_code'
    ];

    const STATUS = [
        'UNPAID'        => 0,
        'SUCCESS'       => 1,
        'FAILURE'       => 2,
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
