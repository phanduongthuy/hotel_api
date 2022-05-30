<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    const STATUS = [
        'NO_PRICE' => 0,
        'ALREADY_PRICE' => 1,
        'TRANSLATING' => 2,
        'TRANSLATION_DONE' => 3,
        'REVIEWING' => 4,
        'REVIEW_DONE' => 5,
    ];

    const TYPE = [
        'TRANSLATE' => 0,
        'REVIEW' => 1,
    ];

    const PAYMENT_STATUS = [
        'UNPAID' => 0,
        'PAID' => 1,
        'WAiTING_PAYMENT' => 2,
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
        'file_name',
        'status',
        'type',
        'native_language_id',
        'translate_language_id',
        'deadline',
        'return_date',
        'admin_id',
        'note',
        'payment_status',
        'payment_type',
        'total_page',
        'price_per_page',
        'total_price',
        'code',
        'order_bill_type',
        'tax_code',
        'company_name',
        'company_address'
    ];

    public function document()
    {
        return $this->hasOne(Document::class);
    }
    public function result()
    {
        return $this->hasMany(Document::class);
    }

    public function languageNative()
    {
        return $this->belongsTo(Language::class,'native_language_id');
    }

    public function languageTranslate()
    {
        return $this->belongsTo(Language::class,'translate_language_id');
    }
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
