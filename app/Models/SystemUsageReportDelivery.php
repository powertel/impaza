<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SystemUsageReportDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'trigger_type',
        'status',
        'subject',
        'primary_recipient',
        'recipients',
        'period_start',
        'period_end',
        'started_at',
        'finished_at',
        'error_message',
        'initiated_by',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public static function tableExists(): bool
    {
        try {
            return Schema::hasTable((new static())->getTable());
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
