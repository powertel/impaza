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
                    {{_('Create Link')}}
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('links.store') }}" method="POST">
                {{ csrf_field() }}

                    <div class="form-group row">
                        <label for="customer" class="col-sm-3 col-form-label">Customer</label>
                        <div class="col-sm-9">
                            <select id="customer" class="custom-select  @error('customer_id') is-invalid @enderror" name="customer_id" value="{{ old('customer_id') }}">
                                <option selected disabled >Select Customer Name</option>
                                @foreach($customer as $customer)
                                    @if (old('customer_id')==$customer->id)
                                        <option value="{{ $customer->id}}" selected>{{ $customer->customer }}</option>
                                    @else
                                        <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label for="city" class="col-sm-3 col-form-label">City/Town</label>
                    <div class="col-sm-9">
                    <select id="city" class="custom-select @error('city_id') is-invalid @enderror" name="city_id">
                        <option selected disabled  >Select City/Town</option>
                        @foreach($city as $city)
                            @if (old('city_id')==$city->id)
                                <option value="{{ $city->id}}" selected>{{ $city->city }}</option>
                            @else
                                <option value="{{ $city->id}}">{{ $city->city }}</option>
                            @endif
                        @endforeach
                    </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="location" class="col-sm-3 col-form-label">Location</label>
                    <div class="col-sm-9">
                    <select id="suburb"  class="custom-select @error('suburb_id') is-invalid @enderror" name="suburb_id">
                        <option selected disabled>Select Suburb</option>
                        @foreach($location as $location)
                            @if (old('suburb_id')==$location->id)
                                <option value="{{ $location->id}}" selected>{{ $location->suburb }}</option>
                            @endif
                        @endforeach
                    </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pop" class="col-sm-3 col-form-label">Pop</label>
                    <div class="col-sm-9">
                    <select id="pop"  class="custom-select @error('pop_id') is-invalid @enderror" name="pop_id">
                        <option selected disabled>Select Pop</option>
                        @foreach($pop as $pop)
                            @if (old('pop_id')==$pop->id)
                                <option value="{{ $pop->id}}" selected>{{ $pop->pop }}</option>
                            @endif
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="linkType" class="col-sm-3 col-form-label">Link Type</label>
                    <div class="col-sm-9">
                    <select id="linkType"  class="custom-select @error('linkType_id') is-invalid @enderror" name="linkType_id">
                        <option selected disabled>Select Link Type</option>
                        @foreach($linkType as $link_type)
                        @if (old('linkType_id')==$link_type->id)
                                        <option value="{{ $link_type->id}}" selected>{{ $link_type->linkType }}</option>
                                    @else
                                        <option value="{{ $link_type->id}}">{{ $link_type->linkType }}</option>
                                    @endif

                        @endforeach
                    </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pop" class="col-sm-3 col-form-label">Link</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control link-name-input @error('link') is-invalid @enderror" name="link" placeholder="Link Name" value="{{ old('link') }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return inlineSave()">{{ __('Save') }}</button>
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
