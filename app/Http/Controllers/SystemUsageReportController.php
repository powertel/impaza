<?php

namespace App\Http\Controllers;

use App\Models\SystemUsageReportDelivery;
use App\Models\SystemUsageReportSetting;
use App\Services\SystemUsageReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemUsageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports');
    }

    public function edit(Request $request, SystemUsageReportService $reportService)
    {
        $settings = $reportService->currentSettings();
        $deliveries = $this->paginatedDeliveries($request);
        $latestDelivery = $this->latestDelivery();
        $deliveryCount = SystemUsageReportDelivery::tableExists() ? SystemUsageReportDelivery::count() : 0;
        $successCount = SystemUsageReportDelivery::tableExists() ? SystemUsageReportDelivery::where('status', 'sent')->count() : 0;
        $failedCount = SystemUsageReportDelivery::tableExists() ? SystemUsageReportDelivery::where('status', 'failed')->count() : 0;

        return view('reports.system_usage_settings', [
            'settings' => $settings,
            'deliveries' => $deliveries,
            'latestDelivery' => $latestDelivery,
            'deliveryCount' => $deliveryCount,
            'successCount' => $successCount,
            'failedCount' => $failedCount,
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

    protected function paginatedDeliveries(Request $request): LengthAwarePaginator
    {
        if (!SystemUsageReportDelivery::tableExists()) {
            return new LengthAwarePaginator(
                collect(),
                0,
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return DB::table('system_usage_report_deliveries as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.initiated_by')
            ->select(
                'd.id',
                'd.trigger_type',
                'd.status',
                'd.subject',
                'd.primary_recipient',
                'd.recipients',
                'd.period_start',
                'd.period_end',
                'd.started_at',
                'd.finished_at',
                'd.error_message',
                'u.name as initiated_by_name'
            )
            ->orderByDesc('d.started_at')
            ->paginate(10)
            ->withQueryString();
    }

    protected function latestDelivery()
    {
        if (!SystemUsageReportDelivery::tableExists()) {
            return null;
        }

        return DB::table('system_usage_report_deliveries as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.initiated_by')
            ->select(
                'd.id',
                'd.trigger_type',
                'd.status',
                'd.subject',
                'd.primary_recipient',
                'd.recipients',
                'd.period_start',
                'd.period_end',
                'd.started_at',
                'd.finished_at',
                'd.error_message',
                'u.name as initiated_by_name'
            )
            ->orderByDesc('d.started_at')
            ->first();
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
