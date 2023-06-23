<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use HasFactory;

    protected $fillable = [
        'remark',
        'user_id',
        'fault_id',
        'remarkActivity_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fault()
    {
        return $this->belongsTo(Fault::class);
    }
    public function remarkActivity()
    {
        return $this->belongsToMany(RemarkActivity::class);
    }
}
