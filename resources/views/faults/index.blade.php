@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('content')
<section class="content">

    <div class="container-fluid">
        <div class="card-header">
            <h3 class="card-title">{{_('Faults')}}</h3>
            <div class="float-end">
            <a class="btn btn-success" href="{{ route('faults.create') }}">{{_('New Fault')}} </a>
            </div>
        </div>
        <div class="card body">
        <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fault Id</th>
                                <th>Customer Name</th>
                                <th>Account Manager</th>
                                <th>Link Name</th>
                                <th>Actions</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr *ngFor="let fault of faults">
                                <td>1</td>
                                <td>ZB Bank</td>
                                <td>Freedom</td>
                                <td>ZB_ANGWA_ZB</td>
                                <td class="table-action" style="width: 90px;">
                                    <div class="dropdown">
                                        <a href="#"  data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item-->
                                            <a href="{{ route('faults.create') }}" class="dropdown-item" >Assess</a>
                                            <a href="{{ route('faults.create') }}" class="dropdown-item" >View</a>
                                            <!-- item-->
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div> <!-- end table-responsive-->
        </div>
    </div>
<!--     <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Details</th>
                    <th width="280px">Action</th>
                </tr>
            </thead>   
            <tbody>     
                <tr>
                    <td>Hello</td>
                    <td>Hello</td>
                    <td>Hello</td>
                    <td>
                        <form  method="POST">
                            <a class="btn btn-info" >Show</a>
                            <a class="btn btn-primary" >Edit</a>
                        </form>
                    </td>
                </tr>
            </tbody>
            </table> -->

</section>
@endsection
