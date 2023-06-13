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
        'requisition_number',
        'faults_id',
        'materials',
        'SAP_ref'
    ];

    public function fault()
    {
        return $this->belongsTo(Fault::class);
    }
}
