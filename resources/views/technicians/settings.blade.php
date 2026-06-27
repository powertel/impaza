@extends('layouts.admin')

@section('title')
Technician Settings
@endsection

@include('partials.css')

@section('styles')
<style>
  .technician-settings-page .settings-grid {
    display: grid;
    grid-template-columns: minmax(320px, .95fr) minmax(0, 1.35fr);
    gap: 16px;
  }

  .technician-settings-page .config-table .form-select,
  .technician-settings-page .config-table .form-control,
  .technician-settings-page .config-table .form-check-input {
    font-size: .76rem;
  }

  @media (max-width: 991.98px) {
    .technician-settings-page .settings-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endsection

@section('content')
@php
  $technicianCount = $technicians->count();
  $weekendEnabled = $technicians->where('weekend_standby', true)->count();
  $regionScoped = $technicians->whereNotNull('region')->where('region', '!=', '')->count();
@endphp
<section class="content workflow-faults-page technician-settings-page">
  <div class="workspace-summary-grid">
    <div class="workspace-summary-card" style="--summary-color:#6366F1;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-cog"></i></span>
          <div>
            <div class="workspace-summary-label">Technicians</div>
            <div class="workspace-summary-title">Available for configuration</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $technicianCount }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-moon"></i></span>
          <div>
            <div class="workspace-summary-label">Weekend Standby</div>
            <div class="workspace-summary-title">Enabled technicians</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $weekendEnabled }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#10B981;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-map-pin"></i></span>
          <div>
            <div class="workspace-summary-label">Region Mapped</div>
            <div class="workspace-summary-title">Scoped coverage</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $regionScoped }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Technician Settings</h3>
        <div class="page-lead">Review assignment windows and technician readiness from the same modern configuration workspace used across the updated modules.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-sliders-h"></i> {{ $technicianCount }} total records</span>
      </div>
    </div>

    <div class="card-body">
      <div class="settings-grid">
        <div class="workspace-panel">
          <div class="workspace-panel-header">
            <div>
              <h5 class="workspace-panel-title mb-0">Auto-Assignment Settings</h5>
              <div class="workspace-panel-lead">Control timing, scope, and exclusion rules for technician assignment.</div>
            </div>
          </div>
          <div class="workspace-panel-body">
            <form action="{{ route('technicians.settings.update') }}" method="POST">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Standby Start</label>
                  <input type="time" name="standby_start_time" class="form-control js-setting" value="{{ old('standby_start_time', \Carbon\Carbon::parse($settings->standby_start_time ?? '04:30:00')->format('H:i')) }}" data-field="standby_start_time">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Standby End</label>
                  <input type="time" name="standby_end_time" class="form-control js-setting" value="{{ old('standby_end_time', \Carbon\Carbon::parse($settings->standby_end_time ?? '08:00:00')->format('H:i')) }}" data-field="standby_end_time">
                </div>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-md-4">
                  <div class="form-check">
                    <input class="form-check-input js-setting" type="checkbox" name="weekend_standby_enabled" value="1" id="weekend_standby_enabled" data-field="weekend_standby_enabled" {{ old('weekend_standby_enabled', ($settings->weekend_standby_enabled ?? true)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="weekend_standby_enabled">Weekend Standby</label>
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
                    <input class="form-check-input js-setting" type="checkbox" name="consider_region" value="1" id="consider_region" data-field="consider_region" {{ old('consider_region', ($settings->consider_region ?? true)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="consider_region">Consider Region</label>
                  </div>
                </div>
              </div>

              <div class="row g-3 mt-2">
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

              <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-sm px-3">Save Settings</button>
              </div>
            </form>
          </div>
        </div>

        <div class="workspace-panel">
          <div class="workspace-panel-header">
            <div>
              <h5 class="workspace-panel-title mb-0">Technician Configuration</h5>
              <div class="workspace-panel-lead">Review regional coverage and standby readiness for each technician.</div>
            </div>
          </div>
          <div class="workspace-panel-body">
            <form action="{{ route('technicians.regions.update') }}" method="POST">
              @csrf
              <div class="table-responsive">
                <table class="table table-sm align-middle config-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Section</th>
                      <th>Status</th>
                      <th>Region</th>
                      <th>Weekly Standby</th>
                      <th>Weekend Standby</th>
                      <th>On Leave</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($technicians as $tech)
                      <tr data-user-id="{{ $tech->id }}">
                        <td>
                          <div class="workspace-cell-main">{{ $tech->name }}</div>
                          <div class="workspace-cell-sub">{{ $tech->status_name }}</div>
                        </td>
                        <td>{{ $tech->section }}</td>
                        <td>{{ $tech->status_name }}</td>
                        <td>
                          <input type="hidden" name="user_id[]" value="{{ $tech->id }}">
                          <select name="region[]" class="form-select form-select-sm js-user-setting" data-field="region" data-user-id="{{ $tech->id }}">
                            <option value="">-- none --</option>
                            @foreach($regions as $region)
                              <option value="{{ $region }}" {{ $tech->region === $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                          </select>
                        </td>
                        <td><div class="form-check"><input class="form-check-input js-user-setting" type="checkbox" data-field="weekly_standby" data-user-id="{{ $tech->id }}" {{ $tech->weekly_standby ? 'checked' : '' }}></div></td>
                        <td><div class="form-check"><input class="form-check-input js-user-setting" type="checkbox" data-field="weekend_standby" data-user-id="{{ $tech->id }}" {{ $tech->weekend_standby ? 'checked' : '' }}></div></td>
                        <td><div class="form-check"><input class="form-check-input js-user-setting" type="checkbox" data-field="on_leave" data-user-id="{{ $tech->id }}" {{ ($tech->status_name === 'On Leave') ? 'checked' : '' }}></div></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-sm px-3">Save Regions</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function() {
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

    // Auto-save global settings on change
    document.querySelectorAll('.js-setting').forEach(el => {
      el.addEventListener('change', async () => {
        const field = el.dataset.field;
        let value;
        if (el.type === 'checkbox') { value = el.checked ? 1 : 0; }
        else { value = el.value; }
        await postJSON('{{ route('technicians.settings.ajax') }}', { field, value });
      });
    });

    // Auto-save per-user settings on change
    document.querySelectorAll('.js-user-setting').forEach(el => {
      el.addEventListener('change', async () => {
        const field = el.dataset.field;
        const userId = el.dataset.userId;
        let value;
        if (el.tagName === 'SELECT') { value = el.value || null; }
        else if (el.type === 'checkbox') { value = el.checked ? 1 : 0; }
        else { value = el.value; }
        const url = `${'{{ url('technicians/users') }}'}/${userId}/setting`;
        await postJSON(url, { field, value });
      });
    });
  })();
</script>
@endsection
