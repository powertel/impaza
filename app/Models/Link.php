<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'city_id',
        'suburb_id',
        'pop_id',
        'link_status',
        'contract_number',
        'customer_id',
        'linkType_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function linktype()
    {
        return $this->belongsTo(LinkType::class);
    }
}
