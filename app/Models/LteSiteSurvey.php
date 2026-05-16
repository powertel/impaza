<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LteSiteSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'survey_date',
        'survey_performed_by',
        'site_name',
        'jc_number',
        'coordinates',
        'latitude',
        'longitude',
        'physical_address',
        'province_region',
        'payload',
        'submitted_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'survey_date' => 'date',
        'submitted_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(LteSiteSurveyPhoto::class);
    }
}
