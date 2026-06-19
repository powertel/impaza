@extends('layouts.admin')

@section('title')
System Usage Report Settings
@endsection

@section('content')
<link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
@php
    $latestStatus = $latestDelivery->status ?? null;
    $statusClass = $latestStatus === 'sent' ? 'success' : ($latestStatus === 'failed' ? 'danger' : 'secondary');
    $deliveryCount = $deliveryCount ?? collect($deliveries ?? [])->count();
    $successCount = $successCount ?? 0;
    $failedCount = $failedCount ?? 0;
    $recipientCount = collect(preg_split('/[\s,;]+/', (string) ($settings->recipients ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [])->filter()->count();
    $triggerMeta = [
        'scheduled' => ['label' => 'Scheduled Run', 'icon' => 'fas fa-calendar-check', 'class' => 'usage-trigger--scheduled'],
        'manual_test' => ['label' => 'Manual Test', 'icon' => 'fas fa-flask', 'class' => 'usage-trigger--manual'],
        'cli_override' => ['label' => 'CLI Override', 'icon' => 'fas fa-terminal', 'class' => 'usage-trigger--cli'],
    ];
@endphp
<style>
  .usage-mail-page .usage-hero {
    background: linear-gradient(135deg, #0f4aa1 0%, #2563eb 55%, #60a5fa 100%);
    border-radius: 18px;
    padding: 28px 30px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 24px 56px rgba(15, 23, 42, 0.16);
  }

  .usage-mail-page .usage-hero::after {
    content: "";
    position: absolute;
    inset: auto -60px -60px auto;
    width: 220px;
    height: 220px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
  }

  .usage-mail-page .usage-hero p,
  .usage-mail-page .usage-panel-note,
  .usage-mail-page .usage-meta {
    color: rgba(255, 255, 255, 0.88);
  }

  .usage-mail-page .usage-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
  }

  .usage-mail-page .usage-stat-card {
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid var(--cc-card-border);
    border-radius: 16px;
    padding: 18px;
    box-shadow: var(--cc-shadow-sm);
    height: 100%;
  }

  .usage-mail-page .usage-stat-label {
    color: var(--cc-muted);
    font-size: 0.82rem;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .usage-mail-page .usage-stat-value {
    color: var(--cc-text);
    font-size: 1.65rem;
    font-weight: 700;
    line-height: 1.15;
  }

  .usage-mail-page .usage-stat-sub {
    color: var(--cc-muted);
    font-size: 0.86rem;
    margin-top: 6px;
  }

  .usage-mail-page .usage-kpi-grid .cc-kpi-value {
    font-size: 1.55rem;
  }

  .usage-mail-page .usage-form-card .form-label {
    font-size: 0.86rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
  }

  .usage-mail-page .usage-form-card .form-control,
  .usage-mail-page .usage-form-card .form-select,
  .usage-mail-page .usage-form-card textarea {
    border-radius: 10px;
    border-color: #dce3ee;
    padding: 10px 12px;
    box-shadow: none;
  }

  .usage-mail-page .usage-form-card .form-control:focus,
  .usage-mail-page .usage-form-card .form-select:focus,
  .usage-mail-page .usage-form-card textarea:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
  }

  .usage-mail-page .usage-switch-wrap {
    background: #f8fbff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    min-height: 100%;
  }

  .usage-mail-page .usage-detail-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
  }

  .usage-mail-page .usage-detail-item {
    padding: 14px 16px;
    border-radius: 14px;
    background: #f8fbff;
    border: 1px solid #e2e8f0;
  }

  .usage-mail-page .usage-detail-item strong {
    display: block;
    color: var(--cc-text);
    font-size: 0.98rem;
    margin-top: 4px;
  }

  .usage-mail-page .usage-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .usage-mail-page .usage-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #edf2f7;
    color: #334155;
  }

  .usage-mail-page .usage-list li:last-child {
    border-bottom: 0;
    padding-bottom: 0;
  }

  .usage-mail-page .usage-list li::before {
    content: "";
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    margin-top: 6px;
    flex: 0 0 10px;
  }

  .usage-mail-page .usage-history-table thead th {
    white-space: nowrap;
    color: #475569;
    font-weight: 700;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    background: #f8fbff;
    border-bottom: 1px solid #e6ebf2;
  }

  .usage-mail-page .usage-history-table tbody td {
    vertical-align: top;
    color: #334155;
  }

  .usage-mail-page .usage-history-table tbody tr:hover {
    background: rgba(59, 130, 246, 0.04);
  }

  .usage-mail-page .usage-error-chip {
    display: inline-block;
    max-width: 320px;
    padding: 8px 10px;
    border-radius: 10px;
    background: #fff1f2;
    color: #be123c;
    font-size: 0.8rem;
    line-height: 1.45;
  }

  .usage-mail-page .dataTables_wrapper .dataTables_filter input,
  .usage-mail-page .dataTables_wrapper .dataTables_length select {
    border-radius: 10px;
    border: 1px solid #dbe5f0;
    padding: 0.4rem 0.75rem;
    box-shadow: none;
  }

  .usage-mail-page .dataTables_wrapper .dataTables_info,
  .usage-mail-page .dataTables_wrapper .dataTables_length,
  .usage-mail-page .dataTables_wrapper .dataTables_filter {
    color: #64748b;
    font-size: 0.86rem;
  }

  .usage-mail-page .dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0 !important;
    margin-left: 6px;
    border: 0 !important;
    background: transparent !important;
  }

  .usage-mail-page .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
    border-radius: 10px;
    border-color: #dbe5f0;
    color: #1d4ed8;
    min-width: 40px;
    text-align: center;
    box-shadow: none;
  }

  .usage-mail-page .dataTables_wrapper .dataTables_paginate .paginate_button.current .page-link,
  .usage-mail-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover .page-link {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-color: #1d4ed8;
    color: #fff !important;
  }

  .usage-mail-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled .page-link {
    color: #94a3b8 !important;
    background: #f8fafc;
  }

  .usage-mail-page .usage-trigger-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    border: 1px solid transparent;
    white-space: nowrap;
  }

  .usage-mail-page .usage-trigger--scheduled {
    color: #1d4ed8;
    background: #dbeafe;
    border-color: #bfdbfe;
  }

  .usage-mail-page .usage-trigger--manual {
    color: #7c2d12;
    background: #ffedd5;
    border-color: #fed7aa;
  }

  .usage-mail-page .usage-trigger--cli {
    color: #6d28d9;
    background: #ede9fe;
    border-color: #ddd6fe;
  }

  .usage-mail-page .usage-trigger--default {
    color: #334155;
    background: #e2e8f0;
    border-color: #cbd5e1;
  }

  @media (max-width: 991.98px) {
    .usage-mail-page .usage-detail-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 767.98px) {
    .usage-mail-page .usage-hero {
      padding: 22px 20px;
    }

    .usage-mail-page .usage-detail-grid {
      grid-template-columns: 1fr;
    }

    .usage-mail-page .usage-error-chip {
      max-width: 100%;
    }

    .usage-mail-page .usage-hero .d-flex.gap-2,
    .usage-mail-page .col-12.d-flex.justify-content-end.gap-2 {
      flex-direction: column;
      align-items: stretch !important;
    }

    .usage-mail-page .col-12.d-flex.justify-content-end.gap-2 .btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<section class="content ux-unified usage-mail-page">
  <div class="container-fluid">
    @if(session('success'))
      <div class="alert alert-success border-0 shadow-sm rounded-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger border-0 shadow-sm rounded-3">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
        <ul class="mb-0 ps-3">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="usage-hero mb-4">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4 position-relative" style="z-index:1;">
        <div>
          <h3 class="mb-2 fw-bold">
            <i class="fas fa-envelope-open-text me-2"></i>
            System Usage Report Settings
          </h3>
          <p class="mb-3 usage-panel-note">Manage the weekly report schedule, recipients, delivery monitoring, and test sends from one polished control centre.</p>
          <div class="d-flex flex-wrap gap-2">
            <span class="usage-pill">
              <i class="fas fa-clock"></i>
              Weekly Time: {{ \Carbon\Carbon::parse($settings->send_time ?? '07:00')->format('h:i A') }}
            </span>
            <span class="usage-pill">
              <i class="fas fa-user-friends"></i>
              Recipients: {{ number_format($recipientCount) }}
            </span>
            <span class="usage-pill">
              <i class="fas fa-shield-alt"></i>
              Status: {{ ($settings->enabled ?? false) ? 'Enabled' : 'Disabled' }}
            </span>
          </div>
        </div>
        <div class="text-lg-end">
          <div class="usage-meta small mb-2">Latest delivery snapshot</div>
          <div class="fs-5 fw-bold">{{ $latestDelivery ? ucfirst($latestDelivery->status ?? 'unknown') : 'No delivery yet' }}</div>
          <div class="usage-meta small mt-1">
            {{ $latestDelivery && $latestDelivery->started_at ? \Carbon\Carbon::parse($latestDelivery->started_at)->format('d M Y H:i') : 'Waiting for first run' }}
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 usage-kpi-grid mb-4">
      <div class="col-md-3">
        <div class="cc-kpi cc-kpi--slate h-100">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-stream"></i></div>
            <div class="cc-kpi-title">Deliveries Logged</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($deliveryCount) }}</div>
          <div class="cc-kpi-sub">Recent report runs recorded</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="cc-kpi cc-kpi--green h-100">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-check-circle"></i></div>
            <div class="cc-kpi-title">Successful Sends</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($successCount) }}</div>
          <div class="cc-kpi-sub">Completed without SMTP errors</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="cc-kpi cc-kpi--rose h-100">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="cc-kpi-title">Failed Sends</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($failedCount) }}</div>
          <div class="cc-kpi-sub">Need attention and retry</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="cc-kpi cc-kpi--indigo h-100">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-user-clock"></i></div>
            <div class="cc-kpi-title">Test Recipient</div>
          </div>
          <div class="cc-kpi-value" style="font-size:1rem; line-height:1.4;">{{ $settings->test_recipient ?? 'Not set' }}</div>
          <div class="cc-kpi-sub">Default manual test destination</div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-8">
        <div class="cc-chart-card mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">Delivery Status</div>
            @if($latestDelivery)
              <span class="badge rounded-pill bg-{{ $statusClass }}">{{ ucfirst($latestDelivery->status ?? 'unknown') }}</span>
            @endif
          </div>

          @if($latestDelivery)
            @php
              $latestTrigger = $triggerMeta[$latestDelivery->trigger_type ?? ''] ?? [
                  'label' => ucwords(str_replace('_', ' ', $latestDelivery->trigger_type ?? 'unknown')),
                  'icon' => 'fas fa-bell',
                  'class' => 'usage-trigger--default',
              ];
            @endphp
            <div class="usage-detail-grid">
              <div class="usage-detail-item">
                <small class="text-muted d-block">Last Trigger</small>
                <strong>
                  <span class="usage-trigger-badge {{ $latestTrigger['class'] }}">
                    <i class="{{ $latestTrigger['icon'] }}"></i>
                    {{ $latestTrigger['label'] }}
                  </span>
                </strong>
              </div>
              <div class="usage-detail-item">
                <small class="text-muted d-block">Started</small>
                <strong>{{ $latestDelivery->started_at ? \Carbon\Carbon::parse($latestDelivery->started_at)->format('d M Y H:i') : 'N/A' }}</strong>
              </div>
              <div class="usage-detail-item">
                <small class="text-muted d-block">Primary Recipient</small>
                <strong>{{ $latestDelivery->primary_recipient ?: 'Not available' }}</strong>
              </div>
              <div class="usage-detail-item">
                <small class="text-muted d-block">Reporting Period</small>
                <strong>
                  {{ $latestDelivery->period_start ? \Carbon\Carbon::parse($latestDelivery->period_start)->format('d M Y') : 'N/A' }}
                  -
                  {{ $latestDelivery->period_end ? \Carbon\Carbon::parse($latestDelivery->period_end)->format('d M Y') : 'N/A' }}
                </strong>
              </div>
              <div class="usage-detail-item">
                <small class="text-muted d-block">Initiated By</small>
                <strong>{{ $latestDelivery->initiated_by_name ?: 'Background scheduler' }}</strong>
              </div>
              <div class="usage-detail-item">
                <small class="text-muted d-block">Finished</small>
                <strong>{{ $latestDelivery->finished_at ? \Carbon\Carbon::parse($latestDelivery->finished_at)->format('d M Y H:i') : 'Pending' }}</strong>
              </div>
            </div>

            @if(!empty($latestDelivery->error_message))
              <div class="alert alert-danger border-0 rounded-3 mt-3 mb-0">
                <strong class="d-block mb-1">Latest Error</strong>
                {{ $latestDelivery->error_message }}
              </div>
            @endif
          @else
            <div class="text-muted">No delivery history yet. Send a test email or wait for the scheduled run.</div>
          @endif
        </div>

        <div class="cc-chart-card usage-form-card mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">Weekly Email Settings</div>
            <span class="badge rounded-pill {{ ($settings->enabled ?? false) ? 'bg-success' : 'bg-secondary' }}">
              {{ ($settings->enabled ?? false) ? 'Enabled' : 'Disabled' }}
            </span>
          </div>

          <form action="{{ route('system-usage-settings.update') }}" method="POST">
            @csrf
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">Scheduled Send Time</label>
                <input
                  type="time"
                  name="send_time"
                  class="form-control"
                  value="{{ old('send_time', \Carbon\Carbon::parse($settings->send_time ?? '07:00')->format('H:i')) }}"
                  required
                >
                <small class="text-muted d-block mt-2">Runs weekly every Monday using the saved time.</small>
              </div>
              <div class="col-md-6">
                <div class="usage-switch-wrap d-flex align-items-center justify-content-between">
                  <div>
                    <div class="fw-semibold text-dark">Background Delivery</div>
                    <small class="text-muted">Enable or pause the scheduled weekly report.</small>
                  </div>
                  <div class="form-check form-switch m-0">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="enabled"
                      name="enabled"
                      value="1"
                      {{ old('enabled', ($settings->enabled ?? false)) ? 'checked' : '' }}
                    >
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Fixed Recipient Emails</label>
                <textarea
                  name="recipients"
                  rows="6"
                  class="form-control"
                  placeholder="manager1@powertel.co.zw&#10;manager2@powertel.co.zw"
                >{{ old('recipients', $settings->recipients ?? '') }}</textarea>
                <small class="text-muted d-block mt-2">Use commas, spaces, or one email per line.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Default Test Recipient</label>
                <input
                  type="email"
                  name="test_recipient"
                  class="form-control"
                  value="{{ old('test_recipient', $settings->test_recipient ?? 'fjatakalula@powertel.co.zw') }}"
                  placeholder="fjatakalula@powertel.co.zw"
                >
              </div>
              <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                  <i class="fas fa-save me-1"></i>
                  Save Settings
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="cc-chart-card usage-form-card mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">Send Test Report</div>
            <span class="badge rounded-pill bg-primary-subtle text-primary">Manual Send</span>
          </div>

          <form action="{{ route('system-usage-settings.send-test') }}" method="POST">
            @csrf
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">Test Recipient</label>
                <input
                  type="email"
                  name="test_recipient"
                  class="form-control"
                  value="{{ old('test_recipient', $settings->test_recipient ?? 'fjatakalula@powertel.co.zw') }}"
                  required
                >
              </div>
              <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
              </div>
              <div class="col-12">
                <small class="text-muted">Leave the dates empty to send the previous full Monday to Sunday week.</small>
              </div>
              <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-outline-primary rounded-pill px-4">
                  <i class="fas fa-paper-plane me-1"></i>
                  Send Test Email
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="cc-chart-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">Delivery History</div>
            <span class="badge rounded-pill bg-dark">{{ number_format($deliveryCount) }} Logged</span>
          </div>

          <div class="table-responsive">
            <table id="usageDeliveryHistoryTable" class="table table-hover align-middle mb-0 usage-history-table">
              <thead>
                <tr>
                  <th>Started</th>
                  <th>Trigger</th>
                  <th>Status</th>
                  <th>Recipient</th>
                  <th>Initiated By</th>
                  <th>Error</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($deliveries ?? collect()) as $delivery)
                  @php
                    $deliveryTrigger = $triggerMeta[$delivery->trigger_type ?? ''] ?? [
                        'label' => ucwords(str_replace('_', ' ', $delivery->trigger_type ?? 'unknown')),
                        'icon' => 'fas fa-bell',
                        'class' => 'usage-trigger--default',
                    ];
                  @endphp
                  <tr>
                    <td data-order="{{ $delivery->started_at ? \Carbon\Carbon::parse($delivery->started_at)->format('Y-m-d H:i:s') : '' }}">{{ $delivery->started_at ? \Carbon\Carbon::parse($delivery->started_at)->format('d M Y H:i') : 'N/A' }}</td>
                    <td>
                      <span class="usage-trigger-badge {{ $deliveryTrigger['class'] }}">
                        <i class="{{ $deliveryTrigger['icon'] }}"></i>
                        {{ $deliveryTrigger['label'] }}
                      </span>
                    </td>
                    <td>
                      <span class="badge rounded-pill {{ ($delivery->status ?? '') === 'sent' ? 'bg-success' : (($delivery->status ?? '') === 'failed' ? 'bg-danger' : 'bg-secondary') }}">
                        {{ ucfirst($delivery->status ?? 'unknown') }}
                      </span>
                    </td>
                    <td>{{ $delivery->primary_recipient ?: 'N/A' }}</td>
                    <td>{{ $delivery->initiated_by_name ?: 'Background scheduler' }}</td>
                    <td>
                      @if($delivery->error_message)
                        <span class="usage-error-chip">{{ $delivery->error_message }}</span>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-5">No delivery history recorded yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-xl-4">
        <div class="usage-stat-card mb-4">
          <div class="usage-stat-label">Included Groups</div>
          <div class="usage-stat-sub mb-2">Monitored teams covered by the weekly report.</div>
          <ul class="usage-list">
            @foreach($monitoredGroups as $group)
              <li>{{ $group }}</li>
            @endforeach
          </ul>
        </div>

        <div class="usage-stat-card mb-4">
          <div class="usage-stat-label">Usage Metrics</div>
          <div class="usage-stat-sub mb-2">Activity signals currently included in the email summary.</div>
          <ul class="usage-list">
            @foreach($defaultMetrics as $metric)
              <li>{{ $metric }}</li>
            @endforeach
          </ul>
        </div>

        <div class="usage-stat-card mb-4">
          <div class="usage-stat-label">Background Behaviour</div>
          <div class="usage-stat-sub">
            The scheduler runs every Monday using the saved time. Recipients saved here are used by the background command. If no database record exists yet, the system falls back to `.env` values.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
@parent
<script>
  $(function () {
    var table = $('#usageDeliveryHistoryTable');
    if (!table.length || !$.fn.DataTable) {
      return;
    }

    table.DataTable({
      responsive: true,
      autoWidth: false,
      order: [[0, 'desc']],
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      language: {
        search: 'Quick Search:',
        lengthMenu: 'Show _MENU_ entries',
        emptyTable: 'No delivery history recorded yet.'
      },
      dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
           '<"row"<"col-sm-12"tr>>' +
           '<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
  });
</script>
@endsection
