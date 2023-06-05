<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonsForOutage extends Model
{
    use HasFactory;
    protected $fillable = [
        'RFO'
    ];
    public function fault()
    {
        return $this->hasMany(Fault::class);
    }
}
