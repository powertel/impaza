@extends('layouts.admin')

@section('title')
Assign
@endsection

@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Re-Assign')}}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('assign.update', $fault->id ) }}" method="POST">
                @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Technician</label>
                        <div class="col-sm-9">
                            <select class="custom-select @error('assignedTo') is-invalid @enderror" name="assignedTo">
                                <option selected disabled  >Assign</option>
                                @foreach($technicians as $tech)
                                    @if (old('assignedTo')==$tech->id)
                                        <option value="{{ $tech->id}}" selected>{{ $tech->name }}</option>
                                    @else
                                        <option value="{{ $tech->id}}">{{ $tech->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Assign') }}</button>  
                        <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Cancel') }}</a>           
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