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
        <h3 class="card-title">{{_('Links')}}</h3>
        <div class="card-tools">
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>City/Town</th>
                    <th>link</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($finance_links as $link)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $link->customer}}</td>
                    <td>{{ $link->city}}</td>
                    <td>{{ $link->link}}</td>
                    <td  style=" background-color: {{ App\Models\LinkStatus::STATUS_COLOR[ $link->link_status ] ?? 'none' }};">
                       <strong>{{ $link->link_status}}</strong> 
                    </td>
                    <td>
                        <form  style="margin-block-end: 0px;" action="{{ route('finance.destroy',$link->id) }}" method="POST">
                            <a href="{{ route('finance.show',$link->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                            @can('finance-link-update')
                            <a href="{{ route('finance.edit',$link->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Approve</a>
                            @endcan

                            @csrf
                            @method('DELETE')
                            @can('link-delete')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:0px 2px; color:#fff;">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>  
        </table>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
