<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotificationUser extends Model
{
    protected $table = 'push_notification_user';

    protected $fillable = [
        'push_notification_id',
        'user_id'
    ];
}
