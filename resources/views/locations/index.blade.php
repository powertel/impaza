@extends('layouts.admin')

@section('title')
Locations
@endsection

@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Locations')}}</h3>
        <div class="card-tools">
            <a  class="btn btn-primary btn-sm" href="{{ route('locations.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create location')}} </a>
            <a  class="btn btn-primary btn-sm" href="{{ route('pops.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Pop')}} </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>City</th>
                    <th>location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($locations as $location)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $location->city}}</td>
                    <td>{{ $location->suburb}}</td>
                    <td>
                        <a href="{{ route('locations.edit',$location->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        <a href="{{ route('locations.show',$location->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
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
@section('scripts')
    @include('partials.scripts')
@endsection
