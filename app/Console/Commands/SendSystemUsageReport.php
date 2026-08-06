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
        Log::info('system-usage:email artisan command invoked', [
            'to_override' => (array) $this->option('to'),
            'start_option' => $this->option('start'),
            'end_option' => $this->option('end'),
        ]);

        [$start, $end] = $reportService->resolvePeriod(
            $this->option('start'),
            $this->option('end')
        );

        $recipients = $reportService->resolveRecipients((array) $this->option('to'));

        if (empty($recipients)) {
            Log::warning('system-usage:email aborted - no recipients configured');
            $this->error('No recipients configured. Set SYSTEM_USAGE_REPORT_RECIPIENTS or use --to=');
            return self::FAILURE;
        }

        try {
            Log::info('system-usage:email calling sendReport', [
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
                'recipient_count' => count($recipients),
            ]);

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
            Log::info('system-usage:email completed successfully', [
                'delivery_id' => $result['delivery_id'] ?? null,
                'primary_recipient' => $result['primary_recipient'],
                'recipient_count' => count($result['recipients'] ?? []),
                'period' => $result['report']['period']['label'] ?? null,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('System usage report email failed', [
                'message' => $exception->getMessage(),
                'recipients' => $recipients,
                'trace_snippet' => $exception->getTraceAsString(),
            ]);

            $this->error('Failed to send system usage report.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
