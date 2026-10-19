@extends('layouts.admin')

@section('title')
 Permissions
@endsection
@include('partials.css')
@section('styles')
<style>
  .permissions-page .permissions-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(120px, 180px) minmax(260px, 1fr) auto auto;
  }

  .permissions-page .permissions-toolbar-search {
    width: 100%;
    min-width: 0;
  }

  .permissions-page .permissions-toolbar-search .input-group {
    width: 100%;
  }

  .perm-row-preset .btn {
    font-size: .72rem;
  }

  @media (max-width: 1199.98px) {
    .permissions-page .permissions-toolbar {
      grid-template-columns: 1fr 1fr 1fr;
    }
    .permissions-page .permissions-toolbar-search {
      grid-column: span 3;
    }
  }

  @media (max-width: 767.98px) {
    .permissions-page .permissions-toolbar {
      grid-template-columns: 1fr 1fr;
    }
    .permissions-page .permissions-toolbar-search {
      grid-column: span 2;
    }
  }
</style>
@endsection
@section('content')
@php
  $permissionCount = $permissionCount ?? $permissions->total();
  $permissionGroups = $permissionGroups ?? 0;
  $recentPermissions = $recentPermissions ?? 0;
  $presets = [
    [
      'label' => 'Stores module',
      'items' => ['stores-list', 'stores-process', 'stores-create', 'stores-edit', 'stores-delete'],
    ],
    [
      'label' => 'Materials catalogue',
      'items' => ['material-list', 'material-create', 'material-edit', 'material-delete'],
    ],
    [
      'label' => 'Technician requests',
      'items' => ['materials', 'material-request-create'],
    ],
  ];
