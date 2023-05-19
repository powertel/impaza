@extends('layouts.admin')

@section('title')
Faults
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Faults')}}</h3>
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
                   <!-- <th>No.</th>-->
                   <!-- <th>fault No.</th>-->
					<th>Ref. No.</th>
                    <th>Customer</th>
                    <th>Account Manager</th>
                    <th>Link</th>
                    <th>Assigned To</th>
                    <th>Date Reported</th>
					<th>Logged By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faults as $fault)
                 <tr >
                    <!--<td>{{ ++$i }}</td>-->
                    <!--<td>{{$fault->id}}</td>-->
					<td>{{$fault->fault_ref_number}}</td>
                    <td>{{ $fault->customer }}</td>
                    <td>{{ $fault->accountManager }}</td>
                    <td>{{ $fault->link }}</td>
                    <td>{{$fault->name}}</td>
                    <th>{{$fault->created_at}}</th>
					<td> {{ auth()->user()->name }}</td>
                    <td style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? 'none' }};">
                       <strong>{{$fault->description}}</strong> 
                    </td>
                    <td>
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
