<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class EmailTemplateGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class, 'group_code', 'code');
    }
}
