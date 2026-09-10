@extends('layouts.admin')

@section('title')
Materials Inventory
@endsection
@include('partials.css')
@section('content')

<section class="content workflow-faults-page">
<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title"><i class="fas fa-warehouse me-2"></i>Materials Inventory</h3>
            <div class="faults-panel-subtitle">Manage stock items: UTP cable, RJ45 connectors, splice protectors and more.</div>
        </div>
        <div class="faults-panel-actions">
            @can('material-create')
                <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createMaterialModal" id="openCreateMaterialBtn">
                    <i class="fas fa-plus-circle me-1"></i>New Material
                </button>
            @endcan
        </div>
    </div>

    <div class="faults-toolbar">
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#3B82F6;">
                    <div class="stat-card-label">Total SKUs</div>
                    <div class="stat-card-value">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#10B981;">
                    <div class="stat-card-label">Active</div>
                    <div class="stat-card-value">{{ number_format($stats['active']) }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#F59E0B;">
                    <div class="stat-card-label">Low Stock</div>
                    <div class="stat-card-value">{{ number_format($stats['lowStock']) }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#EF4444;">
                    <div class="stat-card-label">Out of Stock</div>
                    <div class="stat-card-value">{{ number_format($stats['outOfStock']) }}</div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('materials.index') }}" class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select class="form-select form-select-sm" name="per_page" aria-label="Rows per page" onchange="this.form.submit()">
                        <option value="10" {{ $perPage==10?'selected':'' }}>10</option>
                        <option value="20" {{ $perPage==20?'selected':'' }}>20</option>
                        <option value="50" {{ $perPage==50?'selected':'' }}>50</option>
                        <option value="100" {{ $perPage==100?'selected':'' }}>100</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field">
                <select class="form-select form-select-sm" name="category" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ $category==$c?'selected':'' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="faults-toolbar-field">
                <select class="form-select form-select-sm" name="stock" onchange="this.form.submit()">
                    <option value="all" {{ $stockFilter=='all'?'selected':'' }}>All Stocks</option>
                    <option value="active" {{ $stockFilter=='active'?'selected':'' }}>Active Only</option>
                    <option value="low" {{ $stockFilter=='low'?'selected':'' }}>Low Stock</option>
                    <option value="out" {{ $stockFilter=='out'?'selected':'' }}>Out of Stock</option>
                </select>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Search name, SKU, category..." value="{{ $q }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
                <i class="fas fa-search me-1"></i>Search
            </button>
            <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
                <i class="fas fa-rotate-left me-1"></i>Reset
            </a>
        </form>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th class="text-end">On Hand</th>
                        <th class="text-end">Reorder Lvl</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $idx => $m)
                    <tr>
                        <td>{{ $materials->firstItem() + $idx }}</td>
                        <td>
                            <div class="fw-medium">{{ $m->name }}</div>
                            @if($m->description)
                                <div class="small text-muted mt-1">{{ Str::limit($m->description, 80) }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $m->sku ?: '—' }}</span></td>
                        <td>
                            @if($m->category)
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border">{{ $m->category }}</span>
                            @else — @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $m->unit }}</span></td>
                        <td class="text-end">
                            <strong class="{{ $m->quantity_on_hand <= 0 ? 'text-danger' : ($m->isLowStock() ? 'text-warning' : 'text-success') }}">
                                {{ number_format($m->quantity_on_hand, 2) }}
                            </strong>
                        </td>
                        <td class="text-end text-muted">{{ number_format($m->reorder_level, 2) }}</td>
                        <td>
                            @if(!$m->is_active)
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border">Inactive</span>
                            @elseif($m->quantity_on_hand <= 0)
                                <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border">Out of Stock</span>
                            @elseif($m->isLowStock())
                                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border">Low Stock</span>
                            @else
                                <span class="badge rounded-pill bg-success-subtle text-success-emphasis border">In Stock</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#viewMaterialModal-{{ $m->id }}" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            @can('material-edit')
                            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editMaterialModal-{{ $m->id }}" title="Edit">
                                <i class="fas fa-pencil"></i>
                            </button>
                            @endcan
                            @can('material-delete')
                            <form action="{{ route('materials.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Delete material: {{ $m->name }}?');" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">No materials found. Add your first item.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing {{ $materials->count() }} of {{ $materials->total() }} items</small>
            <div>{{ $materials->links() }}</div>
        </div>
    </div>
</div>
</section>

@can('material-create')
  @include('materials.create_modal')
@endcan
@foreach($materials as $m)
  @include('materials.view_modal', ['material' => $m])
  @can('material-edit')
    @include('materials.edit_modal', ['material' => $m, 'categories' => $categories])
  @endcan
@endforeach

@endsection

@section('scripts')
  @include('partials.scripts')
<script>
(function () {
  document.querySelectorAll('.mat-edit-submit').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const formId = btn.getAttribute('data-form-id');
      const form = document.getElementById(formId);
      if (!form) return;
      if (!form.reportValidity()) return;
      const nativeSubmit = document.createElement('button');
      nativeSubmit.type = 'submit';
      nativeSubmit.style.display = 'none';
      form.appendChild(nativeSubmit);
      nativeSubmit.click();
      setTimeout(function () { nativeSubmit.remove(); }, 100);
    });
  });
})();
</script>
@endsection
