<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    use HasFactory;
    protected $fillable=[
        'fault_ref_number',
        'faultType',
        'user_id',
        'fault_id',
        'materials',
        'store_status',
        'SAP_ref'
    ];

    public function fault()
    {
        return $this->belongsTo(Fault::class, 'fault_id')->withDefault();
    }
    public function store_status()
    {
        return $this->belongsTo(StoreStatus::class);
    }
}
