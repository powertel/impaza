@extends('layouts.admin')

@section('title')
Zones
@endsection
@include('partials.css')
@section('styles')
<style>
  .zones-page .zones-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto auto;
  }

  .zones-page .toolbar-search-form,
  .zones-page .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  @media (max-width: 991.98px) {
    .zones-page .zones-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .zones-page .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .zones-page .zones-toolbar {
      grid-template-columns: 1fr;
    }

    .zones-page .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection
@section('content')
@php
    $zoneCount = $zones->count();
    $zonesWithRegion = $zones->whereNotNull('region')->where('region', '!=', '')->count();
    $popsMapped = $zones->sum('pops_count');
@endphp

<section class="content workflow-faults-page zones-page">
    <div class="workspace-summary-grid">
        <div class="workspace-summary-card" style="--summary-color:#6366F1;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-map-marked-alt"></i></span>
                    <div>
                        <div class="workspace-summary-label">Total Zones</div>
                        <div class="workspace-summary-title">Coverage groups</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $zoneCount }}</div>
            </div>
        </div>
        <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-globe-africa"></i></span>
                    <div>
                        <div class="workspace-summary-label">Region Tagged</div>
                        <div class="workspace-summary-title">Zones with region scope</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $zonesWithRegion }}</div>
            </div>
        </div>
        <div class="workspace-summary-card" style="--summary-color:#10B981;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-broadcast-tower"></i></span>
                    <div>
                        <div class="workspace-summary-label">Mapped POPs</div>
                        <div class="workspace-summary-title">Assigned infrastructure</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $popsMapped }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Manage Zones</h3>
                <div class="page-lead">Search, review, edit, and maintain technician zone mappings from one responsive workspace with dark-theme friendly controls.</div>
            </div>
            <div class="card-tools">
                <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $zoneCount }} total records</span>
                @can('technician-configuration')
                <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#zoneCreateModal">
                    <i class="fas fa-plus-circle me-1"></i> Create Zone
                </button>
                @endcan
            </div>
        </div>

        <div class="faults-toolbar">
            <div class="filter-toolbar zones-toolbar">
                <div class="faults-toolbar-field">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <select id="zonesPageSize" class="form-select" aria-label="Rows per page">
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
                        <input type="text" id="zonesSearch" class="form-control" placeholder="Search zones, regions, or mapped POP counts">
                    </div>
                </form>

                <button type="button" id="zonesSearchButton" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-search me-1"></i> Search
                </button>

                <button type="button" id="zonesResetButton" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#zonesPageSize" data-pager="#zonesPager" data-search="#zonesSearch">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Zone</th>
                            <th>Region</th>
                            <th>POPs Count</th>
                            <th class="text-end">Action(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($zones as $zone)
                        <tr>
                            <td><span class="age-ticker">#{{ ++$i }}</span></td>
                            <td>
                                <div class="workspace-cell-main">{{ $zone->name }}</div>
                                <div class="workspace-cell-sub">Technician coverage zone</div>
                            </td>
                            <td>
                                <div class="workspace-cell-main">{{ $zone->region ?: 'Not set' }}</div>
                                <div class="workspace-cell-sub">Regional scope</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill" style="background: rgba(99, 102, 241, .12); color: #4338CA;">{{ $zone->pops_count }} POPs</span>
                            </td>
                            <td>
                                <div class="workspace-actions">
                                    <form action="{{ route('zones.destroy',$zone->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        @can('technician-configuration')
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#zoneEditModal{{ $zone->id }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                        @endcan
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="zonesPager" class="mt-3"></div>
            </div>
        </div>
    </div>

    @include('zones.create_modal')
    @include('zones.edit_modal')
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id^="zoneCreateModal"], [id^="zoneEditModal"]').forEach(function (modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    const zonesSearch = document.getElementById('zonesSearch');
    const zonesSearchButton = document.getElementById('zonesSearchButton');
    const zonesResetButton = document.getElementById('zonesResetButton');

    if (zonesSearchButton && zonesSearch) {
        zonesSearchButton.addEventListener('click', function () {
            zonesSearch.dispatchEvent(new Event('input', { bubbles: true }));
            zonesSearch.focus();
        });
    }

    if (zonesResetButton && zonesSearch) {
        zonesResetButton.addEventListener('click', function () {
            zonesSearch.value = '';
            zonesSearch.dispatchEvent(new Event('input', { bubbles: true }));
        });
    }
});
</script>
@endsection
