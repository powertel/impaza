@extends('layouts.admin')

@section('title')
Pops
@endsection
@include('partials.css')

@section('styles')
<style>
  .pops-page .pops-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .pops-page .pops-summary-card {
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

  .pops-page .pops-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .pops-page .pops-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .pops-page .pops-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .pops-page .pops-summary-icon {
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

  .pops-page .pops-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .pops-page .pops-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .pops-page .pops-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .pops-page .pops-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto auto;
  }

  .pops-page .pops-toolbar .toolbar-search-form,
  .pops-page .pops-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .pops-page .pops-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .pops-page .pop-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .pops-page .pop-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  @media (max-width: 991.98px) {
    .pops-page .pops-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .pops-page .pops-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .pops-page .pops-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .pops-page .pops-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .pops-page .pops-toolbar {
      grid-template-columns: 1fr;
    }

    .pops-page .pops-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $popCount = $pops->total();
  $cityCoverage = $pops->pluck('city')->filter()->unique()->count();
  $locationCoverage = $pops->pluck('suburb')->filter()->unique()->count();
  $perPage = request('per_page', 20);
@endphp
<section class="content">
  <div class="pops-summary-grid">
    <div class="pops-summary-card" style="--summary-color:#6366F1;">
      <div class="pops-summary-body">
        <div class="pops-summary-copy">
          <span class="pops-summary-icon"><i class="fas fa-bullseye"></i></span>
          <div>
            <div class="pops-summary-label">Total POPs</div>
            <div class="pops-summary-title">Network access points</div>
          </div>
        </div>
        <div class="pops-summary-value">{{ $popCount }}</div>
      </div>
    </div>
    <div class="pops-summary-card" style="--summary-color:#0EA5E9;">
      <div class="pops-summary-body">
        <div class="pops-summary-copy">
          <span class="pops-summary-icon"><i class="fas fa-city"></i></span>
          <div>
            <div class="pops-summary-label">Cities Covered</div>
            <div class="pops-summary-title">Mapped towns</div>
          </div>
        </div>
        <div class="pops-summary-value">{{ $cityCoverage }}</div>
      </div>
    </div>
    <div class="pops-summary-card" style="--summary-color:#10B981;">
      <div class="pops-summary-body">
        <div class="pops-summary-copy">
          <span class="pops-summary-icon"><i class="fas fa-map-marker-alt"></i></span>
          <div>
            <div class="pops-summary-label">Locations Covered</div>
            <div class="pops-summary-title">Deployment spread</div>
          </div>
        </div>
        <div class="pops-summary-value">{{ $locationCoverage }}</div>
      </div>
    </div>
  </div>

  <div class="card workflow-faults-page pops-page">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage POPs</h3>
        <div class="page-lead">Search, review, edit, and inspect POP records using the same modern network workspace and filter bar style.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $popCount }} total records</span>
        @can('pop-create')
          <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#popCreateModal"><i class="fas fa-plus-circle me-1"></i>Create Pop</button>
        @endcan
      </div>
    </div>
    <div class="faults-toolbar">
      <form method="GET" action="{{ route('pops.index') }}" class="filter-toolbar pops-toolbar m-0">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="popsPageSize" class="form-select form-select-sm" style="width:auto;">
              <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>Show 10</option>
              <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>Show 20</option>
              <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>Show 50</option>
              <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>Show 100</option>
            </select>
          </div>
        </div>
        <div class="toolbar-search-form">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search POPs, cities, or locations">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Search</button>
        <a href="{{ route('pops.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Reset</a>
      </form>
    </div>
    <div class="card-body">
      <div class="faults-table-shell">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>No.</th>
                <th>City/Town</th>
                <th>Location</th>
                <th>Pop</th>
                <th class="text-end">Action(s)</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($pops as $pop)
              <tr>
                <td data-label="No."><span class="record-chip">#{{ $loop->iteration + $pops->firstItem() - 1 }}</span></td>
                <td data-label="City/Town">
                  <div class="record-meta">
                    <span class="record-main">{{ $pop->city }}</span>
                    <span class="record-sub">Mapped city</span>
                  </div>
                </td>
                <td data-label="Location">
                  <div class="record-meta">
                    <span class="record-main">{{ $pop->suburb }}</span>
                    <span class="record-sub">Parent location</span>
                  </div>
                </td>
                <td data-label="Pop">
                  <div class="record-meta">
                    <span class="pop-name">{{ $pop->pop }}</span>
                    <span class="pop-helper">POP label</span>
                  </div>
                </td>
                <td class="text-end" data-label="Action(s)">
                  <div class="workflow-actions">
                    @can('pop-edit')
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#popEditModal{{ $pop->id }}">
                      <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    @endcan
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#popViewModal{{ $pop->id }}">
                      <i class="fas fa-eye me-1"></i>View
                    </button>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody> 
          </table>
        </div>
        <div class="workflow-pagination">
          <small class="table-note">
            Showing {{ $pops->firstItem() }} to {{ $pops->lastItem() }} of {{ $pops->total() }} results
          </small>
          {{ $pops->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
    @include('pops.create_modal')
    @include('pops.view_modal')
    @include('pops.edit_modal')
  </div>
</section>
 @endsection

@section('scripts')
  <script>
    document.getElementById('popsPageSize')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      params.set('per_page', this.value);
      params.delete('page');
      window.location.search = params.toString();
    });
  </script>
@endsection

