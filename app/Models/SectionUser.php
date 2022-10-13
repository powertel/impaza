<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'user_id'
    ];
}
