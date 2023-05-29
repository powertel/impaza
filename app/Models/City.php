<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
    ];

    public function suburbs()
    {
        return $this->hasMany(Suburb::class);
    }

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
    public function pops()
    {
        return $this -> hasManyThrough(Pop::class, Suburb::class,Link::class);
    }

    public function links()
    {
        return $this -> hasManyThrough(Link::class, Suburb::class);
    }

    public function fault()
    {
        return $this->hasOne(Fault::class);
    }
}
