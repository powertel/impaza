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
        'faults_logged' => 'Faults Logged',
        'remarks_added' => 'Remarks Added',
        'status_updates' => 'Status Updates',
        'assignments_received' => 'Assignments Received',
        'referrals_made' => 'Referrals Made',
        'surveys_submitted' => 'Surveys Submitted',
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
        $subject = sprintf('Impaza Weekly System Usage Report: %s', $report['period']['label']);

        $primaryRecipient = array_shift($recipients);
        $allRecipients = array_values(array_filter(array_merge([$primaryRecipient], $recipients)));
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
            Mail::send('emails.system_usage_report', [
                'report' => $report,
            ], function ($message) use ($primaryRecipient, $recipients, $subject) {
                $message->to($primaryRecipient)->subject($subject);

                if (!empty($recipients)) {
                    $message->bcc($recipients);
                }
            });
        } catch (\Throwable $exception) {
            $this->finishDeliveryLog($delivery, 'failed', $exception->getMessage());
            throw $exception;
        }

        $this->finishDeliveryLog($delivery, 'sent');

        return [
            'report' => $report,
            'subject' => $subject,
            'primary_recipient' => $primaryRecipient,
            'bcc_recipients' => $recipients,
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

        $users = $users->map(function (array $user) use ($metrics) {
            $usage = [];
            foreach (array_keys($this->metricLabels) as $metricKey) {
                $usage[$metricKey] = (int) ($metrics[$metricKey][$user['id']] ?? 0);
            }

            $user['usage'] = $usage;
            $user['total_actions'] = array_sum($usage);
            $user['active'] = $user['total_actions'] > 0;

            return $user;
        })->sortByDesc('total_actions')->values();

        return [
            'generated_at' => now(),
            'period' => [
                'start' => $start,
                'end' => $end,
                'label' => sprintf('%s to %s', $start->format('d M Y'), $end->format('d M Y')),
            ],
            'metric_labels' => $this->metricLabels,
            'summary' => $this->summaryForUsers($users),
            'groups' => $this->groupBreakdown($users),
            'regions' => $this->regionBreakdown($users),
            'top_users' => $users->take(10)->values()->all(),
            'users' => $users->values()->all(),
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
                $group = $this->matchGroup($user);
                $user['group_key'] = $group['key'] ?? null;
                $user['group_label'] = $group['label'] ?? 'Other';

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
            'remarks_added' => $this->pluckGroupedCounts('remarks', 'user_id', 'created_at', $userIds, $start, $end),
            'status_updates' => $this->pluckGroupedCounts('fault_stage_logs', 'started_by', 'started_at', $userIds, $start, $end),
            'assignments_received' => $this->pluckGroupedCounts('fault_assignments', 'user_id', 'assigned_at', $userIds, $start, $end),
            'referrals_made' => $this->pluckGroupedCounts('fault_referrals', 'referred_by', 'started_at', $userIds, $start, $end),
            'surveys_submitted' => $this->pluckSurveyCounts($userIds, $start, $end),
        ];
    }

    protected function pluckGroupedCounts(
        string $table,
        string $userColumn,
        string $dateColumn,
        array $userIds,
        Carbon $start,
        Carbon $end
    ): array {
        return DB::table($table)
            ->whereIn($userColumn, $userIds)
            ->whereBetween($dateColumn, [$start, $end])
            ->select($userColumn, DB::raw('COUNT(*) as aggregate'))
            ->groupBy($userColumn)
            ->pluck('aggregate', $userColumn)
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

            foreach ($candidates as $candidate) {
                if (in_array($candidate, $group['roles'], true)) {
                    return $group;
                }
            }
        }

        return null;
    }

    protected function monitoredGroups(): array
    {
        return [
            [
                'key' => 'network-operations-technician',
                'label' => 'Network Operations / Technician',
                'sections' => ['network operations'],
                'roles' => ['technician', 'techniciain'],
            ],
            [
                'key' => 'customer-experience-call-centre-chief-tech',
                'label' => 'Customer Experience / Call Centre and Chief Technician',
                'sections' => ['customer experience'],
                'roles' => ['call centre', 'chief technician'],
            ],
            [
                'key' => 'service-management-centre-noc',
                'label' => 'Service Management Centre / Noc Supervisor and Noc',
                'sections' => ['service management centre'],
                'roles' => ['noc supervisor', 'noc', 'network controller'],
            ],
        ];
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
        $region = trim((string) $region);

        return $region !== '' ? $region : 'Unassigned';
    }
}
