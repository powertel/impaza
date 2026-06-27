@extends('layouts.admin')

@section('title')
Departments
@endsection

@include('partials.css')

@section('styles')
<style>
  .departments-page .departments-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .departments-page .departments-summary-card {
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

  .departments-page .departments-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .departments-page .departments-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .departments-page .departments-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .departments-page .departments-summary-icon {
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

  .departments-page .departments-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .departments-page .departments-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .departments-page .departments-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .departments-page .departments-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(240px, 1fr) auto auto;
  }

  .departments-page .departments-toolbar .toolbar-search-form {
    width: 100%;
    min-width: 0;
  }

  .departments-page .departments-toolbar .toolbar-search-form .input-group {
    width: 100%;
  }

  .departments-page .departments-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .departments-page .departments-table td {
    color: var(--impaza-text);
  }

  .departments-page .departments-table .department-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .departments-page .departments-table .department-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  .departments-page .departments-table .chip-stack {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 6px;
  }

  .departments-page .departments-table .workflow-actions form {
    margin: 0;
  }

  @media (max-width: 991.98px) {
    .departments-page .departments-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .departments-page .departments-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .departments-page .departments-toolbar .toolbar-search-form {
      grid-column: span 2;
    }

    .departments-page .departments-toolbar .departments-toolbar-search-btn,
    .departments-page .departments-toolbar .departments-toolbar-reset-btn {
      grid-column: span 1;
    }
  }

  @media (max-width: 767.98px) {
    .departments-page .departments-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .departments-page .departments-toolbar {
      grid-template-columns: 1fr;
    }

    .departments-page .departments-toolbar .toolbar-search-form {
      grid-column: auto;
    }

    .departments-page .departments-toolbar .departments-toolbar-search-btn,
    .departments-page .departments-toolbar .departments-toolbar-reset-btn {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')
@php
  $departmentCount = $departments->count();
  $sectionCount = $departments->sum(fn ($department) => $department->sections->count());
  $positionCount = $departments->sum(fn ($department) => $department->positions->count());
@endphp

<section class="content workflow-faults-page departments-page">
  <div class="departments-summary-grid">
    <div class="departments-summary-card" style="--summary-color:#6366F1;">
      <div class="departments-summary-body">
        <div class="departments-summary-copy">
          <span class="departments-summary-icon"><i class="fas fa-building"></i></span>
          <div>
            <div class="departments-summary-label">Total Departments</div>
            <div class="departments-summary-title">Organization structure</div>
          </div>
        </div>
        <div class="departments-summary-value">{{ $departmentCount }}</div>
      </div>
    </div>

    <div class="departments-summary-card" style="--summary-color:#0EA5E9;">
      <div class="departments-summary-body">
        <div class="departments-summary-copy">
          <span class="departments-summary-icon"><i class="fas fa-sitemap"></i></span>
          <div>
            <div class="departments-summary-label">Mapped Sections</div>
            <div class="departments-summary-title">Department groupings</div>
          </div>
        </div>
        <div class="departments-summary-value">{{ $sectionCount }}</div>
      </div>
    </div>

    <div class="departments-summary-card" style="--summary-color:#10B981;">
      <div class="departments-summary-body">
        <div class="departments-summary-copy">
          <span class="departments-summary-icon"><i class="fas fa-briefcase"></i></span>
          <div>
            <div class="departments-summary-label">Configured Positions</div>
            <div class="departments-summary-title">Assigned roles</div>
          </div>
        </div>
        <div class="departments-summary-value">{{ $positionCount }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Departments</h3>
        <div class="page-lead">Search, review, edit, and maintain department structures from one responsive workspace with dark-theme friendly filters.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $departmentCount }} total records</span>
        @can('department-create')
          <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#departmentCreateModal">
            <i class="fas fa-plus-circle me-1"></i> Create Department
          </button>
        @endcan
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar departments-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="departmentsPageSize" class="form-select" aria-label="Rows per page">
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
            <input type="text" id="departmentsSearch" class="form-control" placeholder="Search departments, sections, or positions">
          </div>
        </form>

        <button type="button" class="btn btn-primary btn-sm departments-toolbar-search-btn" id="departmentsApplyFilters">
          <i class="fas fa-search me-1"></i> Search
        </button>

        <button type="button" class="btn btn-outline-secondary btn-sm departments-toolbar-reset-btn" id="departmentsResetFilters">
          <i class="fas fa-rotate-left me-1"></i> Reset
        </button>
      </div>
    </div>

    <div class="card-body">
      <div class="faults-table-shell">
        <div class="table-responsive">
        <table id="departmentsTable" class="table table-hover align-middle js-paginated-table departments-table" data-page-size="20" data-page-size-control="#departmentsPageSize" data-pager="#departmentsPager" data-search="#departmentsSearch">
          <thead>
            <tr>
              <th>No.</th>
              <th>Department</th>
              <th>Sections</th>
              <th>Positions</th>
              <th class="text-end">Action(s)</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($departments as $department)
              @php
                $sectionTotal = $department->sections->count();
                $positionTotal = $department->positions->count();
              @endphp
              <tr>
                <td data-label="No.">
                  <span class="record-chip">#{{ ++$i }}</span>
                </td>
                <td data-label="Department">
                  <div class="record-meta">
                    <span class="department-name">{{ $department->department }}</span>
                    <span class="department-helper">Core organizational unit</span>
                  </div>
                </td>
                <td data-label="Sections">
                  <div class="chip-stack">
                    <span class="record-chip"><i class="fas fa-sitemap"></i> {{ $sectionTotal }} {{ Str::plural('Section', $sectionTotal) }}</span>
                  </div>
                </td>
                <td data-label="Positions">
                  <div class="chip-stack">
                    <span class="record-chip"><i class="fas fa-briefcase"></i> {{ $positionTotal }} {{ Str::plural('Position', $positionTotal) }}</span>
                  </div>
                </td>
                <td class="text-end" data-label="Actions">
                  <div class="workflow-actions">
                    @can('department-list')
                      <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#departmentShowModal{{ $department->id }}">
                        <i class="fas fa-eye me-1"></i> View
                      </button>
                    @endcan
                    @can('department-edit')
                      <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#departmentEditModal{{ $department->id }}">
                        <i class="fas fa-edit me-1"></i> Edit
                      </button>
                    @endcan
                    @can('department-delete')
                      <form action="{{ route('departments.destroy', $department->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm show_confirm" data-name="{{ $department->department }}" title="Delete">
                          <i class="fas fa-trash me-1"></i> Delete
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        </div>

        <div class="workflow-pagination">
          <small class="table-note">Use the filters above to narrow down department records instantly.</small>
          <div id="departmentsPager" class="m-0"></div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('departments.create_modal')
@include('departments.edit_modal')
@include('departments.show_modal')

@endsection

@section('scripts')
<script>
  document.getElementById('departmentsApplyFilters')?.addEventListener('click', function () {
    const search = document.getElementById('departmentsSearch');
    if (search) {
      search.dispatchEvent(new Event('input', { bubbles: true }));
      search.focus();
    }
  });

  document.getElementById('departmentsResetFilters')?.addEventListener('click', function () {
    const search = document.getElementById('departmentsSearch');
    const pageSize = document.getElementById('departmentsPageSize');

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
