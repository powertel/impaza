<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_request_id',
        'material_id',
        'material_name',
        'unit',
        'quantity_requested',
        'quantity_issued',
        'is_available',
        'remark',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_issued' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function materialRequest()
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function shortfall()
    {
        return max(0, $this->quantity_requested - $this->quantity_issued);
    }

    public function isFullyIssued()
    {
        return $this->quantity_issued >= $this->quantity_requested;
    }
}
