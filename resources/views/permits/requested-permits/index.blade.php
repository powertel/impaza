@extends('layouts.admin')

@section('title')
Requested Permits
@endsection
@include('partials.css')
@section('content')
   
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Requested Permits')}}</h3>
        <div class="card-tools">
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fault ID</th>
                    <td>PTW Numnber</td>
                    <th>Customer Name</th>
                    <th>Date of Request</th>
                    <th>Technician</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <tr>
                <td>1</td>
                <td>653276</td>
                <td>1213313</td>
                <th>Zb Bank</th>
                <th>05/05/43</th>
                <th>Freedom</th>
                <td>
                    <a href="">Delete</a>
                </td>
            </tr>

            </tbody> 
        </table>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
@section('scripts')
    @include('partials.scripts')
@endsection
