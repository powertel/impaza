@extends('layouts.admin')
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    Update Department
                </h3>
            </div>

            <div class="card-body">
                <form  action="{{ route('rfos.update',$rfo->id) }}" method="POST">
                    @csrf
                    @method('PUT')
        
                    <div class="form-group row">
                        <label for="RFO" class="col-sm-3 col-form-label">RFO</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control" name="RFO" value="{{ $rfo->RFO }}">
                            @error ('RFO')

                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                              

                            @enderror
                        </div>

                    </div>
                    
                    <div class="card-footer">

                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('rfos.index') }}">{{ __('Cancel') }}</a>

                        

                    </div>
                </form> 
            </div> 
        </div>
    </div>
</section>

@endsection

