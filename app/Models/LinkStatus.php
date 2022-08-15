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
        'Fault is under rectification'=>'#FFFF00',
        'Fault has been cleared by Technician'=>'#A9A9A9',
        'Fault has been cleared by CT'=>'#ADFF2F',
        'Fault has been cleared by NOC'=>'#4682B4'
    ];
}
