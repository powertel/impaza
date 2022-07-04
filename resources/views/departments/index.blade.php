@extends('layouts.admin')

@section('title')
Departments
@endsection

@section('content')
@include('partials.css')
<section class="content" >

    <div class="card" >
        <div class="card-header">
            <h3 class="card-title">{{_('Departments')}}</h3>
            <div class="card-tools">
                <a  class="btn btn-primary btn-sm" href="{{ route('departments.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New Department')}} </a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
        <!-- /.card-header -->
        <div class="card-body">
            <table  class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th scope="col">Department</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $department->department}}</td>
                        <td>
                            <form  action="{{ route('departments.destroy',$department->id) }}"  method="POST">
                                <a href="{{ route('departments.edit',$department->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Edit</a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;">Delete</button>
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
