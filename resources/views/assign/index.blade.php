@extends('layouts.admin')

@section('title')
Assign Faults
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Assign Faults')}}</h3>
        <div class="card-tools">
            @can('fault-create')
                <a  class="btn btn-primary btn-sm" href="{{ route('faults.create') }}"><i class="fas fa-plus-circle"></i>{{_('Log Fault')}} </a>
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
                    <th>Contact Name</th>
                    <th>Account Manager</th>
                    <th>Link Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faults as $fault)
                 <tr >
                    <td>{{ ++$i }}</td>
                    <td>{{ $fault->customer }}</td>
                    <td>{{ $fault->contactName }}</td>
                    <td>{{ $fault->accountManager }}</td>
                    <td>{{ $fault->link }}</td>
                    <td>
                        @can('fault-assessment')
                            <a href="{{ route('assessments.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Assess</a>
                        @endcan
                        @can('rectify-fault')
                            <a href="{{ route('rectify.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Rectify</a>
                        @endcan
                        @can('assign-fault')
                        <a href="{{ route('assign.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Assign</a>
                        @endcan
                        @can('re-assign-fault')
                         <a href="{{ route('faults.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Re-Assign</a>   
                        @endcan
                        @can('request-permit')
                        <a href="{{ route('request-permit.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Request Permit</a>                            
                        @endcan
                        @can('fault-edit')
                        <a href="{{ route('faults.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Edit</a>
                        @endcan
                        <a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                        </div>
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
