@extends('layouts.admin')

@section('title')
Department Faults
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Department Faults')}}</h3>
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
                    <th>Account Manager</th>
                    <th>Link Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $faults as $fault )
                 <tr >
                    <td>{{ ++$i }}</td>
                    <td>{{ $fault->customer }}</td>
                    <td>{{ $fault->accountManager }}</td>
                    <td>{{ $fault->link }}</td>
                    <td style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? 'none' }};">
                        <strong>{{$fault->description}}</strong> 
                    </td>
                    <td>
                        @can('fault-assessment')
                            <a href="{{ route('assessments.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Assess</a>
                        @endcan
                        @can('assign-fault')
                        <a href="{{ route('assign.edit',$fault->id) }}" class="btn btn-sm btn-primary" style="padding:0px 2px; color:#fff;" >Assign</a>
                        @endcan
                        @can('re-assign-fault')
                         <a href="{{ route('faults.edit',$fault->id) }}" class="btn btn-sm btn-warning" style="padding:0px 2px; color:#fff;" >Re-Assign</a>   
                        @endcan
                        <a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                    </td>
                </tr>
                @endforeach
            </tbody> 
        </table>
    </div>
    <!-- /.card-body -->
</div>
 
{{-- {{$section->section}}

@foreach ($section -> faults as $fault )

<span>{{$fault->contactName}}</span>
    
@endforeach --}}
</section>
@endsection
