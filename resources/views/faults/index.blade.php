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
            
                <!-- <a  class="btn btn-primary btn-sm" href="{{ route('faults.create') }}"><i class="fas fa-plus-circle"></i>{{_('Log Fault')}} </a> -->
            
            @can('fault-create')
                <button type="button" class="btn btn-primary btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createFaultModal">
                    <i class="fas fa-plus-circle"></i> Log Fault
                </button>
            @endcan
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped" id="faults-list" style="font-size:14px">
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
                    <td>{{$fault->customer }}</td>
                    <td>{{$fault->accountManager }}</td>
                    <td>{{$fault->link }}</td>
                    <td>{{$fault->assignedTo}}</td>
                    <td>
					{{ Carbon\Carbon::parse($fault->created_at)->format('j F Y h:i a') }}
					</td>
					<td> {{$fault->reportedBy}}</td>
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
        @include('faults.create')
    </div>
    <!-- /.card-body -->
</div>
</section>
@endsection

@section('scripts')
    @include('partials.faults')
@endsection

