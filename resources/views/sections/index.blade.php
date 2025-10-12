
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
            <input id="sectionsSearch" type="text" class="form-control form-control-sm d-inline-block w-auto" placeholder="Search">
            <select id="sectionsPageSize" class="form-control form-control-sm d-inline-block w-auto">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#sectionsPageSize" data-pager="#sectionsPager" data-search="#sectionsSearch">
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
        <div id="sectionsPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection
