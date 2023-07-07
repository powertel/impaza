<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonsForOutage extends Model
{
    use HasFactory;
    protected $fillable = [
        'RFO',
        'faultType_id',
    ];
    public function fault()
    {
        return $this->hasMany(Fault::class);
    }
    public function faultType()
    {
        return $this->belongsTo(Fault::class);
    }

}
