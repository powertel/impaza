<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region',
    ];

    public function pops()
    {
        return $this->hasMany(Pop::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(User::class, 'technician_zone', 'zone_id', 'user_id');
    }
}
