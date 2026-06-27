@extends('layouts.admin')

@section('title')
Cities
@endsection
@include('partials.css')

@section('styles')
<style>
  .cities-page .cities-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .cities-page .cities-summary-card {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 104px;
    border-radius: 18px;
    border: 1px solid var(--impaza-border);
    background: var(--impaza-card);
    box-shadow: var(--impaza-shadow-sm);
    overflow: hidden;
  }

  .cities-page .cities-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .cities-page .cities-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .cities-page .cities-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .cities-page .cities-summary-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--summary-color, var(--impaza-primary));
    background: color-mix(in srgb, var(--summary-color, var(--impaza-primary)) 12%, transparent);
    font-size: .95rem;
    flex: 0 0 auto;
  }

  .cities-page .cities-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .cities-page .cities-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .cities-page .cities-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .cities-page .cities-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(240px, 1fr) auto auto;
  }

  .cities-page .cities-toolbar .toolbar-search-form,
  .cities-page .cities-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .cities-page .cities-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .cities-page .city-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .cities-page .city-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  @media (max-width: 991.98px) {
    .cities-page .cities-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .cities-page .cities-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .cities-page .cities-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .cities-page .cities-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .cities-page .cities-toolbar {
      grid-template-columns: 1fr;
    }

    .cities-page .cities-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $cityCount = $cities->count();
  $regionCount = $cities->pluck('region')->filter()->unique()->count();
  $namedCityCount = $cities->pluck('city')->filter()->unique()->count();
@endphp

<section class="content workflow-faults-page cities-page">
  <div class="cities-summary-grid">
    <div class="cities-summary-card" style="--summary-color:#6366F1;">
      <div class="cities-summary-body">
        <div class="cities-summary-copy">
          <span class="cities-summary-icon"><i class="fas fa-city"></i></span>
          <div>
            <div class="cities-summary-label">Total Cities</div>
            <div class="cities-summary-title">Network footprint</div>
          </div>
        </div>
        <div class="cities-summary-value">{{ $cityCount }}</div>
      </div>
    </div>
    <div class="cities-summary-card" style="--summary-color:#0EA5E9;">
      <div class="cities-summary-body">
        <div class="cities-summary-copy">
          <span class="cities-summary-icon"><i class="fas fa-globe-africa"></i></span>
          <div>
            <div class="cities-summary-label">Regions Covered</div>
            <div class="cities-summary-title">Mapped areas</div>
          </div>
        </div>
        <div class="cities-summary-value">{{ $regionCount }}</div>
      </div>
    </div>
    <div class="cities-summary-card" style="--summary-color:#10B981;">
      <div class="cities-summary-body">
        <div class="cities-summary-copy">
          <span class="cities-summary-icon"><i class="fas fa-layer-group"></i></span>
          <div>
            <div class="cities-summary-label">Named Records</div>
            <div class="cities-summary-title">Unique city labels</div>
          </div>
        </div>
        <div class="cities-summary-value">{{ $namedCityCount }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Cities</h3>
        <div class="page-lead">Search, review, and maintain city and regional coverage using the same modern toolbar and responsive table workspace.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $cityCount }} total records</span>
        @can('city-create')
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#cityCreateModal"><i class="fas fa-plus-circle me-1"></i>Create City</button>
        @endcan
      </div>
    </div>
    <div class="faults-toolbar">
      <div class="filter-toolbar cities-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="citiesPageSize" class="form-select" aria-label="Rows per page">
              <option value="10">Show 10</option>
              <option value="20" selected>Show 20</option>
              <option value="50">Show 50</option>
              <option value="100">Show 100</option>
              <option value="all">Show All</option>
            </select>
          </div>
        </div>
        <form class="toolbar-search-form" onsubmit="return false;">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="citiesSearch" class="form-control" placeholder="Search cities or regions">
          </div>
        </form>
        <button type="button" class="btn btn-primary btn-sm" id="citiesApplyFilters"><i class="fas fa-search me-1"></i> Search</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="citiesResetFilters"><i class="fas fa-rotate-left me-1"></i> Reset</button>
      </div>
    </div>
    <div class="card-body">
      <div class="faults-table-shell">
        <div class="table-responsive">
          <table class="table table-hover align-middle js-paginated-table" data-page-size="20" data-page-size-control="#citiesPageSize" data-pager="#citiesPager" data-search="#citiesSearch">
            <thead>
              <tr>
                <th>No.</th>
                <th>City/Town</th>
                <th>Region</th>
                <th class="text-end">Action(s)</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($cities as $city)
              <tr>
                <td data-label="No."><span class="record-chip">#{{ ++$i }}</span></td>
                <td data-label="City/Town">
                  <div class="record-meta">
                    <span class="city-name">{{ $city->city }}</span>
                    <span class="city-helper">Network city record</span>
                  </div>
                </td>
                <td data-label="Region">
                  <span class="record-chip"><i class="fas fa-map"></i> {{ $city->region ?? 'Not Set' }}</span>
                </td>
                <td class="text-end" data-label="Action(s)">
                  <div class="workflow-actions">
                    @can('city-edit')
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cityEditModal{{ $city->id }}">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    @endcan
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody> 
          </table>
        </div>
        <div class="workflow-pagination">
          <small class="table-note">Use the toolbar above to narrow down city records instantly.</small>
          <div id="citiesPager" class="m-0"></div>
        </div>
      </div>
      @include('cities.create_modal')
      @include('cities.edit_modal')
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  document.getElementById('citiesApplyFilters')?.addEventListener('click', function () {
    const search = document.getElementById('citiesSearch');
    if (search) {
      search.dispatchEvent(new Event('input', { bubbles: true }));
      search.focus();
    }
  });

  document.getElementById('citiesResetFilters')?.addEventListener('click', function () {
    const search = document.getElementById('citiesSearch');
    const pageSize = document.getElementById('citiesPageSize');

    if (search) {
      search.value = '';
      search.dispatchEvent(new Event('input', { bubbles: true }));
    }

    if (pageSize) {
      pageSize.value = '20';
      pageSize.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });
</script>
@endsection

