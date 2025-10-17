@extends('layouts.admin')

@section('title')
Technician Configuration
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Technician Configuration</h3>
          <div>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#autoSettingsModal">Auto-Assign Settings</button>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
              <div class="input-group input-group-sm" style="width: 170px;">
                  <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                  <select id="autoAssignSize" class="form-select form-select-sm" style="width:auto;">
                      <option value="10">10</option>
                      <option value="20" selected>20</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="all">All</option>
                  </select>
              </div>
              <div class="input-group input-group-sm" style="width: 220px;">
                  <input type="text" id="autoAssignSearch" class="form-control" placeholder="Search Technicians">
              </div>
          </div>
          <form action="{{ route('technicians.regions.update') }}" method="POST">
            @csrf
            <div class="table-responsive">
              <table  class="table table-hover align-middle js-paginated-table" data-page-size="20" data-page-size-control="#autoAssignSize" data-pager="#autoAssignPager" data-search="#autoAssignSearch">
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
                  @foreach($technicians as $t)
                    <tr data-user-id="{{ $t->id }}">
                      <td>{{ $t->name }}</td>
                      <td>{{ $t->section }}</td>
                      <td>
                        <select class="form-select form-select-sm js-user-setting" data-field="user_status" data-role="status-select" {{ $t->status_name === 'On Leave' ? 'disabled' : '' }}>
                          <option value="Assignable" {{ $t->status_name === 'Assignable' ? 'selected' : '' }}>Assignable</option>
                          <option value="Away" {{ $t->status_name === 'Away' ? 'selected' : '' }}>Away (Business Trip)</option>
                        </select>
                      </td>
                      <td>
                        <input type="hidden" name="user_id[]" value="{{ $t->id }}">
                        <select name="region[]" class="form-select form-select-sm js-user-setting" data-field="region">
                          <option value="">Not set</option>
                          @foreach($regions as $region)
                            <option value="{{ $region }}" {{ $t->region === $region ? 'selected' : '' }}>{{ $region }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td>
                        <input type="checkbox" class="form-check-input js-user-setting" data-field="weekly_standby" {{ $t->weekly_standby ? 'checked' : '' }}>
                      </td>
                      <td>
                        <input type="checkbox" class="form-check-input js-user-setting" data-field="weekend_standby" {{ $t->weekend_standby ? 'checked' : '' }}>
                      </td>
                      <td>
                        <input type="checkbox" class="form-check-input js-user-setting" data-field="on_leave" {{ $t->status_name === 'On Leave' ? 'checked' : '' }}>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div id="autoAssignPager" class="mt-2"></div>
            <!-- <div class="mt-2">
              <button type="submit" class="btn btn-primary">Save Regions</button>
            </div> -->
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Auto-Assign Settings Modal -->
  <div class="modal fade" id="autoSettingsModal" tabindex="-1" aria-labelledby="autoSettingsLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="autoSettingsLabel">Auto-Assignment Settings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('technicians.settings.update') }}" method="POST">
            @csrf
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Weekly Standby Start</label>
                <input type="time" name="standby_start_time" class="form-control js-setting" value="{{ old('standby_start_time', \Carbon\Carbon::parse($settings->standby_start_time ?? '16:30:00')->format('H:i')) }}" data-field="standby_start_time">
              </div>
              <div class="col-md-6">
                <label class="form-label">Weekly Standby End</label>
                <input type="time" name="standby_end_time" class="form-control js-setting" value="{{ old('standby_end_time', \Carbon\Carbon::parse($settings->standby_end_time ?? '06:00:00')->format('H:i')) }}" data-field="standby_end_time">
              </div>
            </div>

            <div class="row g-2 mt-2">
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
                  <input class="form-check-input js-setting" type="checkbox" name="consider_region" value="1" id="consider_region" data-field="consider_region" {{ old('consider_region', ($settings->consider_region ?? true)) ? 'checked' : '' }}>
                  <label class="form-check-label" for="consider_region">Consider Region</label>
                </div>
              </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">Save Settings</button>
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
        const field = el.dataset.field;
        let value;
        if (el.type === 'checkbox') { value = el.checked ? 1 : 0; }
        else { value = el.value; }
        await postJSON('{{ route('technicians.settings.ajax') }}', { field, value });
      });
    });
  })();
</script>
@endsection
