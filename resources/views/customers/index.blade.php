@extends('layouts.admin')

@section('title')
Customers
@endsection

@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title" style="text-transform: uppercase; font-family: Times New Roman, Times, serif;">{{_('Customers')}}</h3>
        <div class="card-tools">
            <a  class="btn btn-primary" href="{{ route('customers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Customer')}} </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                 <tr >
                    <td>1</td>
                    <td>CBZ</td>
                    <td>
                                <a  class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                                <a  class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                        </div>
                    </td>
                </tr>
            </tbody> 
        </table>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
