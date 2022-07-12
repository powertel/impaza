@extends('layouts.admin')

@section('title')
Customers
@endsection

@section('content')
@include('partials.css')
<section class="content">

<div class="card">
    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Customers')}}</h3>
        <div class="card-tools">
            @can('customer-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('customers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Customer')}} </a>
            @endcan
            @can('link-create')
                <a  class="btn btn-primary btn-sm" href="{{ route('links.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Link')}} </a>
            @endcan
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                 <tr>
                    <td>{{++$i}}</td>
                    <td>{{ $customer->customer}}</td>
                    <td>
                    <form action="{{ route('customers.destroy',$customer->id) }}" method="POST">
                    <a href="{{ route('customers.show',$customer->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                        @can('account-manager-edit')
                        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        @endcan

                        @csrf
                        @method('DELETE')
                        @can('customer-delete')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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
