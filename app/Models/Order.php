<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    const STATUS = [
        'REQUEST' => 0,
        'CONFIRMED' => 1,
        'SUCCESS' => 2,
        'CANCEL' => 3,
    ];

    const PAYMENT_STATUS = [
        'UNPAID' => 0,
        'PAID' => 1,
        'WAiTING_PAYMENT' => 2,
    ];

    const TYPE = [
        'OVERNIGHT' => 0,
        'HOURS' => 1,
    ];

    const PAYMENT_TYPE = [
        'PAYMENT_ON_DELIVERY' => 0,
        'PAYMENT_WITH_MOMO' => 1,
        'PAYMENT_WITH_VNPAY' => 2,
        'PAYMENT_WITH_PAYPAL' => 3,
    ];

    const ORDER_BILL_TYPE = [
        'NO_BILL' => 0,
        'VAT_BILL' => 1,
    ];

    protected $fillable = [
        'user_id',
        'customer_name',
        'room_id',
        'type',
        'status',
        'order_date',
        'checkin_time',
        'checkout_time',
        'admin_id',
        'note',
        'payment_status',
        'payment_type',
        'time',
        'price',
        'total_price',
        'code',
        'order_bill_type',
    ];


    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
