@extends('layouts.admin')

@section('title')
Stores
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-100">
            <div class="card-header">
                <h3 class="card-title">
                  Requested Materials
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>Fault Ref. No.</strong>
                        <p class="text-muted">{{ $stores->fault_ref_number }}</p>
                    </div>

                    <div class="col">
                        <strong>Fault Name</strong>
                        <p class="text-muted">{{ $stores->fault_name }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-md-6">
                        <strong>Requisition No.</strong>
                        <p class="text-muted">{{ $stores->requisition_number }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>SAP Ref. No.</strong>
                        <p class="text-muted">{{ $stores->ref_Number }}</p>
                    </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Date Created</strong>
                        <p class="text-muted">{{ $stores->created_at }}</p>
                    </div>
                <hr>

                <div class="card-footer">

                </div>
            </div>
        </div>
        </div>
    </div>

</section>
@endsection

@section('scripts')

@endsection

