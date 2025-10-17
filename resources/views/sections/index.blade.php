
@extends('layouts.admin')

@section('title')
Sections
@endsection

@include('partials.css')

@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">Sections</h3>
        <div class="card-tools">
            @can('department-create')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sectionCreateModal"><i class="fas fa-plus-circle"></i>Create Section(s) </button>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="sectionsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="sectionsSearch" class="form-control" placeholder="Search Sections">
                </div>
            </div>

            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#sectionsPageSize" data-pager="#sectionsPager" data-search="#sectionsSearch">
                <thead class="thead-light">
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
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sectionEditModal{{ $section->id }}" style="padding:0px 2px;">
                                <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="sectionsPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>


</section>

@include('sections.create_modal')
@include('sections.edit_modal')
 
@endsection

@section('scripts')
  @include('partials.scripts')
@endsection

