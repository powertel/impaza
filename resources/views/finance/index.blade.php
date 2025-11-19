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
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
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
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Contract Number</th>
                        <th>City/Town</th>
                        <th>link</th>
                        <th>Status</th>
                        <th>Action(s)</th>
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
                            <div class="btn-group">
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i> Actions
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                  <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeViewModal-{{ $link->id }}" title="View">
                                      <i class="fas fa-eye text-success"></i>
                                      <span>View</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeEditModal-{{ $link->id }}" title="Edit">
                                      <i class="fas fa-edit text-primary"></i>
                                      <span>Edit</span>
                                    </a>
                                  </li>
                                  @if ($link->link_status==='Pending')
                                    <li>
                                      <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeEditModal-{{ $link->id }}" title="Approve">
                                        <i class="fas fa-check text-primary"></i>
                                        <span>Approve</span>
                                      </a>
                                    </li>
                                  @endif
                                  <li><hr class="dropdown-divider"></li>
                                  @if ($link->link_status==='Connected')
                                    <li>
                                      <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeDisconnectModal-{{ $link->id }}" title="Disconnect">
                                        <i class="fas fa-unlink text-warning"></i>
                                        <span class="text-warning">Disconnect</span>
                                      </a>
                                    </li>
                                  @endif
                                  @if ($link->link_status==='Disconnected')
                                    <li>
                                      <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeReconnectModal-{{ $link->id }}" title="Reconnect">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect</span>
                                      </a>
                                    </li>
                                  @endif
                                  <li>
                                    <form action="{{ route('decommission',$link->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_decommission" title="Decommission">
                                        <i class="fas fa-ban text-danger"></i>
                                        <span class="text-danger">Decommission</span>
                                      </button>
                                    </form>
                                  </li>
                              </ul>
                            </div>
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

