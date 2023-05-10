@extends('layouts.admin')

@section('title')
links
@endsection
@include('partials.css')
@section('content')
<section class="content">
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Links')}}</h3>
        <div class="card-tools">
            @can('link-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('links.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Link')}} </a>
            @endcan
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>City/Town</th>
                    <th>Location</th>
                    <th>Pop</th>
                    <th>link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($links as $link)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $link->customer}}</td>
                    <td>{{ $link->city}}</td>
                    <td>{{ $link->suburb}}</td>
                    <td>{{ $link->pop}}</td>
                    <td>{{ $link->link}}</td>
                    <td>
                    <form action="{{ route('links.destroy',$link->id) }}" method="POST">
                        <a href="{{ route('links.show',$link->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                        @can('account-manager-edit')
                        <a href="{{ route('links.edit',$link->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        @endcan

                        @csrf
                        @method('DELETE')
                        @can('link-delete')
                        <button type="submit" class="btn btn-danger btn-sm" style="padding:0px 2px; color:#fff;">Delete</button>
                                                                                   
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
