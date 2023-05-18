
@extends('layouts.admin')

@section('title')
Sections
@endsection
@include('partials.css')
@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">{{_('Sections')}}</h3>
        <div class="card-tools">
            @can('department-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('sections.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New Section')}} </a>
            <a  class="btn btn-primary btn-sm" href="{{ route('positions.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New Position')}} </a>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Sections</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sections as $section)
                <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $section->section}}</td>
                    <td>
                            @can('department-edit')
                            <a href="{{ route('sections.edit',$section->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Edit</a>
                            @endcan
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
