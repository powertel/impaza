<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_number',
        'contract_number',
        'jcc_number',
        'sapcodes',
        'comment',
        'quantity',
        'service_type',
        'capacity',
        'city_id',
        'suburb_id',
        'pop_id',
        'linkType_id',
        'link_status',
        'link',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function suburb()
    {
        return $this->belongsTo(Suburb::class);
    }

    public function pop()
    {
        return $this->belongsTo(Pop::class);
    }

    public function linkType()
    {
        return $this->belongsTo(LinkType::class, 'linkType_id');
    }
}
