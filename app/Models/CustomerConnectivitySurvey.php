<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerConnectivitySurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'survey_date',
        'survey_performed_by',
        'customer_name',
        'account_or_jc_number',
        'site_name',
        'coordinates',
        'latitude',
        'longitude',
        'physical_address',
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
        return $this->hasMany(CustomerConnectivitySurveyPhoto::class);
    }
}

