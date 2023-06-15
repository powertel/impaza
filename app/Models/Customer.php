<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer',
        'city_id',
        'suburb_id',
        'pop_id',
    ];

    public function links()
    {
        return $this->hasMany(Link::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function suburb()
    {
        return $this->belongsTo(Suburb::class);
    }
    public function pop()
    {
        return $this->belongsTo(Pop::class);
    }
}
