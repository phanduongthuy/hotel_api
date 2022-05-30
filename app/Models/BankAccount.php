<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'bank_accounts';

    protected $fillable = [
        'account_number',
        'account_holder',
        'bank_name',
        'phone',
        'email',
        'is_delete',
        'note'
    ];
    protected $dates = ['deleted_at'];
    const STATUS = [
        'ACTIVATE' => true,
        'DEACTIVATE' => false
    ];
}
