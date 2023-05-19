@extends('layouts.admin')

@section('title')
Link
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Update Link')}}
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('links.update', $link->id ) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row">
                        <label for="customer" class="col-sm-3 col-form-label">Customer</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="customer" name="customer_id">
                                <option selected="selected" value="{{ $link->customer_id}}">{{ $link->customer }}</option>
                                @foreach($customers as $customer)
                                    @unless ($customer->id ===$link->customer_id)
                                        <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="city" class="col-sm-3 col-form-label">City/Town</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="city" name="city_id">
                                <option selected="selected" value="{{ $link->city_id}}">{{ $link->city }}</option>
                                @foreach($cities as $city)
                                    @unless ($city->id ===$link->city_id)
                                        <option value="{{ $city->id}}">{{ $city->city }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-3 col-form-label">Location</label>
                        <div class="col-sm-9">
                        <select   class="custom-select" id="suburb" name="suburb_id">
                             <option selected="selected" value="{{ $link->suburb_id}}">{{ $link->suburb }}</option>
                                @foreach($suburbs as $suburb)
                                    @if ($suburb->city_id === $link->city_id)
                                        @unless($suburb->id ===$link->suburb_id)
                                            <option value="{{ $suburb->id}}">{{ $suburb->suburb }}</option>
                                        @endunless
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pop" class="col-sm-3 col-form-label">Pop</label>
                        <div class="col-sm-9">
                            <select  class="custom-select" id="pop" name="pop_id" >
                                <option selected="selected" value="{{ $link->pop_id}}">{{ $link->pop }}</option>
                                @foreach($pops as $pop)
                                    @if($pop->suburb_id === $link->suburb_id)
                                        @unless($pop->id ===$link->pop_id)
                                            <option value="{{ $pop->id}}">{{ $pop->pop }}</option>
                                        @endunless
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="link" class="col-sm-3 col-form-label">Link</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="link"  value="{{ $link->link}}">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()">{{ __('Save') }}</button>
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
