@extends('layouts.admin')

@section('title')
Technician Settings
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Auto-Assignment Settings</h3>
        </div>
        <div class="card-body">
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

            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Technician Configuration</h3>
        </div>
        <div class="card-body">
          <form action="{{ route('technicians.regions.update') }}" method="POST">
            @csrf
            <div class="table-responsive">
              <table class="table table-sm align-middle">
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
                      <td>{{ $tech->name }}</td>
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
                      <td>
                        <div class="form-check">
                          <input class="form-check-input js-user-setting" type="checkbox" data-field="weekly_standby" data-user-id="{{ $tech->id }}" {{ $tech->weekly_standby ? 'checked' : '' }}>
                        </div>
                      </td>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input js-user-setting" type="checkbox" data-field="weekend_standby" data-user-id="{{ $tech->id }}" {{ $tech->weekend_standby ? 'checked' : '' }}>
                        </div>
                      </td>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input js-user-setting" type="checkbox" data-field="on_leave" data-user-id="{{ $tech->id }}" {{ ($tech->status_name === 'On Leave') ? 'checked' : '' }}>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary">Save Regions</button>
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
