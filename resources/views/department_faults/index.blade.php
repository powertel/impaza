@extends('layouts.admin')

@section('title')
Department Faults
@endsection

@section('content')
@include('partials.css')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Department Faults')}}</h3>
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
                    <th>Contact Name</th>
                    <th>Accounnt Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $faults as $fault )
                 <tr >
                    <td>{{ ++$i }}</td>
                    <td>{{ $fault->contactName }}</td>
                    <td>{{ $fault->accountManager }}</td>
                    <td>
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
 
{{-- {{$section->section}}

@foreach ($section -> faults as $fault )

<span>{{$fault->contactName}}</span>
    
@endforeach --}}
</section>
@endsection
