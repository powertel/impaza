<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemarkActivity extends Model
{    protected $fillable = [
    'activity',
];
    use HasFactory;
    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }
}
 