@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Faults')}}</h3>
        <div class="card-tools">
            <a  class="btn btn-primary" href="{{ route('faults.create') }}"><i class="fas fa-plus-circle"></i>{{_('Add Fault')}} </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
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
                    <td>{{ $fault->contactName }}</td>
                    <td>{{ $fault->accountManager }}</td>
                    <td>{{ $fault->linkName }}</td>
                    <td class="table-action" style="width: 90px;">
                        <div class="dropdown">
                            <a href="#"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('faults.edit',$fault->id) }}" class="dropdown-item btn btn-info" >Assess</a>
                                <a href="{{ route('faults.show',$fault->id) }}" class="dropdown-item" >View</a>
                
                            </div>
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
