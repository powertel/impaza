@extends('layouts.admin')

@section('title')
Customers
@endsection

@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title" style="text-transform: uppercase; font-family: Times New Roman, Times, serif;">{{_('Customers')}}</h3>
        <div class="card-tools">
            <a  class="btn btn-primary" href="{{ route('customers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Customer')}} </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>City</th>
                    <th>Location</th>
                    <th>Pop</th>
                    <th>Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $customer->customer}}</td>
                    <td>{{ $customer->city}}</td>
                    <td>{{ $customer->suburb}}</td>
                    <td>{{ $customer->pop}}</td>
                    <td>{{ $customer->link}}</td>
                    <td>
                        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        <a href="{{ route('customers.show',$customer->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
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
