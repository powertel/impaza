<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SystemUsageReportSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'enabled',
        'send_time',
        'recipients',
        'test_recipient',
        'updated_by',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function current(): self
    {
        $defaults = static::defaults();

        if (!static::tableExists()) {
            return new static($defaults);
        }

        try {
            return static::query()->firstOrCreate(
                ['id' => 1],
                $defaults
            );
        } catch (\Throwable $exception) {
            return new static($defaults);
        }
    }

    public static function defaults(): array
    {
        return [
            'enabled' => filter_var(env('SYSTEM_USAGE_REPORT_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
            'send_time' => env('SYSTEM_USAGE_REPORT_TIME', '07:00'),
            'recipients' => env('SYSTEM_USAGE_REPORT_RECIPIENTS', ''),
            'test_recipient' => env('SYSTEM_USAGE_REPORT_TEST_RECIPIENT', 'fjatakalula@powertel.co.zw'),
        ];
    }

    public static function tableExists(): bool
    {
        try {
            return Schema::hasTable((new static())->getTable());
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function recipientList(): array
    {
        return collect(preg_split('/[\s,;]+/', (string) ($this->recipients ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [])
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }
}
