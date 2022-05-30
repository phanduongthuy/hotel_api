<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class SupportRequestRespone extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'admin_id',
        'request_support_id',
        'time_response'
    ];

    public function admins()
    {
        return $this->hasOne(Admin::class);
    }
}
