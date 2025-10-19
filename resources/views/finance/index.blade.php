@extends('layouts.admin')

@section('title')
Finance
@endsection
@include('partials.css')
@section('content')
<section class="content">
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Links</h3>
        <div class="card-tools">
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="financePageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="financeSearch" class="form-control" placeholder="Search Links">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#financePageSize" data-pager="#financePager" data-search="#financeSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Contract Number</th>
                        <th>City/Town</th>
                        <th>link</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($finance_links as $link)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $link->customer}}</td>
                        <td>{{$link->contract_number}}</td>
                        <td>{{ $link->city}}</td>
                        <td>{{ $link->link}}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\LinkStatus::STATUS_COLOR[ $link->link_status ] ?? '#6c757d' }}; color: #0d0c0cff; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$link->link_status}}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#financeViewModal-{{ $link->id }}">
                               <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#financeEditModal-{{ $link->id }}">
                               <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            @if ($link->link_status==='Pending')
                            @can('finance-link-update')
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#financeEditModal-{{ $link->id }}">
                                   <i class="fas fa-save me-1"></i> Approve
                                </button>
                            @endcan
                            @elseif ($link->link_status==='Connected')
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#financeDisconnectModal-{{ $link->id }}">
                                    <i class="fas fa-pencil me-1"></i> Disconnect
                                </button>
                            @elseif ($link->link_status==='Disconnected')
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#financeReconnectModal-{{ $link->id }}">
                                    <i class="fas fa-save me-1"></i> Reconnect
                                </button>
                            @else
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>  
            </table>
            <div id="financePager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>

@foreach ($finance_links as $link)
    @include('finance.view_modal', ['link' => $link])
    @include('finance.edit_modal', ['link' => $link])
    @if ($link->link_status==='Connected')
        @include('finance.disconnect_modal', ['link' => $link])
    @endif
    @if ($link->link_status==='Disconnected')
        @include('finance.reconnect_modal', ['link' => $link])
    @endif
@endforeach

</section>
@endsection

