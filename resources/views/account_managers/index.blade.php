@extends('layouts.admin')

@section('title')
Account Managers
@endsection

@section('content')
@include('partials.css')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Account Manager')}}</h3>
        <div class="card-tools">
            @can('account-manager-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('account_managers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Account Manager')}} </a>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Account Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($account_managers as $acc_manager)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $acc_manager->accountManager}}</td>
                    <td>
                        <form action="{{ route('account_managers.destroy',$acc_manager->id) }}" method="POST">
                            <a href="{{ route('account_managers.show',$acc_manager->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                            @can('account-manager-edit')
                            <a href="{{ route('account_managers.edit',$acc_manager->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                            @endcan

                            @csrf
                            @method('DELETE')
                            @can('account-manager-delete')
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
