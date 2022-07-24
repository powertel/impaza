<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function positions()
    {
        return $this -> hasManyThrough(Position::class, Section::class);
    }

}
