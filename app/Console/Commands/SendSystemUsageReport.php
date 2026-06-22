<?php

namespace App\Console\Commands;

use App\Services\SystemUsageReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendSystemUsageReport extends Command
{
    protected $signature = 'system-usage:email
                            {--to=* : Override recipients}
                            {--start= : Report start date}
                            {--end= : Report end date}';

    protected $description = 'Send the weekly system usage email report';

    public function handle(SystemUsageReportService $reportService): int
    {
        [$start, $end] = $reportService->resolvePeriod(
            $this->option('start'),
            $this->option('end')
        );

        $recipients = $reportService->resolveRecipients((array) $this->option('to'));

        if (empty($recipients)) {
            $this->error('No recipients configured. Set SYSTEM_USAGE_REPORT_RECIPIENTS or use --to=');
            return self::FAILURE;
        }

        try {
            $result = $reportService->sendReport($recipients, $start, $end, [
                'trigger_type' => empty($this->option('to')) ? 'scheduled' : 'cli_override',
                'initiated_by' => null,
            ]);

            $this->info('Preparing weekly system usage report');
            $this->line('Period: ' . $result['report']['period']['label']);
            $this->line('Primary recipient: ' . $result['primary_recipient']);
            if (!empty($result['recipients'])) {
                $this->line('Recipients: ' . implode(', ', $result['recipients']));
            }

            $this->info('System usage report sent successfully.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('System usage report email failed', [
                'message' => $exception->getMessage(),
                'recipients' => $recipients,
            ]);

            $this->error('Failed to send system usage report.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
