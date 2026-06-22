<?php

namespace App\Services;

use App\Models\SystemUsageReportDelivery;
use App\Models\SystemUsageReportSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SystemUsageReportService
{
    protected array $metricLabels = [
        'faults_logged' => 'Direct Faults',
        'remarks_added' => 'Remarks Added',
        'status_updates' => 'Status Updates',
        'assignments_received' => 'Assignments Received',
        'referrals_made' => 'Referrals Made',
        'surveys_submitted' => 'Surveys Submitted',
    ];

    protected array $operationalMetricLabels = [
        'assessments_completed' => 'Faults Assessed',
        'technician_resolutions' => 'Faults Rectified',
        'chief_tech_clears' => 'Chief Tech Clears',
        'noc_restorations' => 'NOC Restorations',
        'chief_tech_assignments' => 'Assignments Made',
        'chief_tech_reassignments' => 'Reassignments Made',
        'chief_tech_escalations' => 'Escalations Raised',
        'system_assignments_received' => 'System Assignments',
    ];

    public function resolvePeriod(?string $startInput = null, ?string $endInput = null): array
    {
        if ($startInput || $endInput) {
            $start = $startInput
                ? Carbon::parse($startInput)->startOfDay()
                : Carbon::parse($endInput)->subDays(6)->startOfDay();

            $end = $endInput
                ? Carbon::parse($endInput)->endOfDay()
                : Carbon::parse($startInput)->addDays(6)->endOfDay();

            if ($end->lt($start)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            return [$start, $end];
        }

        $start = now()->subWeek()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        return [$start, $end];
    }

    public function resolveRecipients(array $overrides = []): array
    {
        if (!empty($overrides)) {
            return $this->normalizeEmails($overrides);
        }

        return SystemUsageReportSetting::current()->recipientList();
    }

    public function currentSettings(): SystemUsageReportSetting
    {
        return SystemUsageReportSetting::current();
    }

    public function recentDeliveries(int $limit = 15): Collection
    {
        if (!SystemUsageReportDelivery::tableExists()) {
            return collect();
        }

        try {
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
                ->limit($limit)
                ->get();
        } catch (\Throwable $exception) {
            return collect();
        }
    }

    public function sendReport(array $recipients, Carbon $start, Carbon $end, array $context = []): array
    {
        $recipients = $this->normalizeEmails($recipients);

        if (empty($recipients)) {
            throw new \InvalidArgumentException('No recipients configured for the system usage report.');
        }

        $report = $this->buildReport($start, $end);
        $subject = sprintf('%s: %s', $report['period']['report_title'], $report['period']['label']);

        $primaryRecipient = $recipients[0] ?? null;
        $allRecipients = array_values(array_filter($recipients));
        $delivery = $this->startDeliveryLog([
            'trigger_type' => $context['trigger_type'] ?? 'scheduled',
            'initiated_by' => $context['initiated_by'] ?? null,
            'subject' => $subject,
            'primary_recipient' => $primaryRecipient,
            'recipients' => implode(', ', $allRecipients),
            'period_start' => $start,
            'period_end' => $end,
        ]);

        try {
            foreach ($allRecipients as $recipient) {
                Mail::send('emails.system_usage_report', [
                    'report' => $report,
                ], function ($message) use ($recipient, $subject) {
                    $message->to($recipient)->subject($subject);
                });
            }
        } catch (\Throwable $exception) {
            $this->finishDeliveryLog($delivery, 'failed', $exception->getMessage());
            throw $exception;
        }

        $this->finishDeliveryLog($delivery, 'sent');

        return [
            'report' => $report,
            'subject' => $subject,
            'primary_recipient' => $primaryRecipient,
            'recipients' => $allRecipients,
        ];
    }

    protected function startDeliveryLog(array $attributes): ?SystemUsageReportDelivery
    {
        if (!SystemUsageReportDelivery::tableExists()) {
            return null;
        }

        try {
            return SystemUsageReportDelivery::create([
                'trigger_type' => $attributes['trigger_type'] ?? 'scheduled',
                'status' => 'pending',
                'subject' => $attributes['subject'] ?? null,
                'primary_recipient' => $attributes['primary_recipient'] ?? null,
                'recipients' => $attributes['recipients'] ?? null,
                'period_start' => $attributes['period_start'] ?? null,
                'period_end' => $attributes['period_end'] ?? null,
                'started_at' => now(),
                'initiated_by' => $attributes['initiated_by'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function finishDeliveryLog(?SystemUsageReportDelivery $delivery, string $status, ?string $errorMessage = null): void
    {
        if (!$delivery) {
            return;
        }

        try {
            $delivery->update([
                'status' => $status,
                'finished_at' => now(),
                'error_message' => $errorMessage ? mb_substr($errorMessage, 0, 65535) : null,
            ]);
        } catch (\Throwable $exception) {
            // Ignore history write failures so they do not block email delivery.
        }
    }

    public function buildReport(Carbon $start, Carbon $end): array
    {
        $users = $this->monitoredUsers();
        $userIds = $users->pluck('id')->all();

        $metrics = $this->usageMetricsByUser($userIds, $start, $end);
        $operationalMetrics = $this->operationalMetricsByUser($userIds, $start, $end);

        $users = $users->map(function (array $user) use ($metrics, $operationalMetrics) {
            $usage = [];
            foreach (array_keys($this->metricLabels) as $metricKey) {
                $usage[$metricKey] = (int) ($metrics[$metricKey][$user['id']] ?? 0);
            }

            $operational = [];
            foreach (array_keys($this->operationalMetricLabels) as $metricKey) {
                $operational[$metricKey] = (int) ($operationalMetrics[$metricKey][$user['id']] ?? 0);
            }

            $user['usage'] = $usage;
            $user['operational'] = $operational;
            $user['total_actions'] = array_sum($usage);
            $user['active'] = $user['total_actions'] > 0;
            $user['section_label'] = $user['group_label'];
            $user['section_role_label'] = trim(sprintf('%s / %s', $user['group_label'], $user['role_label']));

            return $user;
        })->sortByDesc('total_actions')->values();

        return [
            'generated_at' => now(),
            'period' => [
                'start' => $start,
                'end' => $end,
                'label' => sprintf('%s to %s', $start->format('d M Y'), $end->format('d M Y')),
                ...$this->periodDescriptor($start, $end),
            ],
            'metric_labels' => $this->metricLabels,
            'operational_metric_labels' => $this->operationalMetricLabels,
            'summary' => $this->summaryForUsers($users),
            'executive_observations' => $this->executiveObservations($users),
            'methodology' => $this->methodologyNotes(),
            'groups' => $this->groupBreakdown($users),
            'regions' => $this->regionBreakdown($users),
            'operational_profiles' => $this->operationalProfiles($users),
            'top_users' => $users->take(10)->values()->all(),
            'users' => $users->values()->all(),
        ];
    }

    protected function periodDescriptor(Carbon $start, Carbon $end): array
    {
        $normalizedStart = $start->copy()->startOfDay();
        $normalizedEnd = $end->copy()->endOfDay();

        $isWeekly = $normalizedStart->copy()->startOfWeek(Carbon::MONDAY)->isSameDay($normalizedStart)
            && $normalizedEnd->copy()->endOfWeek(Carbon::SUNDAY)->isSameDay($normalizedEnd)
            && $normalizedStart->diffInDays($normalizedEnd) === 6;

        return [
            'is_weekly' => $isWeekly,
            'report_title' => $isWeekly
                ? 'Impazamon Weekly System Usage Report'
                : 'Impazamon System Usage Report',
        ];
    }

    protected function monitoredUsers(): Collection
    {
        $users = DB::table('users')
            ->leftJoin('sections', 'users.section_id', '=', 'sections.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.region',
                'sections.section as section_name',
                'positions.position as position_name',
                DB::raw("GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ', ') as role_names")
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.region',
                'sections.section',
                'positions.position'
            )
            ->get()
            ->map(function ($user) {
                $roles = collect(explode(',', (string) ($user->role_names ?? '')))
                    ->map(fn ($role) => trim((string) $role))
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'id' => (int) $user->id,
                    'name' => (string) $user->name,
                    'email' => (string) $user->email,
                    'region' => $this->normalizeRegion($user->region),
                    'section_name' => (string) ($user->section_name ?? 'Unassigned Section'),
                    'position_name' => (string) ($user->position_name ?? ''),
                    'roles' => $roles,
                ];
            })
            ->map(function (array $user) {
                $match = $this->matchGroup($user);
                $user['group_key'] = $match['group_key'] ?? null;
                $user['group_label'] = $match['group_label'] ?? 'Other';
                $user['role_key'] = $match['role_key'] ?? null;
                $user['role_label'] = $match['role_label'] ?? 'Other';
                $user['profile_key'] = $match['profile_key'] ?? null;

                return $user;
            })
            ->filter(fn (array $user) => !empty($user['group_key']))
            ->values();

        return $users;
    }

    protected function usageMetricsByUser(array $userIds, Carbon $start, Carbon $end): array
    {
        if (empty($userIds)) {
            return collect(array_keys($this->metricLabels))
                ->mapWithKeys(fn ($key) => [$key => []])
                ->all();
        }

        return [
            'faults_logged' => $this->pluckGroupedCounts('faults', 'user_id', 'created_at', $userIds, $start, $end),
            'remarks_added' => $this->pluckGroupedCounts('remarks', 'user_id', 'created_at', $userIds, $start, $end, 'fault_id'),
            'status_updates' => $this->pluckGroupedCounts('fault_stage_logs', 'started_by', 'started_at', $userIds, $start, $end, 'fault_id'),
            'assignments_received' => $this->pluckGroupedCounts('fault_assignments', 'user_id', 'assigned_at', $userIds, $start, $end, 'fault_id'),
            'referrals_made' => $this->pluckGroupedCounts('fault_referrals', 'referred_by', 'started_at', $userIds, $start, $end, 'fault_id'),
            'surveys_submitted' => $this->pluckSurveyCounts($userIds, $start, $end),
        ];
    }

    protected function operationalMetricsByUser(array $userIds, Carbon $start, Carbon $end): array
    {
        if (empty($userIds)) {
            return collect(array_keys($this->operationalMetricLabels))
                ->mapWithKeys(fn ($key) => [$key => []])
                ->all();
        }

        $statusIds = $this->statusIds();
        $remarkActivityIds = $this->remarkActivityIds();
        $assignmentSplit = $this->pluckSystemAssignmentSplit($userIds, $start, $end, [
            $remarkActivityIds['chief_tech_assign'] ?? 0,
            $remarkActivityIds['chief_tech_reassign'] ?? 0,
        ]);

        return [
            'assessments_completed' => $this->pluckStatusCounts($userIds, $statusIds['assessed'], $start, $end),
            'technician_resolutions' => $this->pluckStatusCounts($userIds, $statusIds['technician_cleared'], $start, $end),
            'chief_tech_clears' => $this->pluckStatusCounts($userIds, $statusIds['chief_tech_cleared'], $start, $end),
            'noc_restorations' => $this->pluckStatusCounts($userIds, $statusIds['noc_cleared'], $start, $end),
            'chief_tech_assignments' => $this->pluckRemarkActivityCounts(
                $userIds,
                [$remarkActivityIds['chief_tech_assign'] ?? 0],
                $start,
                $end
            ),
            'chief_tech_reassignments' => $this->pluckRemarkActivityCounts(
                $userIds,
                [$remarkActivityIds['chief_tech_reassign'] ?? 0],
                $start,
                $end
            ),
            'chief_tech_escalations' => $this->pluckStatusCounts(
                $userIds,
                [$statusIds['escalated'], $statusIds['manager_escalated']],
                $start,
                $end
            ),
            'system_assignments_received' => $assignmentSplit['system'],
        ];
    }

    protected function pluckGroupedCounts(
        string $table,
        string $userColumn,
        string $dateColumn,
        array $userIds,
        Carbon $start,
        Carbon $end,
        ?string $faultColumn = null
    ): array {
        $query = DB::table($table . ' as source')
            ->whereIn('source.' . $userColumn, $userIds)
            ->whereBetween('source.' . $dateColumn, [$start, $end]);

        if ($table === 'faults') {
            $query->whereNull('source.root_fault_id');
            $this->excludePopImpactedStatus($query, 'source.status_id');
        } elseif ($faultColumn) {
            $query->join('faults as f', 'f.id', '=', 'source.' . $faultColumn);
            $query->whereNull('f.root_fault_id');
            $this->excludePopImpactedStatus($query, 'f.status_id');
        }

        return $query
            ->select('source.' . $userColumn, DB::raw('COUNT(*) as aggregate'))
            ->groupBy('source.' . $userColumn)
            ->pluck('aggregate', 'source.' . $userColumn)
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    protected function pluckSurveyCounts(array $userIds, Carbon $start, Carbon $end): array
    {
        return DB::table('lte_site_surveys')
            ->whereIn('user_id', $userIds)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('submitted_at', [$start, $end])
                    ->orWhere(function ($fallback) use ($start, $end) {
                        $fallback->whereNull('submitted_at')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->select('user_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('user_id')
            ->pluck('aggregate', 'user_id')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    protected function pluckStatusCounts(array $userIds, int|array $statusIds, Carbon $start, Carbon $end): array
    {
        $statusIds = collect((array) $statusIds)
            ->filter(fn ($value) => (int) $value > 0)
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        if (empty($statusIds)) {
            return [];
        }

        $query = DB::table('fault_stage_logs')
            ->join('faults as f', 'f.id', '=', 'fault_stage_logs.fault_id')
            ->whereIn('fault_stage_logs.started_by', $userIds)
            ->whereIn('fault_stage_logs.status_id', $statusIds)
            ->whereBetween('fault_stage_logs.started_at', [$start, $end])
            ->whereNull('f.root_fault_id');

        $this->excludePopImpactedStatus($query, 'f.status_id');

        return $query
            ->select('fault_stage_logs.started_by', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('fault_stage_logs.started_by')
            ->pluck('aggregate', 'fault_stage_logs.started_by')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    protected function pluckRemarkActivityCounts(array $userIds, array $activityIds, Carbon $start, Carbon $end): array
    {
        $activityIds = collect($activityIds)
            ->filter(fn ($value) => (int) $value > 0)
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        if (empty($activityIds)) {
            return [];
        }

        $query = DB::table('remarks')
            ->join('faults as f', 'f.id', '=', 'remarks.fault_id')
            ->whereIn('remarks.user_id', $userIds)
            ->whereIn('remarks.remarkActivity_id', $activityIds)
            ->whereBetween('remarks.created_at', [$start, $end])
            ->whereNull('f.root_fault_id');

        $this->excludePopImpactedStatus($query, 'f.status_id');

        return $query
            ->select('remarks.user_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('remarks.user_id')
            ->pluck('aggregate', 'remarks.user_id')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    protected function pluckSystemAssignmentSplit(
        array $userIds,
        Carbon $start,
        Carbon $end,
        array $manualRemarkActivityIds
    ): array {
        $manualRemarkActivityIds = collect($manualRemarkActivityIds)
            ->filter(fn ($value) => (int) $value > 0)
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        $query = DB::table('fault_assignments as fa')
            ->join('faults as f', 'f.id', '=', 'fa.fault_id')
            ->whereIn('fa.user_id', $userIds)
            ->whereBetween('fa.assigned_at', [$start, $end])
            ->whereNull('f.root_fault_id')
            ->select(
                'fa.id',
                'fa.user_id',
                DB::raw(sprintf(
                    'MAX(CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END) as has_manual_assignment_note'
                ))
            );

        $this->excludePopImpactedStatus($query, 'f.status_id');

        if (!empty($manualRemarkActivityIds)) {
            $query->leftJoin('remarks as r', function ($join) use ($manualRemarkActivityIds) {
                $join->on('r.fault_id', '=', 'fa.fault_id')
                    ->whereIn('r.remarkActivity_id', $manualRemarkActivityIds)
                    ->whereRaw('ABS(TIMESTAMPDIFF(MINUTE, r.created_at, fa.assigned_at)) <= 10');
            });
        }

        $rows = $query
            ->groupBy('fa.id', 'fa.user_id')
            ->get();

        $system = [];
        $manual = [];

        foreach ($rows as $row) {
            $userId = (int) $row->user_id;
            $isManual = (int) ($row->has_manual_assignment_note ?? 0) === 1;

            if ($isManual) {
                $manual[$userId] = ($manual[$userId] ?? 0) + 1;
                continue;
            }

            $system[$userId] = ($system[$userId] ?? 0) + 1;
        }

        return [
            'system' => $system,
            'manual' => $manual,
        ];
    }

    protected function summaryForUsers(Collection $users): array
    {
        $totals = $this->metricTotals($users);

        return [
            'monitored_users' => $users->count(),
            'active_users' => $users->where('active', true)->count(),
            'regions' => $users->pluck('region')->unique()->count(),
            'total_actions' => array_sum($totals),
            'metrics' => $totals,
        ];
    }

    protected function groupBreakdown(Collection $users): array
    {
        $groups = collect($this->monitoredGroups())
            ->map(function (array $group) use ($users) {
                $groupUsers = $users->where('group_key', $group['key'])->values();

                return [
                    'key' => $group['key'],
                    'label' => $group['label'],
                    'monitored_users' => $groupUsers->count(),
                    'active_users' => $groupUsers->where('active', true)->count(),
                    'total_actions' => $groupUsers->sum('total_actions'),
                    'metrics' => $this->metricTotals($groupUsers),
                    'roles' => $this->roleBreakdown($groupUsers),
                    'regions' => $this->regionBreakdown($groupUsers),
                    'top_users' => $groupUsers->sortByDesc('total_actions')->take(5)->values()->all(),
                ];
            })
            ->filter(fn (array $group) => $group['monitored_users'] > 0)
            ->sortByDesc('total_actions')
            ->values()
            ->all();

        return $groups;
    }

    protected function roleBreakdown(Collection $users): array
    {
        return $users
            ->groupBy(fn (array $user) => $user['role_key'])
            ->map(function (Collection $roleUsers) {
                $roleUsers = $roleUsers->sortByDesc('total_actions')->values();
                $firstUser = $roleUsers->first();

                return [
                    'key' => $firstUser['role_key'],
                    'label' => $firstUser['role_label'],
                    'profile_key' => $firstUser['profile_key'],
                    'monitored_users' => $roleUsers->count(),
                    'active_users' => $roleUsers->where('active', true)->count(),
                    'total_actions' => $roleUsers->sum('total_actions'),
                    'metrics' => $this->metricTotals($roleUsers),
                    'operational_metrics' => $this->operationalTotals($roleUsers),
                    'top_users' => $roleUsers->take(5)->values()->all(),
                ];
            })
            ->sortByDesc('total_actions')
            ->values()
            ->all();
    }

    protected function regionBreakdown(Collection $users): array
    {
        return $users
            ->groupBy(fn (array $user) => $user['region'])
            ->map(function (Collection $regionUsers, string $region) {
                $regionUsers = $regionUsers->sortByDesc('total_actions')->values();

                return [
                    'region' => $region,
                    'monitored_users' => $regionUsers->count(),
                    'active_users' => $regionUsers->where('active', true)->count(),
                    'total_actions' => $regionUsers->sum('total_actions'),
                    'metrics' => $this->metricTotals($regionUsers),
                    'top_users' => $regionUsers->take(5)->values()->all(),
                ];
            })
            ->sortByDesc('total_actions')
            ->values()
            ->all();
    }

    protected function metricTotals(Collection $users): array
    {
        $totals = [];

        foreach (array_keys($this->metricLabels) as $metricKey) {
            $totals[$metricKey] = (int) $users->sum(fn (array $user) => (int) ($user['usage'][$metricKey] ?? 0));
        }

        return $totals;
    }

    protected function operationalTotals(Collection $users): array
    {
        $totals = [];

        foreach (array_keys($this->operationalMetricLabels) as $metricKey) {
            $totals[$metricKey] = (int) $users->sum(fn (array $user) => (int) ($user['operational'][$metricKey] ?? 0));
        }

        return $totals;
    }

    protected function executiveObservations(Collection $users): array
    {
        if ($users->isEmpty()) {
            return [
                'No monitored users matched the configured sections and roles for the selected reporting period.',
            ];
        }

        $summary = $this->summaryForUsers($users);
        $topRegion = collect($this->regionBreakdown($users))->first();
        $topUser = $users->first();

        $observations = [
            sprintf(
                'The report covers %s monitored users across %s regions, with %s users recording at least one tracked action.',
                number_format($summary['monitored_users']),
                number_format($summary['regions']),
                number_format($summary['active_users'])
            ),
            sprintf(
                'A total of %s system actions were recorded during the reporting window, including logging, remarks, status updates, assignments, referrals, and survey submissions.',
                number_format($summary['total_actions'])
            ),
        ];

        if (!empty($topRegion)) {
            $observations[] = sprintf(
                '%s recorded the highest action volume with %s total tracked actions.',
                $topRegion['region'],
                number_format($topRegion['total_actions'])
            );
        }

        if (!empty($topUser)) {
            $observations[] = sprintf(
                '%s was the most active monitored user with %s recorded actions.',
                $topUser['name'],
                number_format($topUser['total_actions'])
            );
        }

        return $observations;
    }

    protected function methodologyNotes(): array
    {
        return [
            'User scope is limited to Network Operations technicians, Customer Experience call centre and chief technician roles, and Service Management Centre NOC or NOC supervisor roles.',
            'Regional totals are derived from each monitored user\'s recorded region and are rolled up across the selected reporting period.',
            'Assessment, technician attendance, chief technician clearance, and NOC restoration figures are counted from formal lifecycle status transitions captured in system stage logs.',
            'System assignment counts for technicians are based on assignment records and exclude chief technician manual assignment events when a matching chief-tech assignment note is recorded alongside the dispatch.',
            'POP-impacted child faults are excluded so the email reflects direct, Direct Faults only and does not inflate usage with automatically generated downstream impact records.',
        ];
    }

    protected function operationalProfiles(Collection $users): array
    {
        return collect($this->operationalProfileConfigs())
            ->map(function (array $profile) use ($users) {
                $profileUsers = $users
                    ->where('profile_key', $profile['key'])
                    ->sortByDesc(fn (array $user) => $this->profileSortValue($user, $profile['sort_metric']))
                    ->values();

                if ($profileUsers->isEmpty()) {
                    return null;
                }

                return [
                    'key' => $profile['key'],
                    'title' => $profile['title'],
                    'subtitle' => $profile['subtitle'],
                    'description' => $profile['description'],
                    'monitored_users' => $profileUsers->count(),
                    'active_users' => $profileUsers->where('active', true)->count(),
                    'metrics' => $this->aggregateProfileMetrics($profileUsers, array_keys($profile['metric_labels'])),
                    'metric_labels' => $profile['metric_labels'],
                    'detail_columns' => $profile['detail_columns'],
                    'regional_profiles' => $this->operationalProfileRegions($profileUsers, $profile),
                    'top_users' => $profileUsers->take(6)->values()->map(function (array $user) use ($profile) {
                        $details = [
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'region' => $user['region'],
                            'role_label' => $user['role_label'],
                            'group_label' => $user['group_label'],
                            'section_label' => $user['section_label'],
                            'section_role_label' => $user['section_role_label'],
                        ];

                        foreach ($profile['detail_columns'] as $column) {
                            $details[$column['key']] = $this->metricValueForUser($user, $column['key']);
                        }

                        return $details;
                    })->all(),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function operationalProfileRegions(Collection $users, array $profile): array
    {
        $detailColumns = collect($profile['detail_columns'])
            ->reject(fn (array $column) => $column['key'] === 'region')
            ->values()
            ->all();

        return $users
            ->groupBy(fn (array $user) => $user['region'])
            ->map(function (Collection $regionUsers, string $region) use ($profile, $detailColumns) {
                $regionUsers = $regionUsers
                    ->sortByDesc(fn (array $user) => $this->profileSortValue($user, $profile['sort_metric']))
                    ->values();

                return [
                    'region' => $region,
                    'monitored_users' => $regionUsers->count(),
                    'active_users' => $regionUsers->where('active', true)->count(),
                    'metrics' => $this->aggregateProfileMetrics($regionUsers, array_keys($profile['metric_labels'])),
                    'detail_columns' => $detailColumns,
                    'top_users' => $regionUsers->take(6)->values()->map(function (array $user) use ($detailColumns) {
                        $details = [
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'region' => $user['region'],
                        ];

                        foreach ($detailColumns as $column) {
                            $details[$column['key']] = $this->metricValueForUser($user, $column['key']);
                        }

                        return $details;
                    })->all(),
                ];
            })
            ->sortBy(fn (array $regionProfile) => $this->regionSortOrder($regionProfile['region']))
            ->values()
            ->all();
    }

    protected function regionSortOrder(string $region): int
    {
        return match ($region) {
            'East' => 0,
            'West' => 1,
            default => 2,
        };
    }

    protected function aggregateProfileMetrics(Collection $users, array $metricKeys): array
    {
        $totals = [];

        foreach ($metricKeys as $metricKey) {
            if ($metricKey === 'active_users') {
                $totals[$metricKey] = $users->where('active', true)->count();
                continue;
            }

            if ($metricKey === 'monitored_users') {
                $totals[$metricKey] = $users->count();
                continue;
            }

            $totals[$metricKey] = (int) $users->sum(fn (array $user) => $this->metricValueForUser($user, $metricKey));
        }

        return $totals;
    }

    protected function metricValueForUser(array $user, string $metricKey): int
    {
        if ($metricKey === 'total_actions') {
            return (int) ($user['total_actions'] ?? 0);
        }

        return (int) ($user['operational'][$metricKey] ?? $user['usage'][$metricKey] ?? 0);
    }

    protected function profileSortValue(array $user, string $metricKey): int
    {
        return $this->metricValueForUser($user, $metricKey) ?: (int) ($user['total_actions'] ?? 0);
    }

    protected function matchGroup(array $user): ?array
    {
        $sectionName = $this->normalizeLabel($user['section_name'] ?? '');
        $candidates = collect([$user['position_name'] ?? ''])
            ->merge($user['roles'] ?? [])
            ->map(fn ($value) => $this->normalizeLabel($value))
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($this->monitoredGroups() as $group) {
            if (!in_array($sectionName, $group['sections'], true)) {
                continue;
            }

            foreach ($group['roles'] as $role) {
                foreach ($candidates as $candidate) {
                    if (in_array($candidate, $role['aliases'], true)) {
                        return [
                            'group_key' => $group['key'],
                            'group_label' => $group['label'],
                            'role_key' => $role['key'],
                            'role_label' => $role['label'],
                            'profile_key' => $role['profile'],
                        ];
                    }
                }
            }
        }

        return null;
    }

    protected function monitoredGroups(): array
    {
        return [
            [
                'key' => 'network-operations',
                'label' => 'Network Operations',
                'sections' => ['network operations'],
                'roles' => [
                    [
                        'key' => 'network-operations-technician',
                        'label' => 'Technician',
                        'profile' => 'technician',
                        'aliases' => ['technician', 'techniciain'],
                    ],
                ],
            ],
            [
                'key' => 'customer-experience',
                'label' => 'Customer Experience',
                'sections' => ['customer experience'],
                'roles' => [
                    [
                        'key' => 'customer-experience-call-centre',
                        'label' => 'Call Centre',
                        'profile' => 'call_centre',
                        'aliases' => ['call centre'],
                    ],
                    [
                        'key' => 'customer-experience-chief-technician',
                        'label' => 'Chief Technician',
                        'profile' => 'chief_technician',
                        'aliases' => ['chief technician'],
                    ],
                ],
            ],
            [
                'key' => 'service-management-centre',
                'label' => 'Service Management Centre',
                'sections' => ['service management centre'],
                'roles' => [
                    [
                        'key' => 'service-management-centre-noc',
                        'label' => 'NOC / NOC Supervisor',
                        'profile' => 'noc',
                        'aliases' => ['noc supervisor', 'noc', 'network controller'],
                    ],
                ],
            ],
        ];
    }

    protected function operationalProfileConfigs(): array
    {
        return [
            [
                'key' => 'call_centre',
                'title' => 'Customer Experience: Call Centre',
                'subtitle' => 'Reporting and support activity',
                'description' => 'Captures intake and supporting activity recorded by monitored call centre officers.',
                'sort_metric' => 'total_actions',
                'metric_labels' => [
                    'monitored_users' => 'Monitored Users',
                    'active_users' => 'Active Users',
                    'faults_logged' => 'Direct Faults',
                    'remarks_added' => 'Remarks Added',
                    'total_actions' => 'Total Actions',
                ],
                'detail_columns' => [
                    ['key' => 'region', 'label' => 'Region'],
                    ['key' => 'faults_logged', 'label' => 'Direct Faults'],
                    ['key' => 'remarks_added', 'label' => 'Remarks'],
                    ['key' => 'total_actions', 'label' => 'Actions'],
                ],
            ],
            [
                'key' => 'technician',
                'title' => 'Network Operations: Technicians',
                'subtitle' => 'Assignment, rectification, and action activity',
                'description' => 'Highlights technician workload, including faults routed through the assignment engine, rectification closures, and all recorded operational actions.',
                'sort_metric' => 'system_assignments_received',
                'metric_labels' => [
                    'monitored_users' => 'Monitored Users',
                    'active_users' => 'Active Users',
                    'system_assignments_received' => 'System Assignments',
                    'assignments_received' => 'All Assignments',
                    'technician_resolutions' => 'Faults Rectified',
                    'total_actions' => 'Total Actions',
                ],
                'detail_columns' => [
                    ['key' => 'region', 'label' => 'Region'],
                    ['key' => 'system_assignments_received', 'label' => 'System Assigned'],
                    ['key' => 'assignments_received', 'label' => 'All Assigned'],
                    ['key' => 'technician_resolutions', 'label' => 'Rectified'],
                    ['key' => 'total_actions', 'label' => 'Actions'],
                ],
            ],
            [
                'key' => 'chief_technician',
                'title' => 'Customer Experience: Chief Technicians',
                'subtitle' => 'Control, dispatch, and escalation activity',
                'description' => 'Reports the chief technician actions that materially move work forward, including assignments, reassignments, stage clearances, and escalations.',
                'sort_metric' => 'chief_tech_assignments',
                'metric_labels' => [
                    'monitored_users' => 'Monitored Users',
                    'active_users' => 'Active Users',
                    'chief_tech_assignments' => 'Assignments Made',
                    'chief_tech_reassignments' => 'Reassignments',
                    'chief_tech_clears' => 'Chief Tech Clears',
                    'chief_tech_escalations' => 'Escalations Raised',
                    'total_actions' => 'Total Actions',
                ],
                'detail_columns' => [
                    ['key' => 'region', 'label' => 'Region'],
                    ['key' => 'chief_tech_assignments', 'label' => 'Assigned'],
                    ['key' => 'chief_tech_reassignments', 'label' => 'Reassigned'],
                    ['key' => 'chief_tech_clears', 'label' => 'Cleared'],
                    ['key' => 'chief_tech_escalations', 'label' => 'Escalated'],
                    ['key' => 'total_actions', 'label' => 'Actions'],
                ],
            ],
            [
                'key' => 'noc',
                'title' => 'Service Management Centre: NOC and NOC Supervisors',
                'subtitle' => 'Assessment, rectification, and control activity',
                'description' => 'Shows how NOC teams used the platform to assess incoming faults, drive final restoration activity, and execute supporting control-room actions.',
                'sort_metric' => 'noc_restorations',
                'metric_labels' => [
                    'monitored_users' => 'Monitored Users',
                    'active_users' => 'Active Users',
                    'assessments_completed' => 'Faults Assessed',
                    'noc_restorations' => 'Faults Rectified',
                    'status_updates' => 'Status Updates',
                    'total_actions' => 'Total Actions',
                ],
                'detail_columns' => [
                    ['key' => 'region', 'label' => 'Region'],
                    ['key' => 'assessments_completed', 'label' => 'Assessed'],
                    ['key' => 'noc_restorations', 'label' => 'Rectified'],
                    ['key' => 'status_updates', 'label' => 'Status Updates'],
                    ['key' => 'total_actions', 'label' => 'Actions'],
                ],
            ],
        ];
    }

    protected function statusIds(): array
    {
        static $statusIds = null;

        if ($statusIds !== null) {
            return $statusIds;
        }

        $lookup = DB::table('statuses')
            ->whereIn('status_code', ['ASD', 'CLT', 'CLC', 'CLN', 'ESC', 'MES'])
            ->pluck('id', 'status_code');

        $statusIds = [
            'assessed' => (int) ($lookup['ASD'] ?? 2),
            'technician_cleared' => (int) ($lookup['CLT'] ?? 4),
            'chief_tech_cleared' => (int) ($lookup['CLC'] ?? 5),
            'noc_cleared' => (int) ($lookup['CLN'] ?? 6),
            'escalated' => (int) ($lookup['ESC'] ?? 10),
            'manager_escalated' => (int) ($lookup['MES'] ?? 11),
        ];

        return $statusIds;
    }

    protected function remarkActivityIds(): array
    {
        static $remarkActivityIds = null;

        if ($remarkActivityIds !== null) {
            return $remarkActivityIds;
        }

        $lookup = DB::table('remark_activities')
            ->whereIn('activity', ['ON CHIEF-TECH ASSIGN', 'ON CHIEF-TECH REASSIGN'])
            ->pluck('id', 'activity');

        $remarkActivityIds = [
            'chief_tech_assign' => (int) ($lookup['ON CHIEF-TECH ASSIGN'] ?? 0),
            'chief_tech_reassign' => (int) ($lookup['ON CHIEF-TECH REASSIGN'] ?? 0),
        ];

        return $remarkActivityIds;
    }

    protected function popImpactedStatusId(): int
    {
        static $popImpactedStatusId = null;

        if ($popImpactedStatusId !== null) {
            return $popImpactedStatusId;
        }

        $popImpactedStatusId = (int) (DB::table('statuses')->where('status_code', 'POI')->value('id') ?? 0);

        return $popImpactedStatusId;
    }

    protected function excludePopImpactedStatus($query, string $statusColumn): void
    {
        $popImpactedStatusId = $this->popImpactedStatusId();

        if ($popImpactedStatusId > 0) {
            $query->where($statusColumn, '!=', $popImpactedStatusId);
        }
    }

    protected function normalizeEmails(array $values): array
    {
        return collect($values)
            ->flatMap(function ($value) {
                return preg_split('/[\s,;]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            })
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeLabel(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    protected function normalizeRegion(?string $region): string
    {
        $region = strtolower(trim((string) $region));

        return match ($region) {
            'east', 'eastern', '0' => 'East',
            'west', 'western', '1' => 'West',
            '', 'null' => 'Unassigned',
            default => ucfirst($region),
        };
    }
}
