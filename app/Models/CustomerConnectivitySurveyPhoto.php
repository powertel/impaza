<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerConnectivitySurveyPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_connectivity_survey_id',
        'label',
        'file_path',
        'mime_type',
        'original_name',
    ];

    public function survey()
    {
        return $this->belongsTo(CustomerConnectivitySurvey::class, 'customer_connectivity_survey_id');
    }
}

