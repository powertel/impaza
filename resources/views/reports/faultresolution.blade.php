@extends('layouts.admin')

@section('title')
Departments
@endsection
@include('partials.css')
@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">{{_('Departments')}}</h3>
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
        <table  class="table table-striped">
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
                    <td> {{$global}}</td>
                    <td> {{$Resolved}}</td>
                    <td>{{$Outstanding = $global - $Resolved}}</td>
                    @if($global===0)
                    <td>NULL</td>
                    @else
                    <td>{{$Resolved / $global}}</td>
                    @endif
                </tr>
                <tr >
                    <th scope="row">NOC</th>
                    <td> {{$NOC_count}}</td>
                    <td> {{$ResolvedNOC}}</td>
                    <td>{{$NOC_count - $ResolvedNOC}}</td>
                    @if($NOC_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$ResolvedNOC / $NOC_count}}</td>
                        @endif
                </tr>
                <tr >   
                    <th scope="row">HRE</th>
                    <td>{{$HRE_count}}</td>
                    <td> {{$ResolvedHRE}}</td>
                    <td>{{$HRE_count - $ResolvedHRE}}</td>
                    @if($HRE_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$ResolvedHRE / $HRE_count}}</td>
                    @endif
                    
                </tr>
                <tr >
                    <th scope="row">BYO</th>
                    <td> {{$BYO_count}}</td>
                    <td> {{$ResolvedBYO}}</td>
                    <td>{{$BYO_count- $ResolvedBYO}}</td>
                    @if($BYO_count===0)
                    <td>NULL</td>
                    @else
                    <td>{{$ResolvedBYO / $BYO_count}}</td>
                        @endif
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection
