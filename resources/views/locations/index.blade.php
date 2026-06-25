@extends('layouts.admin')

@section('title')
Locations
@endsection
@include('partials.css')

@section('styles')
<style>
  .locations-page .locations-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .locations-page .locations-summary-card {
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

  .locations-page .locations-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .locations-page .locations-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .locations-page .locations-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .locations-page .locations-summary-icon {
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

  .locations-page .locations-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .locations-page .locations-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .locations-page .locations-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .locations-page .locations-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto auto;
  }

  .locations-page .locations-toolbar .toolbar-search-form,
  .locations-page .locations-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .locations-page .locations-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .locations-page .location-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .locations-page .location-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  @media (max-width: 991.98px) {
    .locations-page .locations-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .locations-page .locations-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .locations-page .locations-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .locations-page .locations-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .locations-page .locations-toolbar {
      grid-template-columns: 1fr;
    }

    .locations-page .locations-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $locationCount = $locations->total();
  $cityCoverage = $locations->pluck('city')->filter()->unique()->count();
  $visibleLocations = $locations->count();
  $perPage = request('per_page', 20);
@endphp
<section class="content">
  <div class="workflow-kpis locations-summary-grid">
    <div class="locations-summary-card" style="--summary-color:#6366F1;">
      <div class="locations-summary-body">
        <div class="locations-summary-copy">
          <span class="locations-summary-icon"><i class="fas fa-map-marker-alt"></i></span>
          <div>
            <div class="locations-summary-label">Total Locations</div>
            <div class="locations-summary-title">Network geography</div>
          </div>
        </div>
        <div class="locations-summary-value">{{ $locationCount }}</div>
      </div>
    </div>
    <div class="locations-summary-card" style="--summary-color:#0EA5E9;">
      <div class="locations-summary-body">
        <div class="locations-summary-copy">
          <span class="locations-summary-icon"><i class="fas fa-city"></i></span>
          <div>
            <div class="locations-summary-label">Cities Covered</div>
            <div class="locations-summary-title">Active footprint</div>
          </div>
        </div>
        <div class="locations-summary-value">{{ $cityCoverage }}</div>
      </div>
    </div>
    <div class="locations-summary-card" style="--summary-color:#10B981;">
      <div class="locations-summary-body">
        <div class="locations-summary-copy">
          <span class="locations-summary-icon"><i class="fas fa-list-check"></i></span>
          <div>
            <div class="locations-summary-label">Visible Results</div>
            <div class="locations-summary-title">Current page records</div>
          </div>
        </div>
        <div class="locations-summary-value">{{ $visibleLocations }}</div>
      </div>
    </div>
  </div>

  <div class="card workflow-faults-page locations-page">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Locations</h3>
        <div class="page-lead">Search, review, edit, and inspect locations from one responsive workspace with the same modern filter and table treatment.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $locationCount }} total records</span>
        @can('location-create')
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#locationCreateModal"><i class="fas fa-plus-circle me-1"></i>Create Location</button>
        @endcan
      </div>
    </div>
    <div class="faults-toolbar">
      <form method="GET" action="{{ route('locations.index') }}" class="filter-toolbar locations-toolbar m-0">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="locationsPageSize" class="form-select form-select-sm" style="width:auto;">
              <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>Show 10</option>
              <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>Show 20</option>
              <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>Show 50</option>
              <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>Show 100</option>
            </select>
          </div>
        </div>
        <div class="toolbar-search-form">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search locations or cities">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i> Search</button>
        <a href="{{ route('locations.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i> Reset</a>
      </form>
    </div>
    <div class="card-body">
      <div class="faults-table-shell">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>No.</th>
                <th>City</th>
                <th>Location</th>
                <th class="text-end">Action(s)</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($locations as $location)
              <tr>
                <td data-label="No."><span class="record-chip">#{{ (($locations->currentPage()-1)*$locations->perPage()) + $loop->iteration }}</span></td>
                <td data-label="City">
                  <div class="record-meta">
                    <span class="record-main">{{ $location->city }}</span>
                    <span class="record-sub">City coverage</span>
                  </div>
                </td>
                <td data-label="Location">
                  <div class="record-meta">
                    <span class="location-name">{{ $location->suburb }}</span>
                    <span class="location-helper">Mapped network location</span>
                  </div>
                </td>
                <td class="text-end" data-label="Action(s)">
                  <div class="workflow-actions">
                    @can('location-edit')
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#locationEditModal{{ $location->id }}">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    @endcan
                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#locationViewModal{{ $location->id }}">
                      <i class="fas fa-eye me-1"></i> View
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center empty-state">No locations found</td>
              </tr>
              @endforelse
            </tbody> 
          </table>
        </div>
        <div class="workflow-pagination">
          <small class="table-note">
            @if($locations->total())
              Showing {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ $locations->total() }} results
            @else
              Showing 0 results
            @endif
          </small>
          {{ $locations->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
 
</section>
@include('locations.create_modal')
@include('locations.edit_modal')
@include('locations.view_modal')
@endsection
@section('scripts')
    @include('partials.scripts')
    <script>
      document.getElementById('locationsPageSize')?.addEventListener('change', function(){
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', this.value);
        params.delete('page');
        window.location.search = params.toString();
      });
    </script>
@endsection
