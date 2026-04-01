<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultSection extends Model
{
    use HasFactory;
    
    protected $table ='fault_section';

    protected $fillable = [
        'fault_id',
        'section_id'

    ];
}
