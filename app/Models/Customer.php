<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer',
        'account_number',
        'contract_number',
        'account_manager_id',
        'address',
        'contact_number',
    ];

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(AccountManager::class, 'account_manager_id');
    }
}
