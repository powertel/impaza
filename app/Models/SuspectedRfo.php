<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuspectedRfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'SuspectedRFO'
    ];
    public function fault()
    {
        return $this->hasMany(Fault::class);
    }
}
