<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class NotificationPopupGroup extends Model
{
    use HasFactory;

    protected $table = "notification_popup_groups";

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function notificationPopups()
    {
        return $this->hasMany(NotificationPopup::class, 'group_code', 'code');
    }
}
