@extends('layouts.admin')

@section('title')
Materials
@endsection

@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Request Material')}}
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('stores.store') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-group row">
                    <label for="issuedTo" class="col-sm-3 col-form-label">Fault ID</label>
                    <div class="col-sm-9 ">

                        @error ('fault_id')
                            <div class="alert-danger">
                                 {{$message }}
                            </div>
                        @enderror
                    </div >
                        <label for="SAP_ref" class="col-sm-3 col-form-label">Sap Ref. No.</label>
                        <div class="col-sm-9 ">
                            <input type="text" class="form-control @error('request-material') is-invalid @enderror" name="SAP_ref" placeholder="Enter SAP No." value="{{ old('SAP_ref') }}">
                            @error ('SAP_ref')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror
                        </div >
                        <label for="materials" class="col-sm-3 col-form-label">Material</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('request-material') is-invalid @enderror" name="materials" placeholder="Material Description" value="{{ old('materials') }}">
                            @error ('materials')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror
                        </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return inlineSave()">{{ __('Request') }}</button>
                    <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Cancel') }}</a>
                </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection
@section('scripts')
    @include('partials.scripts')
@endsection
