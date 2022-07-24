<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_code',
        'description',
    ];
    const STATUS_COLOR = [
        'Waiting for assessment'=>'  #ff8080',
        'Fault has been assessed'=>'#90EE90',
        'Fault is under rectification'=>'#FFFF00',
        'Fault has been cleared by Technician'=>'#A9A9A9',
        'Fault has been cleared by CT'=>'#ADFF2F',
        'Fault has been cleared by NOC'=>'#4682B4'
    ];

}
