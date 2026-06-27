
@extends('layouts.admin')

@section('title')
Positions
@endsection
@include('partials.css')

@section('styles')
<style>
  .positions-page .positions-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .positions-page .positions-summary-card {
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

  .positions-page .positions-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .positions-page .positions-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .positions-page .positions-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .positions-page .positions-summary-icon {
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

  .positions-page .positions-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .positions-page .positions-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .positions-page .positions-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .positions-page .positions-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(240px, 1fr) auto auto;
  }

  .positions-page .positions-toolbar .toolbar-search-form,
  .positions-page .positions-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .positions-page .positions-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .positions-page .position-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .positions-page .position-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  @media (max-width: 991.98px) {
    .positions-page .positions-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .positions-page .positions-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .positions-page .positions-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .positions-page .positions-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .positions-page .positions-toolbar {
      grid-template-columns: 1fr;
    }

    .positions-page .positions-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $positionCount = $positions->count();
  $sectionCount = $positions->pluck('section')->filter()->unique()->count();
  $roleCount = $positions->pluck('position')->filter()->unique()->count();
@endphp

<section class="content workflow-faults-page positions-page">
  <div class="positions-summary-grid">
    <div class="positions-summary-card" style="--summary-color:#6366F1;">
      <div class="positions-summary-body">
        <div class="positions-summary-copy">
          <span class="positions-summary-icon"><i class="fas fa-briefcase"></i></span>
          <div>
            <div class="positions-summary-label">Total Positions</div>
            <div class="positions-summary-title">Configured roles</div>
          </div>
        </div>
        <div class="positions-summary-value">{{ $positionCount }}</div>
      </div>
    </div>
    <div class="positions-summary-card" style="--summary-color:#0EA5E9;">
      <div class="positions-summary-body">
        <div class="positions-summary-copy">
          <span class="positions-summary-icon"><i class="fas fa-sitemap"></i></span>
          <div>
            <div class="positions-summary-label">Sections Covered</div>
            <div class="positions-summary-title">Mapped org units</div>
          </div>
        </div>
        <div class="positions-summary-value">{{ $sectionCount }}</div>
      </div>
    </div>
    <div class="positions-summary-card" style="--summary-color:#10B981;">
      <div class="positions-summary-body">
        <div class="positions-summary-copy">
          <span class="positions-summary-icon"><i class="fas fa-layer-group"></i></span>
          <div>
            <div class="positions-summary-label">Unique Roles</div>
            <div class="positions-summary-title">Named job titles</div>
          </div>
        </div>
        <div class="positions-summary-value">{{ $roleCount }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Positions</h3>
        <div class="page-lead">Search, review, and update positions with the same modern filter bar and responsive workspace used across the refreshed organization pages.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $positionCount }} total records</span>
        @can('department-create')
        <button class="btn btn-primary btn-sm px-3" href="#" data-bs-toggle="modal" data-bs-target="#positionCreateModal"><i class="fas fa-plus-circle me-1"></i>Create Position</button>
        @endcan
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar positions-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="positionsPageSize" class="form-select" aria-label="Rows per page">
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
            <input type="text" id="positionsSearch" class="form-control" placeholder="Search positions or sections">
          </div>
        </form>
        <button type="button" class="btn btn-primary btn-sm" id="positionsApplyFilters"><i class="fas fa-search me-1"></i> Search</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="positionsResetFilters"><i class="fas fa-rotate-left me-1"></i> Reset</button>
      </div>
    </div>

    <div class="card-body">
      <div class="faults-table-shell">
        <div class="table-responsive">
          <table class="table table-hover align-middle js-paginated-table" data-page-size="20" data-page-size-control="#positionsPageSize" data-pager="#positionsPager" data-search="#positionsSearch">
            <thead>
              <tr>
                <th>No.</th>
                <th>Section</th>
                <th>Position</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($positions as $position)
              <tr>
                <td data-label="No."><span class="record-chip">#{{ ++$i }}</span></td>
                <td data-label="Section">
                  <div class="record-meta">
                    <span class="record-main">{{ $position->section }}</span>
                    <span class="record-sub">Parent section</span>
                  </div>
                </td>
                <td data-label="Position">
                  <div class="record-meta">
                    <span class="position-name">{{ $position->position }}</span>
                    <span class="position-helper">Configured role</span>
                  </div>
                </td>
                <td class="text-end" data-label="Action">
                  <div class="workflow-actions">
                    @can('department-edit')
                    <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#positionEditModal" data-id="{{ $position->id }}" data-position="{{ $position->position }}">
                      <i class="fas fa-edit me-1"></i>Edit</a>
                    @endcan
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="workflow-pagination">
          <small class="table-note">Use the toolbar above to narrow down positions instantly.</small>
          <div id="positionsPager" class="m-0"></div>
        </div>
      </div>

      @include('positions.create_modal')
      @include('positions.edit_modal')
    </div>
  </div>
</section>

@endsection

@section('scripts')
    @include('partials.department')
    <script>
      document.getElementById('positionsApplyFilters')?.addEventListener('click', function () {
        const search = document.getElementById('positionsSearch');
        if (search) {
          search.dispatchEvent(new Event('input', { bubbles: true }));
          search.focus();
        }
      });

      document.getElementById('positionsResetFilters')?.addEventListener('click', function () {
        const search = document.getElementById('positionsSearch');
        const pageSize = document.getElementById('positionsPageSize');

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
