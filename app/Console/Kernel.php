<?php

namespace App\Console;

use App\Models\SystemUsageReportSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        if (filter_var(env('SCHEDULE_AUTO_ASSIGN', true), FILTER_VALIDATE_BOOLEAN)) {
            try {
                $schedule->command('faults:auto-assign')->everyFiveMinutes();
            } catch (\Throwable $e) {
                Log::error('Scheduler: failed to register faults:auto-assign', ['message' => $e->getMessage()]);
            }
        }

        try {
            $schedule->command('accounts:sync')->everyTenMinutes()->withoutOverlapping();
        } catch (\Throwable $e) {
            Log::error('Scheduler: failed to register accounts:sync', ['message' => $e->getMessage()]);
        }

        try {
            $this->registerSystemUsageReportSchedule($schedule);
        } catch (\Throwable $e) {
            Log::error('Scheduler: failed to register system-usage:email, using safe fallback', ['message' => $e->getMessage()]);
            try {
                $fallbackDay = (int) env('SYSTEM_USAGE_REPORT_DAY', 1);
                if ($fallbackDay < 1) { $fallbackDay = 1; }
                if ($fallbackDay > 7) { $fallbackDay = 7; }
                $fallbackTime = (string) env('SYSTEM_USAGE_REPORT_TIME', '07:00');
                $schedule->command('system-usage:email')
                    ->days($fallbackDay)
                    ->at($fallbackTime)
                    ->timezone(config('app.timezone', 'Africa/Harare'))
                    ->before(function () use ($fallbackDay, $fallbackTime) {
                        $dayLabel = SystemUsageReportSetting::dayOptions()[$fallbackDay] ?? 'Monday';
                        Log::info('Scheduler: system-usage:email STARTING (fallback schedule)', [
                            'day_label' => $dayLabel,
                            'time' => $fallbackTime,
                        ]);
                    })
                    ->onSuccess(function () {
                        Log::info('Scheduler: system-usage:email COMPLETED (fallback schedule)');
                    })
                    ->onFailure(function () {
                        Log::warning('Scheduler: system-usage:email FAILED (fallback schedule)');
                    });
            } catch (\Throwable $inner) {
                Log::critical('Scheduler: could not register fallback system-usage:email', ['message' => $inner->getMessage()]);
            }
        }
    }

    protected function registerSystemUsageReportSchedule(Schedule $schedule): void
    {
        $usageReportSettings = SystemUsageReportSetting::current();

        $sendDay = (int) ($usageReportSettings->send_day ?? 1);
        if ($sendDay < 1) { $sendDay = 1; }
        if ($sendDay > 7) { $sendDay = 7; }

        $rawTime = (string) ($usageReportSettings->send_time ?: env('SYSTEM_USAGE_REPORT_TIME', '07:00'));
        $sendTime = '07:00';
        if (preg_match('/^(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?$/', (string) $rawTime, $m)) {
            $h = (int) $m[1];
            $i = (int) $m[2];
            if ($h < 0) { $h = 0; }
            if ($h > 23) { $h = 23; }
            if ($i < 0) { $i = 0; }
            if ($i > 59) { $i = 59; }
            $sendTime = sprintf('%02d:%02d', $h, $i);
        }

        $isEnabled = (bool) ($usageReportSettings->enabled ?? true);
        $dayLabel = SystemUsageReportSetting::dayOptions()[$sendDay] ?? 'Monday';

        if ($isEnabled) {
            $schedule->command('system-usage:email')
                ->days($sendDay)
                ->at($sendTime)
                ->timezone(config('app.timezone', 'Africa/Harare'))
                ->before(function () use ($sendDay, $dayLabel, $sendTime) {
                    Log::info('Scheduler: system-usage:email STARTING', [
                        'day_value' => $sendDay,
                        'day_label' => $dayLabel,
                        'time' => $sendTime,
                        'app_timezone' => config('app.timezone'),
                    ]);
                })
                ->onSuccess(function () {
                    Log::info('Scheduler: system-usage:email COMPLETED successfully');
                })
                ->onFailure(function () {
                    Log::warning('Scheduler: system-usage:email FAILED');
                });
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
