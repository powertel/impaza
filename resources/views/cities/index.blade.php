@extends('layouts.admin')

@section('title')
Cities
@endsection

@section('content')
@include('partials.css')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Cities')}}</h3>
        <div class="card-tools">
            @can('city-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('cities.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create City/Town')}} </a>
            @endcan
            @can('location-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('locations.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create location')}} </a>
            @endcan
            
           
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>City/Town</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cities as $city)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $city->city}}</td>
                    <td>
                        <form action="{{ route('cities.destroy',$city->id) }}" method="POST">
                            <a href="{{ route('cities.show',$city->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                            @can('city-edit')
                            <a href="{{ route('cities.edit',$city->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                            @endcan

                            @csrf
                            @method('DELETE')
                            @can('city-delete')
                            <button type="submit" class="btn btn-danger btn-sm " style="padding:0px 2px; color:#fff;">Delete</button>
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
