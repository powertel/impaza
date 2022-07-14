<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'position',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
