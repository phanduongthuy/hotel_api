<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PaymentVNPAY extends Model
{
    use HasFactory;
    protected $table = 'payments_vnpay';

    protected $fillable = [
        'user_id',
        'order_ids',
        'code',
        'money',
        'content',
        'status',
        'code_bank',
        'time'
    ];

    const STATUS = [
        'UNPAID'        => 0,
        'SUCCESS'       => 1,
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
