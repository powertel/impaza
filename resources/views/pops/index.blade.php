@extends('layouts.admin')

@section('title')
Pops
@endsection

@section('content')
    @include('partials.css')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Pops')}}</h3>
        <div class="card-tools">
            @can('pop-create')
              <a  class="btn btn-primary btn-sm" href="{{ route('pops.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Pop')}} </a>  
            @endcan
            @can('customer-create')
                <a  class="btn btn-primary btn-sm" href="{{ route('customers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Customer')}} </a>
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
                    <th>Location</th>
                    <th>Pop</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pops as $pop)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $pop->city}}</td>
                    <td>{{ $pop->suburb}}</td>
                    <td>{{ $pop->pop}}</td>
                    <td>
                        @can('pop-edit')
                           <a href="{{ route('pops.edit',$pop->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a> 
                        @endcan
                        
                        <a href="{{ route('pops.show',$pop->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
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
