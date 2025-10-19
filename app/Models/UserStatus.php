<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];
    const STATUS_COLOR = [
        'Assignable'    => '#90EE90',
        'Unassignable'  => '#ff8080',
        'Standby'       => '#FFD700',
        'On Leave'      => '#FFA500',
        'Away'          => '#DDA0DD',
    ];
}
