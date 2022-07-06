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
            <a  class="btn btn-primary btn-sm" href="{{ route('cities.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create City/Town')}} </a>
            <a  class="btn btn-primary btn-sm" href="{{ route('locations.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create location')}} </a>
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
                        <a href="{{ route('cities.edit',$city->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        <a href="{{ route('cities.show',$city->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
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
