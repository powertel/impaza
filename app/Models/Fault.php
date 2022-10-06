<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fault extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'contactName',
        'phoneNumber',
        'contactEmail',
        'address',
        'accountManager_id',
        'city_id',
        'suburb_id',
        'pop_id',
        'link_id',
        'suspectedRfo',
        'serviceType',
        'serviceAttribute',
        'status_id',
        'confirmedRfo',
        'faultType',
        'priorityLevel',
        'assignedTo',
        'user_id'
    ];

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

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    //relationship of section and faults
    public function sections()
    {
        return $this->belongsToMany(Section::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
