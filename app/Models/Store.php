<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable=[
        'fault_ref_number',
        'fault_name',
        'requisition_number',
        'ref_Number',
        'materials',
        'SAP_ref'
    ];
}