@endphp
<section class="content workflow-faults-page permissions-page">
    <div class="workspace-summary-grid">
        <div class="workspace-summary-card" style="--summary-color:#6366F1;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-key"></i></span>
                    <div>
                        <div class="workspace-summary-label">Total Permissions</div>
                        <div class="workspace-summary-title">Access catalog</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $permissionCount }}</div>
            </div>
        </div>
        <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-layer-group"></i></span>
                    <div>
                        <div class="workspace-summary-label">Permission Groups</div>
                        <div class="workspace-summary-title">Module prefixes</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $permissionGroups }}</div>
            </div>
        </div>
        <div class="workspace-summary-card" style="--summary-color:#10B981;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-sparkles"></i></span>
                    <div>
                        <div class="workspace-summary-label">Recent Additions</div>
                        <div class="workspace-summary-title">Last 30 days</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $recentPermissions }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Permission Directory</h3>
                <div class="page-lead">Search and review the full list of available permissions. Use the buttons on the right to add one or many permissions in one go.</div>
            </div>
            <div class="card-tools d-flex flex-wrap gap-2">
                <span class="record-chip"><i class="fas fa-key"></i> {{ $permissionCount }} total records</span>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#singlePermModal">
                    <i class="fas fa-plus me-1"></i>New Permission
                </button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#bulkPermModal">
                    <i class="fas fa-layer-group me-1"></i>Bulk Add Multiple
                </button>
            </div>
        </div>

        <div class="faults-toolbar">
            <form method="GET" action="{{ route('permission.index') }}" class="filter-toolbar permissions-toolbar">
                <div class="faults-toolbar-field">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <select class="form-select" name="per_page" aria-label="Rows per page" onchange="this.form.submit()">
                            <option value="10" {{ $perPage==10?'selected':'' }}>Show 10</option>
                            <option value="20" {{ $perPage==20?'selected':'' }}>Show 20</option>
                            <option value="50" {{ $perPage==50?'selected':'' }}>Show 50</option>
                            <option value="100" {{ $perPage==100?'selected':'' }}>Show 100</option>
                            <option value="200" {{ $perPage==200?'selected':'' }}>Show 200</option>
                        </select>
                    </div>
                </div>

                @php
                  $groups = \Spatie\Permission\Models\Permission::all()->groupBy(fn($p)=> explode('-',$p->name)[0] ?? 'general')->keys()->sort()->values();
                @endphp
                <div class="faults-toolbar-field">
                    <select class="form-select form-select-sm" id="groupFilter" onchange="document.getElementById('searchInput').value = this.value; this.form.submit();">
                        <option value="">All groups</option>
                        @foreach($groups as $g)
                          <option value="{{ $g }}" @if($q===$g) selected @endif>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="permissions-toolbar-search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" name="q" class="form-control" placeholder="Search permission name or module prefix" value="{{ $q ?? '' }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-search me-1"></i>Search
                </button>
                <a href="{{ route('permission.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-rotate-left me-1"></i>Reset
                </a>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle impaza-table faults-table">
                    <thead>
                        <tr>
                            <th style="width:8%">#</th>
                            <th>Permission</th>
                            <th style="width:12%">Guard</th>
                            <th style="width:22%">Date</th>
                            <th style="width:10%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $idx => $permission )
                        <tr>
                            <td><span class="age-ticker">#{{ $permission->id }}</span></td>
                            <td>
                                <div class="workspace-cell-main fw-medium">{{ $permission->name }}</div>
                                <div class="workspace-cell-sub">{{ ucfirst(explode('-', $permission->name)[0] ?? 'General') }} module permission</div>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace small">{{ $permission->guard_name ?? 'web' }}</span></td>
                            <td>
                                <div class="workspace-cell-main">{{ optional($permission->created_at)->format('d M Y') }}</div>
                                <div class="workspace-cell-sub">{{ optional($permission->created_at)->format('h:i a') }}</div>
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editPermModal-{{ $permission->id }}" title="Edit permission">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <form action="{{ route('permission.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('Delete permission &quot;{{ $permission->name }}&quot;? This will detach it from all roles/users.');" class="d-inline ms-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Delete permission">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No permissions match your filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="faults-table-footer">
                <small class="text-muted">Showing {{ $permissions->count() }} of {{ $permissions->total() }} permissions</small>
                <div>{{ $permissions->appends(['q' => $q ?? '', 'per_page' => $perPage])->links() }}</div>
            </div>
        </div>
    </div>
</section>

{{-- Single permission modal --}}
<div class="modal custom-modal fade" id="singlePermModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="singlePermModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('permission.store') }}">
        @csrf
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0" id="singlePermModalLabel"><i class="fas fa-plus-circle me-2 text-primary"></i>New Permission</h5>
            <div class="small text-muted mt-1">Add one permission to the access catalog.</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Permission Name *</label>
            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. material-request-create" value="{{ old('name') }}" required>
            <div class="form-text small">Use the pattern <code class="font-monospace">[module]-[action]</code> — e.g. <code>stores-process</code>, <code>material-edit</code>.</div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Guard</label>
            <select name="guard_name" class="form-select form-select-sm">
              <option value="web" selected>web (default)</option>
              <option value="api">api</option>
            </select>
          </div>
          <div>
            <div class="small fw-semibold mb-2 text-muted">Quick templates</div>
            <div class="d-flex flex-wrap gap-2">
              @foreach(['material-list','material-create','material-edit','material-delete','stores-list','stores-process','materials'] as $tpl)
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill perm-fill" data-name="{{ $tpl }}">{{ $tpl }}</button>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fas fa-save me-1"></i>Create Permission</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Bulk / Multiple permissions modal --}}
<div class="modal custom-modal fade" id="bulkPermModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="bulkPermModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('permission.storeBulk') }}" id="bulkPermForm">
        @csrf
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0" id="bulkPermModalLabel"><i class="fas fa-layer-group me-2 text-primary"></i>Bulk Add Multiple Permissions</h5>
            <div class="small text-muted mt-1">Create many permissions at once. Add or remove rows and type the permission names.</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3 p-3 bg-light border rounded-3">
            <div class="small fw-semibold mb-2"><i class="fas fa-lightbulb text-warning me-1"></i>Quick-fill preset bundles</div>
            <div class="row g-2 perm-row-preset">
              @foreach($presets as $idx => $p)
              <div class="col-md-4">
                <div class="small text-muted mb-1">{{ $p['label'] }}</div>
                <div class="d-flex flex-wrap gap-1">
                  <button type="button" class="btn btn-outline-primary rounded-pill perm-preset" data-items="{{ implode(',', $p['items']) }}">
                    <i class="fas fa-plus"></i> Add all ({{ count($p['items']) }})
                  </button>
                </div>
                <div class="small text-muted mt-1" style="word-break: break-all;">
                  {{ implode(', ', $p['items']) }}
                </div>
              </div>
              @endforeach
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-semibold mb-0"><i class="fas fa-list-check me-1 text-secondary"></i>Permission Rows</label>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addPermRowBtn">
              <i class="fas fa-plus me-1"></i>Add Row
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" id="bulkPermTable">
              <thead class="table-light">
                <tr>
                  <th style="width:5%">#</th>
                  <th style="width:55%">Permission Name *</th>
                  <th style="width:20%">Guard</th>
                  <th style="width:20%" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="bulkPermBody">
                @for($i = 0; $i < 5; $i++)
                <tr class="bulk-row" data-idx="{{ $i }}">
                  <td class="text-center row-num">{{ $i + 1 }}</td>
                  <td>
                    <input type="text" name="permissions[{{ $i }}][name]" class="form-control form-control-sm bulk-name" placeholder="e.g. material-create" value="{{ old('permissions.'.$i.'.name') }}">
                  </td>
                  <td>
                    <select name="permissions[{{ $i }}][guard_name]" class="form-select form-select-sm">
                      <option value="web" selected>web</option>
                      <option value="api">api</option>
                    </select>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill remove-row" @if($i===0) disabled @endif>
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                @endfor
              </tbody>
            </table>
          </div>
          <div class="form-text small mt-2">Note: existing permission names are skipped automatically (no duplicates).</div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fas fa-save me-1"></i>Create Permissions</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit permission modals (one per row, safe for paginated slice) --}}
