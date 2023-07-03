@extends('layouts.admin')

@section('title')
Stores
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Stores')}}</h3>
        <div class="card-tools">
            @can('material')
                <a  class="btn btn-primary btn-sm" href="{{ route('materials') }}"><i class="fas fa-plus-circle"></i> </a>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped" id="material" style="font-size:14px">
            <thead>
                <tr>
                    <th>Fault Ref. No.</th>
                    <th>Fault Type</th>
                    <th>SAP Ref. No.</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stores as $store)
                 <tr >
                    <td>{{$store->fault_ref_number}}</td>
                    <td>{{$store->faultType }}</td>
                    <td>{{$store->SAP_ref}}</td>
                    <td  style=" background-color: {{ App\Models\StoreStatus::STATUS_COLOR[ $store->store_status ] ?? 'none' }};">
                        <strong>{{ $store->store_status}}</strong>
                     </td>
                    <td>
                        <form  style="margin-block-end: 0px;" action="{{ route('deny',$store->id) }}" method="POST">
                            <a href="{{ route('stores.show',$store->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:0px 2px; color:#fff;">Deny</button>
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
