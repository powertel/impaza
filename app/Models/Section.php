<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'section',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
    
    //relationship of section and faults
    public function fault()
    {
        return $this->hasMany(Fault::class);
    }

    //relationship of section and faults
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
