<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPushToken extends Model
{
    protected $table = 'user_push_tokens';

    protected $fillable = [
        'user_id',
        'expo_push_token',
        'platform',
        'device_id',
        'last_seen_at',
    ];
}

