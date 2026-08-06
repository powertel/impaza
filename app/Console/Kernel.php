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
        $sendTime = (string) ($usageReportSettings->send_time ?: env('SYSTEM_USAGE_REPORT_TIME', '07:00'));
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $sendTime)) {
            $sendTime = '07:00';
        }
        $isEnabled = (bool) ($usageReportSettings->enabled ?? true);
        $dayLabel = SystemUsageReportSetting::dayOptions()[$sendDay] ?? 'Monday';

        if ($isEnabled) {
            $schedule->command('system-usage:email')
                ->days($sendDay)
                ->at($sendTime)
                ->before(function () use ($sendDay, $dayLabel, $sendTime) {
                    Log::info('Scheduler: system-usage:email STARTING', [
                        'day_value' => $sendDay,
                        'day_label' => $dayLabel,
                        'time' => $sendTime,
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
