<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suburb extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'suburb',
        'zone_id',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function pops()
    {
        return $this->hasMany(Pop::class);
    }
    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

    public function fault()
    {
        return $this->hasOne(Fault::class);
    }
}
