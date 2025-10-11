@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@section('pageName')
Dashboard
@endsection

@section('content')
<section class="content">
  <div class="row">
    <!-- Stat cards -->
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted" style="font-size:12px">Faults</div>
              <div class="h4 mb-0">{{ $faultCount ?? 0 }}</div>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#eef4ff;color:#1f5cff">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted" style="font-size:12px">Customers</div>
              <div class="h4 mb-0">{{ $customerCount ?? 0 }}</div>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#e8fff2;color:#16a34a">
              <i class="fas fa-address-card"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted" style="font-size:12px">Links</div>
              <div class="h4 mb-0">{{ $linkCount ?? 0 }}</div>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#fff7ed;color:#f97316">
              <i class="fas fa-link"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted" style="font-size:12px">Open Today</div>
              <div class="h4 mb-0">{{ ($recentFaults ?? collect([]))->where('created_at','>=', now()->startOfDay())->count() }}</div>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#f1f5f9;color:#0ea5e9">
              <i class="fas fa-calendar-day"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="row mb-3">
    <div class="col">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between py-2">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-filter"></i> Filters</button>
            <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-download"></i> Export</button>
          </div>
          <a class="btn btn-primary btn-sm" href="{{ route('faults.index') }}"><i class="fas fa-plus-circle"></i> Log Fault</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" style="font-size:13px">Recent Faults</h3>
        </div>
        <div class="card-body p-2">
          <div class="table-responsive">
            <table class="table table-hover table-sm" id="dashboard-recent-faults">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Link</th>
                  <th>Created At</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($recentFaults ?? []) as $f)
                  <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->customer }}</td>
                    <td>{{ $f->link }}</td>
                    <td>{{ \Carbon\Carbon::parse($f->created_at)->format('d M Y, h:i A') }}</td>
                  </tr>
                @empty
                  <tr class="no-data">
                    <td class="text-muted">No recent faults</td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
$(function(){
  $('#dashboard-recent-faults').DataTable({
    order: [[3,'desc']],
    pageLength: 10,
    lengthChange: false
  });
});
</script>
@endsection
