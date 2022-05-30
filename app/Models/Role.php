<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permission_ids',
        'is_protected',
        'admin_id'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

}
