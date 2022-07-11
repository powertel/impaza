@extends('layouts.admin')

@section('title')
Assign
@endsection

@section('content')
    @include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Assign')}}</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Technician</label>
                        <div class="col-sm-9">
                            <select class="custom-select" name="" id="">
                                <option>Select Technician</option>
                                <option>Chipangura</option>
                                <option>Hotera</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Assign') }}</button>  
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}" >{{ __('Cancel') }}</a>           
                    </div>
                </form> 
            </div> 
        </div>
    </div>
</section>
@endsection

@section('scripts')
    @include('partials.faults')
@endsection