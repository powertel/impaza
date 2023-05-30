<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmedRfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'ConfirmedRFO'
    ];
    public function fault()
    {
        return $this->hasMany(Fault::class);
    }
}

