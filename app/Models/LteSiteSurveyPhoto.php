<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LteSiteSurveyPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'lte_site_survey_id',
        'label',
        'file_path',
        'mime_type',
        'original_name',
    ];

    public function survey()
    {
        return $this->belongsTo(LteSiteSurvey::class, 'lte_site_survey_id');
    }
}

