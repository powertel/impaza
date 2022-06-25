@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card card-primary  w-80">
            <div class="card-header">
                <h3 class="card-title">
                    <h3 class="text-center uppercase" style="text-transform: uppercase;">{{_('View Details')}}</h3> 
                </h3>
            </div>
            <div class="card-body">
                <div class="bg-light p-4 rounded">
                    <h1>Show user</h1>
                    <div class="lead">
                        
                    </div>
                    
                    <div class="container mt-4">
                        <div>
                            Name: {{ $fault->contactName }}
                        </div>
                        <div>
                            Email: {{ $fault->contactEmail }}
                        </div>
                        <div>
                            Username: {{ $fault->accountManager }}
                        </div>
                    </div>
            
                </div>
                <div class="mt-4">
                    <a type="button" class="btn btn-danger" href="javascript:history.back()">{{ __('Back') }}</a>
                </div>
            </div> 
        </div>
    </div>


 
</section>
@endsection

@section('scripts')

@endsection