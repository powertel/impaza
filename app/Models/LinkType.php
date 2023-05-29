<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkType extends Model
{
    use HasFactory;
    protected $fillable = [
        'linkType'
    ];
    public function link()
    {
        return $this->hasMany(Link::class);
    }
}
