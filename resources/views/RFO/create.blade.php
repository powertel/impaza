@extends('layouts.admin')

@section('title')
Reasons For Outage
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    Create RFO
                </h3>
            </div>

            <div class="card-body">
                <form action="{{ route('rfos.store') }}" method="POST">
                {{ csrf_field() }}

                    <div class="form-group row">
                        <label for="RFO" class="col-sm-3 col-form-label">RFO</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control @error('RFO') is-invalid @enderror" name="RFO" placeholder="Reason For Outage" value="{{ old('RFO') }}">
                            @error ('RFO')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror

                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" onclick="return submitResult()" class="btn btn-success btn-sm" >{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('rfos.index') }}">{{ __('Cancel') }}</a>

                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

