<?php

namespace App\Http\Controllers;

use App\Models\SystemUsageReportSetting;
use App\Services\SystemUsageReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemUsageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports');
    }

    public function edit(SystemUsageReportService $reportService)
    {
        $settings = $reportService->currentSettings();
        $deliveries = $reportService->recentDeliveries();
        $latestDelivery = $deliveries->first();

        return view('reports.system_usage_settings', [
            'settings' => $settings,
            'deliveries' => $deliveries,
            'latestDelivery' => $latestDelivery,
            'defaultMetrics' => [
                'Faults Logged',
                'Remarks Added',
                'Status Updates',
                'Assignments Received',
                'Referrals Made',
                'Surveys Submitted',
            ],
            'monitoredGroups' => [
                'Network Operations / Technician',
                'Customer Experience / Call Centre and Chief Technician',
                'Service Management Centre / Noc Supervisor and Noc',
            ],
        ]);
    }

    public function update(Request $request, SystemUsageReportService $reportService): RedirectResponse
    {
        if (!SystemUsageReportSetting::tableExists()) {
            return redirect()
                ->route('system-usage-settings.edit')
                ->with('error', 'Run the latest database migration before saving system usage report settings.');
        }

        $data = $request->validate([
            'enabled' => 'nullable|boolean',
            'send_time' => 'required|date_format:H:i',
            'recipients' => 'nullable|string',
            'test_recipient' => 'nullable|email',
        ]);

        $settings = $reportService->currentSettings();
        $settings->fill([
            'enabled' => (bool) ($data['enabled'] ?? false),
            'send_time' => $data['send_time'],
            'recipients' => trim((string) ($data['recipients'] ?? '')),
            'test_recipient' => $data['test_recipient'] ?? null,
            'updated_by' => optional($request->user())->id,
        ]);
        $settings->save();

        return redirect()
            ->route('system-usage-settings.edit')
            ->with('success', 'System usage report settings saved successfully.');
    }

    public function sendTest(Request $request, SystemUsageReportService $reportService): RedirectResponse
    {
        if (!SystemUsageReportSetting::tableExists()) {
            return redirect()
                ->route('system-usage-settings.edit')
                ->with('error', 'Run the latest database migration before sending a test system usage report.');
        }

        $data = $request->validate([
            'test_recipient' => 'required|email',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $settings = $reportService->currentSettings();
        $settings->fill([
            'test_recipient' => $data['test_recipient'],
            'updated_by' => optional($request->user())->id,
        ]);
        $settings->save();

        [$start, $end] = $reportService->resolvePeriod(
            $data['start_date'] ?? null,
            $data['end_date'] ?? null
        );

        try {
            $reportService->sendReport([$data['test_recipient']], $start, $end, [
                'trigger_type' => 'manual_test',
                'initiated_by' => optional($request->user())->id,
            ]);

            return redirect()
                ->route('system-usage-settings.edit')
                ->with('success', 'Test system usage report sent successfully to ' . $data['test_recipient'] . '.');
        } catch (\Throwable $exception) {
            Log::error('System usage report test email failed', [
                'message' => $exception->getMessage(),
                'recipient' => $data['test_recipient'],
            ]);

            return redirect()
                ->route('system-usage-settings.edit')
                ->with('error', 'Failed to send test report: ' . $exception->getMessage());
        }
    }
}
