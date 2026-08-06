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
        'send_day',
        'send_time',
        'recipients',
        'test_recipient',
        'updated_by',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'send_day' => 'integer',
    ];

    protected $appends = [
        'send_day_label',
    ];

    protected static function normalizeDay($raw): int
    {
        $sendDay = is_numeric($raw) ? (int) $raw : null;
        if ($sendDay === null || $sendDay < 1 || $sendDay > 7) {
            $sendDay = (int) env('SYSTEM_USAGE_REPORT_DAY', 1);
        }
        if ($sendDay < 1) {
            $sendDay = 1;
        }
        if ($sendDay > 7) {
            $sendDay = 7;
        }
        return $sendDay;
    }

    protected static function normalizeTime($raw): string
    {
        $time = is_string($raw) && $raw !== '' ? $raw : null;
        if ($time === null) {
            $time = (string) env('SYSTEM_USAGE_REPORT_TIME', '07:00');
        }
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $time)) {
            $time = '07:00';
        }
        return $time;
    }

    protected static function normalizeEnabled($raw): bool
    {
        if (is_bool($raw)) {
            return $raw;
        }
        return filter_var($raw ?? env('SYSTEM_USAGE_REPORT_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
    }

    public function getSendDayAttribute($value): int
    {
        return static::normalizeDay($value);
    }

    public function getSendTimeAttribute($value): string
    {
        return static::normalizeTime($value);
    }

    public function getEnabledAttribute($value): bool
    {
        return static::normalizeEnabled($value);
    }

    public static function current(): self
    {
        $defaults = static::defaults();

        if (!static::tableExists()) {
            return new static($defaults);
        }

        try {
            $instance = static::query()->firstOrCreate(
                ['id' => 1],
                $defaults
            );

            $attrs = method_exists($instance, 'getRawOriginal')
                ? $instance->getRawOriginal()
                : $instance->getAttributes();

            if (!is_array($attrs)) {
                $attrs = [];
            }

            $required = ['send_day', 'send_time', 'enabled', 'recipients', 'test_recipient'];
            foreach ($required as $key) {
                if (!array_key_exists($key, $attrs)) {
                    switch ($key) {
                        case 'send_day':
                            $instance->setAttribute($key, $defaults['send_day']);
                            break;
                        case 'send_time':
                            $instance->setAttribute($key, $defaults['send_time']);
                            break;
                        case 'enabled':
                            $instance->setAttribute($key, $defaults['enabled']);
                            break;
                        case 'recipients':
                            $instance->setAttribute($key, $defaults['recipients']);
                            break;
                        case 'test_recipient':
                            $instance->setAttribute($key, $defaults['test_recipient']);
                            break;
                    }
                }
            }

            return $instance;
        } catch (\Throwable $exception) {
            return new static($defaults);
        }
    }

    public static function defaults(): array
    {
        $sendDay = (int) env('SYSTEM_USAGE_REPORT_DAY', 1);
        if ($sendDay < 1) {
            $sendDay = 1;
        }
        if ($sendDay > 7) {
            $sendDay = 7;
        }

        return [
            'enabled' => filter_var(env('SYSTEM_USAGE_REPORT_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
            'send_day' => $sendDay,
            'send_time' => env('SYSTEM_USAGE_REPORT_TIME', '07:00'),
            'recipients' => env('SYSTEM_USAGE_REPORT_RECIPIENTS', ''),
            'test_recipient' => env('SYSTEM_USAGE_REPORT_TEST_RECIPIENT', 'fjatakalula@powertel.co.zw'),
        ];
    }

    public static function dayOptions(): array
    {
        return [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
    }

    public function getSendDayLabelAttribute(): string
    {
        $day = (int) ($this->send_day ?? 1);
        if ($day < 1) {
            $day = 1;
        }
        if ($day > 7) {
            $day = 7;
        }
        $options = static::dayOptions();
        return $options[$day] ?? 'Monday';
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