@foreach($permissions as $permission)
    @include('permission.edit_modal', ['permission' => $permission])
@endforeach

@endsection

@section('scripts')
@include('partials.scripts')
<script>
(function () {
  // Single modal quick-fill templates
  document.querySelectorAll('.perm-fill').forEach(btn => {
    btn.addEventListener('click', function () {
      const input = document.querySelector('#singlePermModal input[name="name"]');
      if (input) {
        input.value = btn.dataset.name;
        input.focus();
      }
    });
  });

  // Bulk modal: row management
  const body = document.getElementById('bulkPermBody');
  const addBtn = document.getElementById('addPermRowBtn');
  let idx = body ? body.querySelectorAll('tr.bulk-row').length : 0;

  function renumber() {
    if (!body) return;
    body.querySelectorAll('tr.bulk-row').forEach((tr, i) => {
      tr.dataset.idx = i;
      const rn = tr.querySelector('.row-num');
      if (rn) rn.textContent = i + 1;
      tr.querySelectorAll('[name^="permissions["]').forEach(el => {
        el.name = el.name.replace(/permissions\[\d+\]/, 'permissions[' + i + ']');
      });
      const rem = tr.querySelector('.remove-row');
      if (rem) rem.disabled = (i === 0);
    });
    idx = body.querySelectorAll('tr.bulk-row').length;
  }

  function addRow(defaultName = '') {
    if (!body) return;
    const i = idx++;
    const tr = document.createElement('tr');
    tr.className = 'bulk-row';
    tr.dataset.idx = i;
    tr.innerHTML = `
      <td class="text-center row-num">${i + 1}</td>
      <td>
        <input type="text" name="permissions[${i}][name]" class="form-control form-control-sm bulk-name" placeholder="e.g. stores-process" value="${defaultName.replace(/"/g,'&quot;')}">
      </td>
      <td>
        <select name="permissions[${i}][guard_name]" class="form-select form-select-sm">
          <option value="web" selected>web</option>
          <option value="api">api</option>
        </select>
      </td>
      <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill remove-row"><i class="fas fa-trash"></i></button>
      </td>`;
    body.appendChild(tr);
    renumber();
  }

  if (addBtn) addBtn.addEventListener('click', () => addRow(''));

  document.addEventListener('click', function (e) {
    const t = e.target.closest('.remove-row');
    if (t && body && body.contains(t)) {
      t.closest('tr.bulk-row').remove();
      renumber();
    }
  });

  // Presets: append all items into the row list, reusing existing rows first
  document.querySelectorAll('.perm-preset').forEach(btn => {
    btn.addEventListener('click', function () {
      const items = (btn.dataset.items || '').split(',').map(s => s.trim()).filter(Boolean);
      if (!items.length) return;
      const existing = body.querySelectorAll('tr.bulk-row');
      let placed = 0;
      existing.forEach(row => {
        const input = row.querySelector('.bulk-name');
        if (input && !input.value.trim() && placed < items.length) {
          input.value = items[placed++];
        }
      });
      while (placed < items.length) addRow(items[placed++]);
    });
  });

  // Validate bulk form: require at least 1 non-empty name before submit
  const bulkForm = document.getElementById('bulkPermForm');
  if (bulkForm) {
    bulkForm.addEventListener('submit', function (e) {
      let count = 0;
      bulkForm.querySelectorAll('.bulk-name').forEach(i => { if (i.value.trim()) count++; });
      if (count === 0) {
        e.preventDefault();
        alert('Please type at least one permission name.');
        return false;
      }
      return true;
    });
  }
})();
</script>
@endsection
