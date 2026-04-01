<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'suburb_id',
        'pop',
        'zone_id',
    ];
    public function suburb()
    {
        return $this->belongsTo(Suburb::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function fault()
    {
        return $this->hasOne(Fault::class);
    }
    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

}
