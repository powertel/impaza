<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'unit',
        'quantity_on_hand',
        'reorder_level',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requestItems()
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    public function isLowStock()
    {
        return $this->quantity_on_hand <= $this->reorder_level;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity_on_hand <= reorder_level');
    }
}
