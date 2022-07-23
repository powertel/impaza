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
                    <th>No.</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $department)
                <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $department->department}}</td>
                    <td>
                        <form  action="{{ route('departments.destroy',$department->id) }}"  method="POST">
                            @can('department-edit')
                            <a href="{{ route('departments.edit',$department->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Edit</a>
                            @endcan
                            
                            @csrf
                            @method('DELETE')
                            @can('department-delete')
                            <button type="submit" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;">Delete</button>
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
