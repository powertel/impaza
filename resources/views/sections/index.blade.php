
@extends('layouts.admin')

@section('title')
Sections
@endsection

@include('partials.css')

@section('styles')
<style>
  .sections-page .sections-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .sections-page .sections-summary-card {
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

  .sections-page .sections-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .sections-page .sections-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .sections-page .sections-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .sections-page .sections-summary-icon {
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

  .sections-page .sections-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .sections-page .sections-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .sections-page .sections-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .sections-page .sections-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(240px, 1fr) auto auto;
  }

  .sections-page .sections-toolbar .toolbar-search-form,
  .sections-page .sections-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .sections-page .sections-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .sections-page .sections-table td {
    color: var(--impaza-text);
  }

  .sections-page .section-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .sections-page .section-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  @media (max-width: 991.98px) {
    .sections-page .sections-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sections-page .sections-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .sections-page .sections-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .sections-page .sections-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .sections-page .sections-toolbar {
      grid-template-columns: 1fr;
    }

    .sections-page .sections-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $sectionCount = $sections->count();
  $departmentCount = $sections->pluck('department')->filter()->unique()->count();
  $namedSectionCount = $sections->pluck('section')->filter()->unique()->count();
@endphp

<section class="content workflow-faults-page sections-page">
  <div class="sections-summary-grid">
    <div class="sections-summary-card" style="--summary-color:#6366F1;">
      <div class="sections-summary-body">
        <div class="sections-summary-copy">
          <span class="sections-summary-icon"><i class="fas fa-sitemap"></i></span>
          <div>
            <div class="sections-summary-label">Total Sections</div>
            <div class="sections-summary-title">Org groupings</div>
          </div>
        </div>
        <div class="sections-summary-value">{{ $sectionCount }}</div>
      </div>
    </div>
    <div class="sections-summary-card" style="--summary-color:#0EA5E9;">
      <div class="sections-summary-body">
        <div class="sections-summary-copy">
          <span class="sections-summary-icon"><i class="fas fa-building"></i></span>
          <div>
            <div class="sections-summary-label">Departments Covered</div>
            <div class="sections-summary-title">Mapped parents</div>
          </div>
        </div>
        <div class="sections-summary-value">{{ $departmentCount }}</div>
      </div>
    </div>
    <div class="sections-summary-card" style="--summary-color:#10B981;">
      <div class="sections-summary-body">
        <div class="sections-summary-copy">
          <span class="sections-summary-icon"><i class="fas fa-layer-group"></i></span>
          <div>
            <div class="sections-summary-label">Named Units</div>
            <div class="sections-summary-title">Unique section labels</div>
          </div>
        </div>
        <div class="sections-summary-value">{{ $namedSectionCount }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Sections</h3>
        <div class="page-lead">Search, review, and maintain sections with the same modern workspace and dark-mode friendly filters used on departments.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $sectionCount }} total records</span>
        @can('department-create')
          <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#sectionCreateModal"><i class="fas fa-plus-circle me-1"></i> Create Section</button>
        @endcan
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar sections-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="sectionsPageSize" class="form-select" aria-label="Rows per page">
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
            <input type="text" id="sectionsSearch" class="form-control" placeholder="Search sections or departments">
          </div>
        </form>
        <button type="button" class="btn btn-primary btn-sm" id="sectionsApplyFilters"><i class="fas fa-search me-1"></i> Search</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="sectionsResetFilters"><i class="fas fa-rotate-left me-1"></i> Reset</button>
      </div>
    </div>

    <div class="card-body">
      <div class="faults-table-shell">
        <div class="table-responsive">
          <table class="table table-hover align-middle js-paginated-table sections-table" data-page-size="20" data-page-size-control="#sectionsPageSize" data-pager="#sectionsPager" data-search="#sectionsSearch">
            <thead>
              <tr>
                <th>No.</th>
                <th>Department</th>
                <th>Section</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sections as $section)
              <tr>
                <td data-label="No."><span class="record-chip">#{{ ++$i }}</span></td>
                <td data-label="Department">
                  <div class="record-meta">
                    <span class="record-main">{{ $section->department ?? 'Not Assigned' }}</span>
                    <span class="record-sub">Parent department</span>
                  </div>
                </td>
                <td data-label="Section">
                  <div class="record-meta">
                    <span class="section-name">{{ $section->section }}</span>
                    <span class="section-helper">Organizational section</span>
                  </div>
                </td>
                <td class="text-end" data-label="Action">
                  <div class="workflow-actions">
                    @can('department-edit')
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sectionEditModal{{ $section->id }}">
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
          <small class="table-note">Use the toolbar above to refine section results instantly.</small>
          <div id="sectionsPager" class="m-0"></div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('sections.create_modal')
@include('sections.edit_modal')
 
@endsection

@section('scripts')
  @include('partials.scripts')
  <script>
    document.getElementById('sectionsApplyFilters')?.addEventListener('click', function () {
      const search = document.getElementById('sectionsSearch');
      if (search) {
        search.dispatchEvent(new Event('input', { bubbles: true }));
        search.focus();
      }
    });

    document.getElementById('sectionsResetFilters')?.addEventListener('click', function () {
      const search = document.getElementById('sectionsSearch');
      const pageSize = document.getElementById('sectionsPageSize');

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
