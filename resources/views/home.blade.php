@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@section('pageName')
Dashboard
@endsection

@section('content')
<section class="content dashboard-page">
  <div class="row">
    <!-- Stat cards -->
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Faults</div>
              <div class="h4 mb-0 stat-value">{{ $faultCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-faults">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Customers</div>
              <div class="h4 mb-0 stat-value">{{ $customerCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-customers">
              <i class="fas fa-address-card"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Links</div>
              <div class="h4 mb-0 stat-value">{{ $linkCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-links">
              <i class="fas fa-link"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Open Today</div>
              <div class="h4 mb-0 stat-value">{{ ($recentFaults ?? collect([]))->where('created_at','>=', now()->startOfDay())->count() }}</div>
            </div>
            <div class="metric-icon icon-open">
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
      <div class="card toolbar-card">
        <div class="card-body d-flex align-items-center justify-content-between py-2">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fas fa-filter"></i> Filters</button>
            <button class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fas fa-download"></i> Export</button>
          </div>
          <a class="btn btn-primary btn-sm rounded-pill" href="{{ route('faults.index') }}"><i class="fas fa-plus-circle"></i> Log Fault</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Recent Faults</h3>
         <div class="card-tools">
           <input id="dashboardRecentSearch" type="text" class="form-control form-control-sm rounded-pill d-inline-block w-auto" placeholder="Search">
           <select id="dashboardRecentPageSize" class="form-control form-control-sm rounded-pill d-inline-block w-auto">
            <option value="10">10</option>
           <option value="20" selected>20</option>
           <option value="50">50</option>
             <option value="100">100</option>
          </select>
        </div>
        </div>
        <div class="card-body p-2">
          <div class="table-responsive">

           <table class="table table-hover table-sm js-paginated-table" id="dashboard-recent-faults" data-page-size="10" data-page-size-control="#dashboardRecentPageSize" data-pager="#dashboardRecentPager" data-search="#dashboardRecentSearch">
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
                    <td class="text-muted" colspan="4">No recent faults</td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                @endforelse
              </tbody>
            </table>
           <div id="dashboardRecentPager" class="mt-2"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection



@section('scripts')
@include('partials.scripts')
@endsection

