@extends('layouts.admin')

@section('title')
links
@endsection
@include('partials.css')
@section('content')
<section class="content">
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Links')}}</h3>
        <div class="card-tools">
            @can('link-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('links.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Link')}} </a>
            @endcan
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <label for="linksPageSize" class="mb-0 small text-muted">Show</label>
            <select id="linksPageSize" class="form-select form-select-sm" style="width:auto;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>
            <input id="linksSearch" type="search" class="form-control form-control-sm" placeholder="Search..." style="max-width:240px;">
        </div>
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#linksPageSize" data-pager="#linksPager" data-search="#linksSearch">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>City/Town</th>
                    <th>Location</th>
                    <th>Pop</th>
                    <th>link</th>
                    <th>Actions</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($links as $link)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $link->customer}}</td>
                    <td>{{ $link->city}}</td>
                    <td>{{ $link->suburb}}</td>
                    <td>{{ $link->pop}}</td>
                    <td>{{ $link->link}}</td>

                    <td>
                    <form  name="theForm" action="{{ route('links.destroy',$link->id) }}" method="POST">
                        <a href="{{ route('links.show',$link->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                        @can('account-manager-edit')
                        <a href="{{ route('links.edit',$link->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        @endcan    
                        @csrf
                        @method('DELETE')
                        @can('link-delete') 
                        <button type="button" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="padding:0px 2px; color:#fff;">Delete</button>                                                               
                      @endcan
                    </form>  
                    </td>
                </tr>
                @endforeach
            </tbody>  
        </table>
        <div id="linksPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection






                          
