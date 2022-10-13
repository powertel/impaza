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
        'Active'=> '  #90EE90',
        'Inactive'=>'#ff8080',
    ];
}
