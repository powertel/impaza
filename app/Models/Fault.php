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
        'accountManager',
        'city_id',
        'suburb_id',
        'pop_id',
        'link_id',
        'suspectedRfo',
        'serviceType',
        'serviceAttribute',
        'remarks',
        'status',
    ];


    public function cities(){
        return $this->hasMany(City::class);
    }

}
