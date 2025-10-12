@extends('layouts.admin')

@section('title')
Assess Faults
@endsection

@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Assess Faults')}}</h3>
        <div class="card-tools">
            <input id="assessmentsSearch" type="text" class="form-control form-control-sm d-inline-block w-auto" placeholder="Search">
            <select id="assessmentsPageSize" class="form-control form-control-sm d-inline-block w-auto">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#assessmentsPageSize" data-pager="#assessmentsPager" data-search="#assessmentsSearch">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>Contact Name</th>
                    <th>Account Manager</th>
                    <th>Link Name</th>
                    <th>Status</th>
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
                    <td style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? 'none' }};">
                       <strong>{{$fault->description}}</strong> 
                    </td>
                    <td>
                        @can('fault-assessment')
                            <a href="{{ route('assessments.edit',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Assess</a>
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
        <div id="assessmentsPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
