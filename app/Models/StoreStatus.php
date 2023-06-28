<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_status'
    ];

    const STATUS_COLOR = [
        'Pending'=>'  #ff8080',
        'Issued'=>'#90EE90',
        'Denied'=>'#FFFF00',
    ];
    public function store()
    {
        return $this->hasMany(Store::class);
    }
}
