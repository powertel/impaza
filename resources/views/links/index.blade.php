@extends('layouts.admin')

@section('title')
links
@endsection

@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title" style="text-transform: uppercase; font-family: Times New Roman, Times, serif;">{{_('Links')}}</h3>
        <div class="card-tools">
            <a  class="btn btn-primary" href="{{ route('links.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Link')}} </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer Name</th>
                    <th>link Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($links as $link)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $link->customerName}}</td>
                    <td>{{ $link->linkName}}</td>
                    <td>
                        <a href="{{ route('links.edit',$link->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        <a href="{{ route('links.show',$link->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
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
