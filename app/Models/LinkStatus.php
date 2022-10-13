<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_status'
    ];

    const STATUS_COLOR = [
        'Pending'=>'  #ff8080',
        'Connected'=>'#90EE90',
        'Disconnected'=>'#FFFF00',
        'Decommissioned'=>'#A9A9A9',
    ];
}
