<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PaymentPayPal extends Model
{
    use HasFactory;
    protected $table = 'payments_paypal';

    protected $fillable = [
        'user_id',
        'order_ids',
        'code',
        'money',
        'status',
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
