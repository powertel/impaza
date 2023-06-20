@extends('layouts.admin')

@section('title')
Departments
@endsection
@include('partials.css')
@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">{{_('Fault Resolution Report')}}</h3>
        <div class="card-tools">
            @can('department-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('departments.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New Department')}} </a>
            <a  class="btn btn-primary btn-sm" href="{{ route('sections.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New Section')}} </a>
            <a  class="btn btn-primary btn-sm" href="{{ route('positions.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New Position')}} </a>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-borderless">
            <thead>
                <tr>
                    <th scope="col">Scope</th>
                    <th scope="col">Total faults</th>
                    <th scope="col">Resolved</th>
                    <th scope="col">Outstanding</th>
                    <th scope="col">Resolution Ratio</th>
                    <th scope="col">TDT for RF</th>
                    <th scope="col">Clearance ratio</th>
                </tr>
            </thead>
            <tbody>
                <tr >
                    <th scope="row">GLOBAL</th>
                    <td> {{$global_count}}</td>
                    <td> {{$Resolved_count}}</td>
                    <td>{{$Outstanding = $global_count - $Resolved_count}}</td>
                    @if($global_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$Resolved_count / $global_count}}</td>
                    <td>{{$TD_global}}</td>
                    <td>{{$Resolved_count / $TD_global }}</td>
                    @endif
                </tr>
                <tr >
                    <th scope="row">NOC</th>
                    <td> {{$NOC_count}}</td>
                    <td> {{$ResolvedNOC_count}}</td>
                    <td>{{$NOC_count - $ResolvedNOC_count}}</td>
                    @if($NOC_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$ResolvedNOC_count / $NOC_count}}</td>
                    <td>{{$TD_NOC}}</td>
                    <td>{{$ResolvedNOC_count / $TD_NOC }}</td>
                    @endif
                </tr>
                <tr >   
                    <th scope="row">HRE</th>
                    <td>{{$HRE_count}}</td>
                    <td> {{$ResolvedHRE_count}}</td>
                    <td>{{$HRE_count - $ResolvedHRE_count}}</td>
                    @if($HRE_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$ResolvedHRE_count / $HRE_count}}</td>
                    <td>{{$TD_HRE}}</td>
                    <td>{{$ResolvedHRE_count / $TD_HRE }}</td>
                    @endif
                    
                </tr>
                <tr >
                    <th scope="row">BYO</th>
                    <td> {{$BYO_count}}</td>
                    <td> {{$ResolvedBYO_count}}</td>
                    <td>{{$BYO_count- $ResolvedBYO_count}}</td>
                    @if($BYO_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$ResolvedBYO_count / $BYO_count}}</td>
                    <td>{{$TD_BYO}}</td>
                    <td>{{$ResolvedBYO_count / $TD_BYO}}</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection
