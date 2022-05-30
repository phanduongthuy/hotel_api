<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class SupportRequest extends Model
{
    use HasFactory;

    const STATUS = [
        'DONE' => 1,
        'PENDING' => 0
    ];

    protected $fillable = [
        'name',
        'email',
        'content',
        'status',
    ];

    public function response()
    {
        return $this->hasOne(SupportRequestRespone::class, 'request_support_id', '');
    }
}
