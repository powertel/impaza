<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function suburbs()
    {
        return $this->hasMany(Suburb::class);
    }

    public function fault(){
        return $this->belongsTo(Fault::class);
    }

    public function pops()
    {
        return $this -> hasManyThrough(Pop::class, Suburb::class);
    }

    public function faults()
    {
        return $this -> hasManyThrough(Fault::class, Suburb::class ,Pop::class);
    }
}
