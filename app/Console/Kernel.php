<?php

namespace App\Console;

use App\Models\SystemUsageReportSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $usageReportSettings = SystemUsageReportSetting::current();

        if (filter_var(env('SCHEDULE_AUTO_ASSIGN', true), FILTER_VALIDATE_BOOLEAN)) {
            $schedule->command('faults:auto-assign')->everyFiveMinutes();
        }
        $schedule->command('accounts:sync')->everyTenMinutes()->withoutOverlapping();

        if ((bool) ($usageReportSettings->enabled ?? true)) {
            $schedule->command('system-usage:email')
                ->mondays()
                ->at($usageReportSettings->send_time ?: env('SYSTEM_USAGE_REPORT_TIME', '07:00'))
                ->withoutOverlapping();
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
