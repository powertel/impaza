@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card card-primary w-100">
            <div class="card-header">
                <h3 class="card-title">
                    <h3 class="text-center" style="text-transform: uppercase;font-family: Times New Roman, Times, serif;">{{_('View Details')}}</h3> 
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col">
                        <strong>Contact Email</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col">
                        <strong>Contact Email</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col">
                        <strong>Contact Email</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col">
                        <strong>Contact Email</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col">
                        <strong>Contact Email</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Account Manager</strong>
                        <p class="text-muted">{{ $fault->accountManager }}</p>
                    </div>

                    <div class="col">
                        <strong>Contact Email</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger" href="javascript:history.back()">{{ __('Back') }}</a>
                </div>
            </div> 
        </div>
    </div>
 
</section>
@endsection

@section('scripts')

@endsection