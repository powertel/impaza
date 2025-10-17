@extends('layouts.admin')

@section('title')
Rectify
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">Fault Rectification</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Customer</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control" name="customer" value="{{ $fault->customer }}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Link</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control" name="link" value="{{ $fault->link }}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="clear" class="col-sm-3 col-form-label">Restored</label>
                        <div class="col-sm-9 form-check ">
                            <input type="checkbox" class="form-check-input">
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Clear') }}</button>  
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}" >{{ __('Cancel') }}</a>           
                    </div>
                </form> 
            </div> 
        </div>
    </div>
    <div class="col d-flex justify-content-center">
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">Remarks</h3>
            </div>
            <div class="card-body" style="height: 250px; overflow-y: auto">
                @foreach($remarks as $remark)
                @if ($remark->fault_id === $fault->id)
                <div class="callout callout-info">
                    @if($remark->user)
                    <h5 class="font-weight-bold">{{ $remark->user->name}}</h5>
                    @endif

                    <h4 class="text-muted text-sm">
                        <strong>
                        Added Remark  {{$remark->created_at->diffForHumans()}}
                       </strong>
                    </h4>

                    <p>{{$remark->remark}} </p>
                </div>
                @endif
                @endforeach
            </div> 

            <div class="card-footer">
                <form action="/faults/{{$fault->id}}/remarks" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="remark" class="form-control" placeholder="Enter Your Remarks" rows="1"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-sm float-right">{{ __('Add Remark') }}</button>
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
