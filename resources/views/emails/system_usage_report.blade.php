<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impaza Weekly System Usage Report</title>
    <style>
        body {
            margin: 0;
            padding: 24px 0;
            background: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #172033;
        }
        .container {
            width: 100%;
            max-width: 920px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #dbe3ee;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }
        .hero {
            background: linear-gradient(135deg, #0f4aa1 0%, #1d72f3 100%);
            color: #ffffff;
            padding: 36px 40px 28px;
            border-bottom: 1px solid #d7e5ff;
        }
        .hero h1 {
            margin: 0 0 10px;
            font-size: 28px;
            line-height: 1.2;
        }
        .hero p {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }
        .content {
            padding: 32px 40px 40px;
            background: #ffffff;
        }
        .meta {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.28);
            font-size: 13px;
            letter-spacing: 0.2px;
        }
        .section-title {
            margin: 0 0 16px;
            font-size: 18px;
            color: #0f172a;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eef7;
        }
        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px;
            margin: 0 0 28px;
        }
        .summary-card {
            width: 25%;
            background: #ffffff;
            border: 1px solid #d7e5ff;
            border-radius: 16px;
            padding: 18px;
            vertical-align: top;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }
        .summary-card .label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #5a6b85;
            margin-bottom: 8px;
        }
        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #0f4aa1;
        }
        .metric-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 28px;
        }
        .metric-grid td {
            width: 33.33%;
            padding: 0 10px 12px 0;
            vertical-align: top;
        }
        .metric-pill {
            display: block;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.04);
        }
        .metric-pill strong {
            display: block;
            font-size: 22px;
            color: #111827;
            margin-bottom: 4px;
        }
        .metric-pill span {
            font-size: 13px;
            color: #64748b;
        }
        .panel {
            margin: 0 0 28px;
            border: 1px solid #dbe5f0;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }
        .panel-header {
            background: #f8fbff;
            border-bottom: 1px solid #dbe5f0;
            padding: 18px 22px;
        }
        .panel-header h3 {
            margin: 0;
            font-size: 17px;
            color: #0f172a;
        }
        .panel-header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #64748b;
        }
        .panel-body {
            padding: 22px;
            background: #ffffff;
        }
        .region-card {
            margin-bottom: 16px;
            padding: 18px;
            border-radius: 14px;
            background: #fbfdff;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        .region-card:last-child {
            margin-bottom: 0;
        }
        .region-title {
            margin: 0 0 10px;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .micro {
            margin: 0 0 12px;
            font-size: 12px;
            color: #64748b;
        }
        .table-wrap {
            overflow-x: auto;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e6edf5;
            border-radius: 12px;
            overflow: hidden;
        }
        .data-table th,
        .data-table td {
            padding: 11px 12px;
            border-bottom: 1px solid #ebf1f7;
            text-align: left;
            font-size: 13px;
        }
        .data-table th {
            background: #f5f9ff;
            color: #475569;
            font-weight: 600;
        }
        .data-table tr:nth-child(even) td {
            background: #fbfdff;
        }
        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e8f1ff;
            color: #0f4aa1;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            padding: 22px 40px 30px;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
            border-top: 1px solid #e2e8f0;
        }
        @media only screen and (max-width: 680px) {
            .hero,
            .content,
            .footer {
                padding-left: 20px;
                padding-right: 20px;
            }
            .summary-grid,
            .metric-grid,
            .summary-grid tbody,
            .metric-grid tbody,
            .summary-grid tr,
            .metric-grid tr,
            .summary-grid td,
            .metric-grid td {
                display: block;
                width: 100%;
            }
            .summary-grid,
            .metric-grid {
                border-spacing: 0;
            }
            .summary-card,
            .metric-grid td {
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Weekly System Usage Report</h1>
            <p>Usage activity for the requested sections and roles, grouped by region and summarised for the selected reporting week.</p>
            <div class="meta">
                Reporting Period: {{ $report['period']['label'] }} |
                Generated: {{ $report['generated_at']->format('d M Y H:i') }}
            </div>
        </div>

        <div class="content">
            <h2 class="section-title">Executive Summary</h2>
            <table class="summary-grid" role="presentation">
                <tr>
                    <td class="summary-card">
                        <span class="label">Monitored Users</span>
                        <span class="value">{{ number_format($report['summary']['monitored_users']) }}</span>
                    </td>
                    <td class="summary-card">
                        <span class="label">Active Users</span>
                        <span class="value">{{ number_format($report['summary']['active_users']) }}</span>
                    </td>
                    <td class="summary-card">
                        <span class="label">Regions</span>
                        <span class="value">{{ number_format($report['summary']['regions']) }}</span>
                    </td>
                    <td class="summary-card">
                        <span class="label">Total Actions</span>
                        <span class="value">{{ number_format($report['summary']['total_actions']) }}</span>
                    </td>
                </tr>
            </table>

            <table class="metric-grid" role="presentation">
                <tr>
                    @foreach($report['metric_labels'] as $metricKey => $metricLabel)
                        <td>
                            <div class="metric-pill">
                                <strong>{{ number_format($report['summary']['metrics'][$metricKey] ?? 0) }}</strong>
                                <span>{{ $metricLabel }}</span>
                            </div>
                        </td>
                        @if(($loop->iteration % 3) === 0 && !$loop->last)
                            </tr><tr>
                        @endif
                    @endforeach
                </tr>
            </table>

            <div class="panel">
                <div class="panel-header">
                    <h3>Usage By Section and Role</h3>
                    <p>Monitored groups requested for this report.</p>
                </div>
                <div class="panel-body">
                    @forelse($report['groups'] as $group)
                        <div class="region-card">
                            <p class="region-title">{{ $group['label'] }}</p>
                            <p class="micro">
                                Users: {{ number_format($group['monitored_users']) }} |
                                Active: {{ number_format($group['active_users']) }} |
                                Total Actions: {{ number_format($group['total_actions']) }}
                            </p>

                            <table class="metric-grid" role="presentation" style="margin-bottom: 12px;">
                                <tr>
                                    @foreach($report['metric_labels'] as $metricKey => $metricLabel)
                                        <td>
                                            <div class="metric-pill">
                                                <strong>{{ number_format($group['metrics'][$metricKey] ?? 0) }}</strong>
                                                <span>{{ $metricLabel }}</span>
                                            </div>
                                        </td>
                                        @if(($loop->iteration % 3) === 0 && !$loop->last)
                                            </tr><tr>
                                        @endif
                                    @endforeach
                                </tr>
                            </table>

                            @if(!empty($group['regions']))
                                <div class="table-wrap">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Region</th>
                                                <th>Users</th>
                                                <th>Active</th>
                                                <th>Total Actions</th>
                                                <th>Top User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['regions'] as $region)
                                                <tr>
                                                    <td>{{ $region['region'] }}</td>
                                                    <td>{{ number_format($region['monitored_users']) }}</td>
                                                    <td>{{ number_format($region['active_users']) }}</td>
                                                    <td>{{ number_format($region['total_actions']) }}</td>
                                                    <td>{{ data_get($region, 'top_users.0.name', 'No activity') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="margin:0; color:#64748b;">No users matched the configured sections and roles for this report.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h3>Regional Breakdown</h3>
                    <p>Aggregated usage across all monitored groups, categorised by region.</p>
                </div>
                <div class="panel-body">
                    @forelse($report['regions'] as $region)
                        <div class="region-card">
                            <p class="region-title">{{ $region['region'] }}</p>
                            <p class="micro">
                                Users: {{ number_format($region['monitored_users']) }} |
                                Active: {{ number_format($region['active_users']) }} |
                                Total Actions: {{ number_format($region['total_actions']) }}
                            </p>

                            <table class="metric-grid" role="presentation" style="margin-bottom: 12px;">
                                <tr>
                                    @foreach($report['metric_labels'] as $metricKey => $metricLabel)
                                        <td>
                                            <div class="metric-pill">
                                                <strong>{{ number_format($region['metrics'][$metricKey] ?? 0) }}</strong>
                                                <span>{{ $metricLabel }}</span>
                                            </div>
                                        </td>
                                        @if(($loop->iteration % 3) === 0 && !$loop->last)
                                            </tr><tr>
                                        @endif
                                    @endforeach
                                </tr>
                            </table>

                            @if(!empty($region['top_users']))
                                <div class="table-wrap">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Section / Role</th>
                                                <th>Email</th>
                                                <th>Total Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($region['top_users'] as $user)
                                                <tr>
                                                    <td>{{ $user['name'] }}</td>
                                                    <td>{{ $user['group_label'] }}</td>
                                                    <td>{{ $user['email'] }}</td>
                                                    <td><span class="badge">{{ number_format($user['total_actions']) }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="margin:0; color:#64748b;">No regional activity was captured in this reporting period.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h3>Top Active Users</h3>
                    <p>Highest total recorded actions during the reporting week.</p>
                </div>
                <div class="panel-body">
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Region</th>
                                    <th>Section / Role</th>
                                    <th>Total Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['top_users'] as $user)
                                    <tr>
                                        <td>{{ $user['name'] }}</td>
                                        <td>{{ $user['region'] }}</td>
                                        <td>{{ $user['group_label'] }}</td>
                                        <td><span class="badge">{{ number_format($user['total_actions']) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No activity recorded for the selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            This is an automated email from Impaza.
            The usage totals are derived from recorded system actions currently available in the application, including fault logging, remarks, status updates, assignments, referrals, and survey submissions.
        </div>
    </div>
</body>
</html>
