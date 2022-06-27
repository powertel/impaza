<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    use HasFactory;

    public function suburb()
    {
        return $this->belongsTo(Suburb::class);
    }

    public function fault()
    {
        return $this->hasOne(Fault::class);
    }
}
