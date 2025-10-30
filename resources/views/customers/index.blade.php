@extends('layouts.admin')

@section('title')
Customers
@endsection

@include('partials.css')

@section('content')

<section class="content">

<div class="card">
    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Customers</h3>
        <div class="card-tools">
            @can('customer-create')
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#customerCreateModal"><i class="fas fa-plus-circle"></i> Create Customer(s) </button>
            @endcan            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    @php $perPage = request('per_page', 20); @endphp
                    <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    <select id="customersPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('customers.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 360px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search all records">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('customers.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
            <table  class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Account Manager</th>
                        <th>Account Number</th>
                          <th>Address</th>
                          <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customers->firstItem() + $loop->index }}</td>
                        <td>{{ $customer->customer}}</td>
                        <td>{{ $customer->accountManager }}</td>
                        <td>{{ $customer->account_number }}</td>
                         <td>{{ $customer->address ?? '' }}</td>
                        <td>{{ $customer->contact_number ?? '' }}</td>
                        <td>
                            <button type="button" class="btn  btn-outline-success"  data-bs-toggle="modal" data-bs-target="#customerViewModal{{ $customer->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            @can('customer-edit')
                            <button type="button" class="btn  btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#customerEditModal{{ $customer->id }}">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            @endcan
                            @can('customer-delete')
                            <button type="button" class="btn btn-outline-danger "  data-bs-toggle="modal" data-bs-target="#customerDeleteModal{{ $customer->id }}">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button> 
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
              </small>
              {{ $customers->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>

            @include('customers.create_modal')
            @include('customers.edit_modal')
            @include('customers.view_modal')
            @include('customers.delete_modal')
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection

@section('scripts')
<script>
  document.getElementById('customersPageSize')?.addEventListener('change', function(){
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', this.value);
    params.delete('page');
    window.location.search = params.toString();
  });
</script>
@endsection



