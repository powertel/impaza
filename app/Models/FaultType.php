<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultType extends Model
{
    use HasFactory;
    protected $fillable = [
        'Type',
    ];
    public function reasonForoutage()
    {
        return $this->hasMany(ReasonsForOutage::class);
    }
    public function fault()
    {
        return $this->hasMany(Fault::class);
    }

}
