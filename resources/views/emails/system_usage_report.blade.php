<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $report['period']['report_title'] }}</title>
    <style>
        body {
            margin: 0;
            padding: 24px 12px;
            background: #f3f6fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #142033;
        }
        .container {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d9e3ef;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.10);
        }
        .hero {
            background: #eef5ff;
            color: #142033;
            padding: 38px 42px 30px;
            border-bottom: 1px solid #d7e5ff;
            border-top: 6px solid #1f6feb;
        }
        .eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #1e40af;
        }
        .hero h1 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.18;
            color: #0f172a;
        }
        .hero p {
            margin: 0;
            font-size: 15px;
            line-height: 1.7;
            color: #425166;
        }
        .content {
            padding: 32px 42px 42px;
            background: #ffffff;
        }
        .hero-meta {
            width: 100%;
            margin-top: 18px;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .hero-meta td {
            width: 33.33%;
            padding: 14px 16px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #d7e5ff;
            vertical-align: top;
        }
        .hero-meta .meta-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #5b6b81;
            margin-bottom: 5px;
        }
        .hero-meta .meta-value {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.5;
        }
        .section {
            margin-bottom: 28px;
        }
        .section:last-child {
            margin-bottom: 0;
        }
        .section-title {
            margin: 0 0 14px;
            font-size: 19px;
            color: #0f172a;
        }
        .section-copy {
            margin: 0 0 16px;
            font-size: 14px;
            line-height: 1.7;
            color: #52637a;
        }
        .note-panel {
            margin-bottom: 18px;
            padding: 18px 20px;
            border-radius: 18px;
            background: #f8fbff;
            border: 1px solid #d9e7ff;
        }
        .note-list {
            margin: 0;
            padding-left: 18px;
        }
        .note-list li {
            margin-bottom: 8px;
            font-size: 13px;
            line-height: 1.6;
            color: #425166;
        }
        .note-list li:last-child {
            margin-bottom: 0;
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
            padding: 18px 18px 20px;
            vertical-align: top;
            box-shadow: 0 4px 14px rgba(31, 111, 235, 0.06);
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
        .observation-list {
            margin: 0;
            padding-left: 18px;
        }
        .observation-list li {
            margin-bottom: 10px;
            font-size: 14px;
            line-height: 1.7;
            color: #3d4b60;
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
            font-size: 18px;
            color: #0f172a;
        }
        .panel-header p {
            margin: 6px 0 0;
            font-size: 13px;
            line-height: 1.6;
            color: #64748b;
        }
        .panel-body {
            padding: 22px;
            background: #ffffff;
        }
        .profile-kpis {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin: 0 0 18px;
        }
        .profile-kpis td {
            width: 25%;
            padding: 14px 15px;
            border-radius: 14px;
            border: 1px solid #dde6f2;
            background: #fbfdff;
            vertical-align: top;
        }
        .profile-kpis .kpi-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #5b6b81;
            margin-bottom: 6px;
        }
        .profile-kpis .kpi-value {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: #163b7a;
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
        .subregion-card {
            margin-bottom: 18px;
            padding: 18px;
            border-radius: 16px;
            background: #fbfdff;
            border: 1px solid #dbe5f0;
        }
        .subregion-card:last-child {
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
            line-height: 1.6;
        }
        .micro strong {
            color: #334155;
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
        .data-table td strong {
            color: #0f172a;
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
            padding: 22px 42px 30px;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0 0 8px;
        }
        .footer p:last-child {
            margin-bottom: 0;
        }
        .muted-empty {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background: #0f172a !important;
                color: #e5edf7 !important;
            }
            .container,
            .panel,
            .panel-body,
            .summary-card,
            .metric-pill,
            .region-card,
            .profile-kpis td,
            .hero-meta td {
                background: #111827 !important;
                border-color: #334155 !important;
                color: #e5edf7 !important;
                box-shadow: none !important;
            }
            .hero {
                background: #162133 !important;
                border-top-color: #3b82f6 !important;
                color: #e5edf7 !important;
            }
            .eyebrow {
                background: #1e3a8a !important;
                border-color: #2563eb !important;
                color: #dbeafe !important;
            }
            .content {
                background: #0f172a !important;
            }
            .panel-header,
            .note-panel,
            .footer {
                background: #162133 !important;
                border-color: #334155 !important;
                color: #cbd5e1 !important;
            }
            .panel-header h3,
            .section-title,
            .region-title,
            .data-table td strong,
            .hero h1,
            .hero-meta .meta-value {
                color: #f8fafc !important;
            }
            .section-copy,
            .panel-header p,
            .micro,
            .note-list li,
            .observation-list li,
            .muted-empty,
            .footer,
            .footer p,
            .hero p,
            .hero-meta .meta-label {
                color: #cbd5e1 !important;
            }
            .summary-card .label,
            .metric-pill span,
            .profile-kpis .kpi-label {
                color: #94a3b8 !important;
            }
            .summary-card .value,
            .profile-kpis .kpi-value,
            .metric-pill strong,
            .badge {
                color: #bfdbfe !important;
            }
            .data-table th {
                background: #1e293b !important;
                color: #e2e8f0 !important;
                border-color: #334155 !important;
            }
            .data-table td {
                border-color: #243244 !important;
                color: #e2e8f0 !important;
            }
            .data-table tr:nth-child(even) td {
                background: #162133 !important;
            }
            .badge {
                background: #1d4ed8 !important;
            }
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
            .hero-meta,
            .profile-kpis,
            .summary-grid tbody,
            .metric-grid tbody,
            .hero-meta tbody,
            .profile-kpis tbody,
            .summary-grid tr,
            .metric-grid tr,
            .hero-meta tr,
            .profile-kpis tr,
            .summary-grid td,
            .metric-grid td,
            .hero-meta td,
            .profile-kpis td {
                display: block;
                width: 100%;
            }
            .summary-grid,
            .metric-grid,
            .hero-meta,
            .profile-kpis {
                border-spacing: 0;
            }
            .summary-card,
            .metric-grid td,
            .hero-meta td,
            .profile-kpis td {
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>
    @php
        $tableWrapStyle = 'border:1px solid #dbe5f0; border-radius:14px; overflow:hidden; background:#ffffff;';
        $tableStyle = 'width:100%; border-collapse:separate; border-spacing:0; background:#ffffff;';
        $thStyle = 'padding:12px 14px; background:#f5f9ff; color:#475569; font-size:13px; font-weight:700; text-align:left; border-bottom:1px solid #dbe5f0; border-right:1px solid #e5edf5;';
        $tdStyle = 'padding:12px 14px; color:#1f2937; font-size:13px; text-align:left; vertical-align:top; border-bottom:1px solid #ebf1f7; border-right:1px solid #eef3f8; background:#ffffff;';
        $tdAltStyle = 'padding:12px 14px; color:#1f2937; font-size:13px; text-align:left; vertical-align:top; border-bottom:1px solid #ebf1f7; border-right:1px solid #eef3f8; background:#fbfdff;';
        $lastCellStyle = 'border-right:none;';
        $lastRowStyle = 'border-bottom:none;';
    @endphp
    <div class="container">
        <div class="hero" style="background:#eef5ff; color:#142033; padding:38px 42px 30px; border-bottom:1px solid #d7e5ff; border-top:6px solid #1f6feb;">
            <span class="eyebrow" style="display:inline-block; margin-bottom:12px; padding:6px 12px; border-radius:999px; background:#dbeafe; border:1px solid #bfdbfe; font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#1e40af;">Management Report</span>
            <h1 style="margin:0 0 10px; font-size:30px; line-height:1.18; color:#0f172a;">{{ $report['period']['report_title'] }}</h1>
            <p style="margin:0; font-size:15px; line-height:1.7; color:#425166;"></p>
            <table class="hero-meta" role="presentation" style="width:100%; margin-top:18px; border-collapse:separate; border-spacing:10px;">
                <tr>
                    <td style="width:33.33%; padding:14px 16px; border-radius:16px; background:#ffffff; border:1px solid #d7e5ff; vertical-align:top;">
                        <span class="meta-label" style="display:block; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:5px;">Reporting Period</span>
                        <span class="meta-value" style="display:block; font-size:15px; font-weight:700; color:#0f172a; line-height:1.5;">{{ $report['period']['label'] }}</span>
                    </td>
                    <td style="width:33.33%; padding:14px 16px; border-radius:16px; background:#ffffff; border:1px solid #d7e5ff; vertical-align:top;">
                        <span class="meta-label" style="display:block; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:5px;">Generated</span>
                        <span class="meta-value" style="display:block; font-size:15px; font-weight:700; color:#0f172a; line-height:1.5;">{{ $report['generated_at']->format('d M Y H:i') }}</span>
                    </td>
                    <td style="width:33.33%; padding:14px 16px; border-radius:16px; background:#ffffff; border:1px solid #d7e5ff; vertical-align:top;">
                        <span class="meta-label" style="display:block; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:5px;">Coverage</span>
                        <span class="meta-value" style="display:block; font-size:15px; font-weight:700; color:#0f172a; line-height:1.5;">Network Operations, Customer Experience, and Service Management Centre</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <div class="section">
                <h2 class="section-title">Executive Summary</h2>
                <p class="section-copy">The summary below provides a consolidated view of monitored user participation and total platform activity across the defined operational teams.</p>

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

                @if(!empty($report['executive_observations']))
                    <div class="note-panel">
                        <ul class="observation-list">
                            @foreach($report['executive_observations'] as $observation)
                                <li>{{ $observation }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

           <!--  <div class="section">
                <h2 class="section-title">Scope and Methodology</h2>
                <p class="section-copy">This report follows a formal operational reporting approach by distinguishing user scope, regional coverage, lifecycle status transitions, and role-specific actions captured in the system.</p>
                <div class="note-panel">
                    <ul class="note-list">
                        @foreach($report['methodology'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
 -->
            <div class="section">
                <h2 class="section-title">Operational Performance by Role</h2>
                <p class="section-copy"></p>

                @forelse($report['operational_profiles'] as $profile)
                    <div class="panel">
                        <div class="panel-header">
                            <h3>{{ $profile['title'] }}</h3>
                            <p><strong>{{ $profile['subtitle'] }}.</strong> {{ $profile['description'] }}</p>
                        </div>
                        <div class="panel-body">
                            <table class="profile-kpis" role="presentation">
                                <tr>
                                    @foreach($profile['metric_labels'] as $metricKey => $metricLabel)
                                        <td>
                                            <span class="kpi-label">{{ $metricLabel }}</span>
                                            <span class="kpi-value">{{ number_format($profile['metrics'][$metricKey] ?? 0) }}</span>
                                        </td>
                                        @if(($loop->iteration % 4) === 0 && !$loop->last)
                                            </tr><tr>
                                        @endif
                                    @endforeach
                                </tr>
                            </table>

                            @if(!empty($profile['regional_profiles']))
                                @foreach($profile['regional_profiles'] as $regionalProfile)
                                    <div class="subregion-card">
                                        <p class="region-title">{{ $regionalProfile['region'] }} Region</p>
                                        <p class="micro">
                                            <strong>Monitored Users:</strong> {{ number_format($regionalProfile['monitored_users']) }} |
                                            <strong>Active Users:</strong> {{ number_format($regionalProfile['active_users']) }} |
                                            <strong>Total Actions:</strong> {{ number_format($regionalProfile['metrics']['total_actions'] ?? 0) }}
                                        </p>

                                        <table class="metric-grid" role="presentation" style="margin-bottom: 14px;">
                                            <tr>
                                                @foreach($profile['metric_labels'] as $metricKey => $metricLabel)
                                                    <td>
                                                        <div class="metric-pill">
                                                            <strong>{{ number_format($regionalProfile['metrics'][$metricKey] ?? 0) }}</strong>
                                                            <span>{{ $metricLabel }}</span>
                                                        </div>
                                                    </td>
                                                    @if(($loop->iteration % 3) === 0 && !$loop->last)
                                                        </tr><tr>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </table>

                                        @if(!empty($regionalProfile['top_users']))
                                            <div class="table-wrap" style="{{ $tableWrapStyle }}">
                                                <table class="data-table" style="{{ $tableStyle }}">
                                                    <thead>
                                                        <tr>
                                                            <th style="{{ $thStyle }}">Officer</th>
                                                            @foreach($regionalProfile['detail_columns'] as $column)
                                                                <th style="{{ $thStyle }}{{ $loop->last ? ' ' . $lastCellStyle : '' }}">{{ $column['label'] }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($regionalProfile['top_users'] as $user)
                                                            <tr>
                                                                <td style="{{ $loop->odd ? $tdStyle : $tdAltStyle }}">
                                                                    <strong>{{ $user['name'] }}</strong><br>
                                                                    {{ $user['email'] }}
                                                                </td>
                                                                @foreach($regionalProfile['detail_columns'] as $column)
                                                                    <td style="{{ ($loop->parent->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastCellStyle : '') . ($loop->parent->last ? ' ' . $lastRowStyle : '') }}">
                                                                        {{ number_format($user[$column['key']] ?? 0) }}
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="muted-empty">No activity was recorded for this region in the selected reporting period.</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="muted-empty">No activity was recorded for this operational profile in the selected reporting period.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="muted-empty">No role-based operational activity was available for the selected reporting period.</p>
                @endforelse
            </div>

            <div class="section">
                <h2 class="section-title">Section and Role Governance View</h2>
                <p class="section-copy">This section summarises monitored usage by operational section, then shows the role composition within each section together with the regional distribution of recorded activity.</p>

                @forelse($report['groups'] as $group)
                    <div class="panel">
                        <div class="panel-header">
                            <h3>{{ $group['label'] }}</h3>
                            <p>Monitored users: {{ number_format($group['monitored_users']) }} | Active users: {{ number_format($group['active_users']) }} | Total actions: {{ number_format($group['total_actions']) }}</p>
                        </div>
                        <div class="panel-body">
                            <table class="metric-grid" role="presentation" style="margin-bottom: 16px;">
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

                            @if(!empty($group['roles']))
                                <div class="table-wrap" style="margin-bottom: 16px; {{ $tableWrapStyle }}">
                                    <table class="data-table" style="{{ $tableStyle }}">
                                        <thead>
                                            <tr>
                                                <th style="{{ $thStyle }}">Role</th>
                                                <th style="{{ $thStyle }}">Monitored Users</th>
                                                <th style="{{ $thStyle }}">Active Users</th>
                                                <th style="{{ $thStyle }}">Total Actions</th>
                                                <th style="{{ $thStyle }}{{ $lastCellStyle }}">Leading User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['roles'] as $role)
                                                <tr>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $role['label'] }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ number_format($role['monitored_users']) }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ number_format($role['active_users']) }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ number_format($role['total_actions']) }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ' ' . $lastCellStyle . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ data_get($role, 'top_users.0.name', 'No activity') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if(!empty($group['regions']))
                                <div class="table-wrap" style="{{ $tableWrapStyle }}">
                                    <table class="data-table" style="{{ $tableStyle }}">
                                        <thead>
                                            <tr>
                                                <th style="{{ $thStyle }}">Region</th>
                                                <th style="{{ $thStyle }}">Monitored Users</th>
                                                <th style="{{ $thStyle }}">Active Users</th>
                                                <th style="{{ $thStyle }}">Total Actions</th>
                                                <th style="{{ $thStyle }}{{ $lastCellStyle }}">Leading User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['regions'] as $region)
                                                <tr>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $region['region'] }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ number_format($region['monitored_users']) }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ number_format($region['active_users']) }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ number_format($region['total_actions']) }}</td>
                                                    <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ' ' . $lastCellStyle . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ data_get($region, 'top_users.0.name', 'No activity') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="muted-empty">No monitored groups matched the configured roles during the selected reporting period.</p>
                @endforelse
            </div>

            <div class="section">
                <h2 class="section-title">Regional Breakdown</h2>
                <p class="section-copy">Regional categorisation is based on the monitored user profile and provides a management view of where recorded activity was concentrated during the reporting week.</p>

                @forelse($report['regions'] as $region)
                    <div class="region-card">
                        <p class="region-title">{{ $region['region'] }}</p>
                        <p class="micro">
                            <strong>Monitored Users:</strong> {{ number_format($region['monitored_users']) }} |
                            <strong>Active Users:</strong> {{ number_format($region['active_users']) }} |
                            <strong>Total Actions:</strong> {{ number_format($region['total_actions']) }}
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
                            <div class="table-wrap" style="{{ $tableWrapStyle }}">
                                <table class="data-table" style="{{ $tableStyle }}">
                                    <thead>
                                        <tr>
                                            <th style="{{ $thStyle }}">User</th>
                                            <th style="{{ $thStyle }}">Section</th>
                                            <th style="{{ $thStyle }}">Email</th>
                                            <th style="{{ $thStyle }}{{ $lastCellStyle }}">Total Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($region['top_users'] as $user)
                                            <tr>
                                                <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $user['name'] }}</td>
                                                <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $user['section_label'] }}</td>
                                                <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $user['email'] }}</td>
                                                <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ' ' . $lastCellStyle . ($loop->last ? ' ' . $lastRowStyle : '') }}"><span class="badge">{{ number_format($user['total_actions']) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="muted-empty">No regional activity was captured in this reporting period.</p>
                @endforelse
            </div>

            <div class="section">
                <h2 class="section-title">Top Active Users</h2>
                <p class="section-copy">The following ranked view shows the monitored users with the highest total recorded action volume during the reporting period.</p>
                <div class="panel">
                    <div class="panel-body">
                        <div class="table-wrap" style="{{ $tableWrapStyle }}">
                            <table class="data-table" style="{{ $tableStyle }}">
                                <thead>
                                    <tr>
                                        <th style="{{ $thStyle }}">User</th>
                                        <th style="{{ $thStyle }}">Region</th>
                                        <th style="{{ $thStyle }}{{ $lastCellStyle }}">Total Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report['top_users'] as $user)
                                        <tr>
                                            <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $user['name'] }}</td>
                                            <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ($loop->last ? ' ' . $lastRowStyle : '') }}">{{ $user['region'] }}</td>
                                            <td style="{{ ($loop->odd ? $tdStyle : $tdAltStyle) . ' ' . $lastCellStyle . ($loop->last ? ' ' . $lastRowStyle : '') }}"><span class="badge">{{ number_format($user['total_actions']) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" style="{{ $tdStyle }} {{ $lastCellStyle }} {{ $lastRowStyle }}">No activity recorded for the selected period.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated management email generated by Impaza.</p>
            <p>The report is intended to support formal operational oversight and should be interpreted together with team workflow context, including assessments, dispatch activity, rectification stages, chief technician actions, and NOC restoration events.</p>
            <p>Recorded usage totals are derived from system data currently available in the application, including fault logging, remarks, status updates, assignments, referrals, and survey submissions.</p>
        </div>
    </div>
</body>
</html>
