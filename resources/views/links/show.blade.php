@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Link Details</h5>
          <a href="{{ route('links.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Links
          </a>
        </div>
        <div class="card-body">
          @if(isset($link))
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Customer</label>
              <div class="form-control bg-light">{{ $link->customer }}</div>
            </div>
            <div class="col-md-3">
              <label class="form-label">City/Town</label>
              <div class="form-control bg-light">{{ $link->city }}</div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Location</label>
              <div class="form-control bg-light">{{ $link->suburb }}</div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Pop</label>
              <div class="form-control bg-light">{{ $link->pop }}</div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Link Type</label>
              <div class="form-control bg-light">{{ $link->linkType ?? '' }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Link</label>
              <div class="form-control bg-light">{{ $link->link }}</div>
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
              <label class="form-label">JCC Number</label>
              <div class="form-control bg-light">{{ $link->jcc_number ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Service Type</label>
              <div class="form-control bg-light">{{ $link->service_type ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Capacity</label>
              <div class="form-control bg-light">{{ $link->capacity ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Contract Number</label>
              <div class="form-control bg-light">{{ $link->contract_number ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">SAP Codes</label>
              <div class="form-control bg-light">{{ $link->sapcodes ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Quantity</label>
              <div class="form-control bg-light">{{ $link->quantity ?? '—' }}</div>
            </div>
            <div class="col-md-12">
              <label class="form-label">Comment</label>
              <div class="form-control bg-light">{{ $link->comment ?? '—' }}</div>
            </div>
          </div>
          @else
            <div class="alert alert-warning">Link details not found.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection