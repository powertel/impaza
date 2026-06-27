@extends('layouts.admin')

@section('title')
Technician Configuration
@endsection

@include('partials.css')
@section('styles')
<style>
  .technician-config-page .tech-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(280px, 1fr) auto auto;
  }

  .technician-config-page .toolbar-search-wrap,
  .technician-config-page .toolbar-search-wrap .input-group {
    width: 100%;
    min-width: 0;
  }

  .technician-config-page .config-table .form-select,
  .technician-config-page .config-table .form-control,
  .technician-config-page .config-table .form-check-input {
    font-size: .76rem;
  }

  .technician-config-page .config-table .form-control-plaintext {
    color: var(--impaza-text);
    font-size: .78rem;
    padding-inline: 0;
  }

  @media (max-width: 991.98px) {
    .technician-config-page .tech-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .technician-config-page .toolbar-search-wrap {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .technician-config-page .tech-toolbar {
      grid-template-columns: 1fr;
    }

    .technician-config-page .toolbar-search-wrap {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $technicianCount = $technicians->count();
  $standbyCount = $technicians->where('weekly_standby', true)->count();
  $onLeaveCount = $technicians->where('status_name', 'On Leave')->count();
@endphp
<section class="content workflow-faults-page technician-config-page">
  <div class="workspace-summary-grid">
    <div class="workspace-summary-card" style="--summary-color:#6366F1;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-cog"></i></span>
          <div>
            <div class="workspace-summary-label">Technicians</div>
            <div class="workspace-summary-title">Configurable team members</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $technicianCount }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-clock"></i></span>
          <div>
            <div class="workspace-summary-label">Weekly Standby</div>
            <div class="workspace-summary-title">Active standby coverage</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $standbyCount }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#F59E0B;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-plane-departure"></i></span>
          <div>
            <div class="workspace-summary-label">On Leave</div>
            <div class="workspace-summary-title">Unavailable technicians</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $onLeaveCount }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Technician Configuration</h3>
        <div class="page-lead">Review assignment readiness, adjust coverage settings, and manage zone availability from one modern technician workspace.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-users-cog"></i> {{ $technicianCount }} total records</span>
        <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#autoSettingsModal">
          <i class="fas fa-cog me-1"></i> Auto-Assign Settings
        </button>
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar tech-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="autoAssignSize" class="form-select" aria-label="Rows per page">
              <option value="10">Show 10</option>
              <option value="20" selected>Show 20</option>
              <option value="50">Show 50</option>
              <option value="100">Show 100</option>
              <option value="all">Show All</option>
            </select>
          </div>
        </div>

        <div class="toolbar-search-wrap">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="autoAssignSearch" class="form-control" placeholder="Search technicians, sections, regions, or statuses">
          </div>
        </div>

        <button type="button" id="autoAssignSearchButton" class="btn btn-primary btn-sm px-3">
          <i class="fas fa-search me-1"></i> Search
        </button>

        <button type="button" id="autoAssignResetButton" class="btn btn-outline-secondary btn-sm px-3">
          <i class="fas fa-rotate-left me-1"></i> Reset
        </button>
      </div>
    </div>

    <div class="card-body">
      <form action="{{ route('technicians.regions.update') }}" method="POST">
        @csrf
        <div class="table-responsive">
          <table class="table table-hover align-middle js-paginated-table config-table" data-page-size="20" data-page-size-control="#autoAssignSize" data-pager="#autoAssignPager" data-search="#autoAssignSearch">
            <thead>
              <tr>
                <th>Technician</th>
                <th>Section</th>
                <th>Status</th>
                <th>Region</th>
                <th>Zone</th>
                <th>Weekly Standby</th>
                <th>Weekend Standby</th>
                <th>On Leave</th>
              </tr>
            </thead>
            <tbody>
              @foreach($technicians as $t)
                <tr data-user-id="{{ $t->id }}">
                  <td>
                    <div class="workspace-cell-main">{{ $t->name }}</div>
                    <div class="workspace-cell-sub">{{ $t->status_name ?: 'No status' }}</div>
                  </td>
                  <td>
                    <div class="workspace-cell-main">{{ $t->section ?: 'Not assigned' }}</div>
                    <div class="workspace-cell-sub">Team section</div>
                  </td>
                  <td>
                    <select class="form-select form-select-sm js-user-setting" data-field="user_status" data-role="status-select" {{ $t->status_name === 'On Leave' ? 'disabled' : '' }}>
                      <option value="Assignable" {{ $t->status_name === 'Assignable' ? 'selected' : '' }}>Assignable</option>
                      <option value="Away" {{ $t->status_name === 'Away' ? 'selected' : '' }}>Away</option>
                    </select>
                  </td>
                  <td>
                    <input type="hidden" name="user_id[]" value="{{ $t->id }}">
                    <input type="hidden" name="region[]" value="{{ $t->region }}">
                    <span class="form-control-plaintext form-control-sm">{{ $t->region ?: 'Not set' }}</span>
                  </td>
                  <td>
                    <select class="form-select form-select-sm js-user-setting" data-field="zones">
                        <option value="">Select Zone</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}" {{ $t->zones->contains($z->id) ? 'selected' : '' }}>{{ $z->name }}</option>
                        @endforeach
                    </select>
                  </td>
                  <td><input type="checkbox" class="form-check-input js-user-setting" data-field="weekly_standby" {{ $t->weekly_standby ? 'checked' : '' }}></td>
                  <td><input type="checkbox" class="form-check-input js-user-setting" data-field="weekend_standby" {{ $t->weekend_standby ? 'checked' : '' }}></td>
                  <td><input type="checkbox" class="form-check-input js-user-setting" data-field="on_leave" {{ $t->status_name === 'On Leave' ? 'checked' : '' }}></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div id="autoAssignPager" class="mt-3"></div>
      </form>
    </div>
  </div>

  <!-- Auto-Assign Settings Modal -->
  <div class="modal custom-modal fade" id="autoSettingsModal" tabindex="-1" aria-labelledby="autoSettingsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="autoSettingsLabel"><i class="fas fa-sliders-h me-2"></i>Auto-Assignment Settings</h5>
            <div class="modal-subtitle">Fine-tune standby windows, scope rules, and automation conditions for technician assignment.</div>
          </div>
          <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('technicians.settings.update') }}" method="POST">
            @csrf
            <div class="fault-modal-helper mb-3">
              <i class="fas fa-bolt"></i>
              <span>Changes can auto-save as you adjust individual fields, and the final save keeps the full configuration aligned.</span>
            </div>

            <div class="fault-modal-section">
              <div class="fault-modal-section-title">
                <i class="fas fa-clock"></i>
                <span>Standby Window</span>
              </div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Weekly Standby Start</label>
                  <input type="time" name="standby_start_time" class="form-control js-setting" value="{{ old('standby_start_time', \Carbon\Carbon::parse($settings->standby_start_time ?? '16:30:00')->format('H:i')) }}" data-field="standby_start_time">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Weekly Standby End</label>
                  <input type="time" name="standby_end_time" class="form-control js-setting" value="{{ old('standby_end_time', \Carbon\Carbon::parse($settings->standby_end_time ?? '06:00:00')->format('H:i')) }}" data-field="standby_end_time">
                </div>
              </div>
            </div>

            <div class="fault-modal-section">
              <div class="fault-modal-section-title">
                <i class="fas fa-crosshairs"></i>
                <span>Scope Rules</span>
              </div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Scope Section</label>
                  <select name="scope_section_id" class="form-select">
                    <option value="">Not set</option>
                    @foreach($sections as $s)
                      <option value="{{ $s->id }}" {{ (int)($settings->scope_section_id ?? 0) === (int)$s->id ? 'selected' : '' }}>{{ $s->section }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Scope Region</label>
                  <select name="scope_region" class="form-select">
                    <option value="">Not set</option>
                    @foreach($regions as $region)
                      <option value="{{ $region }}" {{ ($settings->scope_region ?? '') === $region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="fault-modal-section">
              <div class="fault-modal-section-title">
                <i class="fas fa-toggle-on"></i>
                <span>Automation Options</span>
              </div>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input js-setting" type="checkbox" name="weekend_standby_enabled" value="1" id="weekend_standby_enabled" data-field="weekend_standby_enabled" {{ old('weekend_standby_enabled', ($settings->weekend_standby_enabled ?? true)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="weekend_standby_enabled">Enable Weekend Standby</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input js-setting" type="checkbox" name="consider_leave" value="1" id="consider_leave" data-field="consider_leave" {{ old('consider_leave', ($settings->consider_leave ?? true)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="consider_leave">Exclude On Leave</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input js-setting" type="checkbox" name="consider_region" value="1" id="consider_region" data-field="consider_region" {{ old('consider_region', ($effectiveConsiderRegion ?? ($settings->consider_region ?? true))) ? 'checked' : '' }} {{ ($sectionLocked ?? false) ? '' : 'disabled' }}>
                    <label class="form-check-label" for="consider_region">Consider Region</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input js-setting" type="checkbox" name="consider_zones" value="1" id="consider_zones" data-field="consider_zones" {{ old('consider_zones', ($effectiveConsiderZones ?? ($settings->consider_zones ?? false))) ? 'checked' : '' }}>
                    <label class="form-check-label" for="consider_zones">Consider Zones</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input js-setting" type="checkbox" name="auto_assign_enabled" value="1" id="auto_assign_enabled" data-field="auto_assign_enabled" {{ old('auto_assign_enabled', ($effectiveAutoAssignEnabled ?? ($settings->auto_assign_enabled ?? false))) ? 'checked' : '' }} {{ (($sectionLocked ?? false) && !($sectionMatches ?? true)) || (($regionLocked ?? false) && !($regionMatches ?? true)) ? 'disabled' : '' }}>
                    <label class="form-check-label" for="auto_assign_enabled">Enable Auto-Assign</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
              <button type="submit" class="btn btn-primary btn-sm px-3">Save Settings</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function() {
    document.querySelectorAll('#autoSettingsModal').forEach(function (modal) {
      if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
      }
    });

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    async function postJSON(url, data) {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify(data)
      });
      if (!res.ok) {
        console.error('Save failed', await res.text());
      }
      return res.json().catch(() => ({}));
    }

    const rows = document.querySelectorAll('tr[data-user-id]');
    rows.forEach(row => {
      const onLeave = row.querySelector('input[data-field="on_leave"]');
      const statusSel = row.querySelector('select[data-role="status-select"]');
      if (onLeave && statusSel) {
        statusSel.disabled = !!onLeave.checked;
        onLeave.addEventListener('change', () => {
          statusSel.disabled = !!onLeave.checked;
        });
      }
    });

    document.querySelectorAll('.js-user-setting').forEach(el => {
      el.addEventListener('change', async () => {
        const tr = el.closest('tr');
        const userId = tr?.dataset.userId;
        const field = el.dataset.field;
        let value;
        if (el.type === 'checkbox') {
          value = el.checked ? 1 : 0;
        } else {
          value = el.value;
        }
        await postJSON(`{{ url('technicians/users') }}/${userId}/setting`, { field, value });
      });
    });

    // Auto-save global settings on change (modal)
    document.querySelectorAll('.js-setting').forEach(el => {
      el.addEventListener('change', async () => {
        if (el.disabled) { return; }
        const field = el.dataset.field;
        let value;
        if (el.type === 'checkbox') { value = el.checked ? 1 : 0; }
        else { value = el.value; }
        await postJSON('{{ route('technicians.settings.ajax') }}', { field, value });
      });
    });

    const search = document.getElementById('autoAssignSearch');
    const searchButton = document.getElementById('autoAssignSearchButton');
    const resetButton = document.getElementById('autoAssignResetButton');

    if (searchButton && search) {
      searchButton.addEventListener('click', function () {
        search.dispatchEvent(new Event('input', { bubbles: true }));
        search.focus();
      });
    }

    if (resetButton && search) {
      resetButton.addEventListener('click', function () {
        search.value = '';
        search.dispatchEvent(new Event('input', { bubbles: true }));
      });
    }
  })();
</script>
@endsection
