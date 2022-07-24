@extends('layouts.admin')

@section('title')
Request Permit
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Request Permit')}}</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="mb-3 col">
                            <label for="issuedTo" class="form-label">Requested By</label>
                            <input type="text"  class="form-control" name="customer" value="{{ $fault->customer }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col">
                            <label for="faultNumber" class="form-label">Fault Number</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-3 col">
                            <label for="ptwNumber" class="form-label">PTW Number</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="mb-3 col">
                            <label for="crNumnber" class="form-label">CR Number</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row">

                        <div class="mb-3 col">
                            <label for="dateIssue" class="form-label">Date of Issue</label>
                            <input type="text" class="form-control">
                        </div>

                        <div class="mb-3 col">
                            <label for="startTime" class="form-label">Start TIme</label>
                            <input type="text" class="form-control">
                        </div>

                        <div class="mb-3 col">
                            <label for="endTime" class="form-label">End Time</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col">
                            <label for="issuedTo" class="form-label">Requested By</label>
                            <select  class="form-select">
                                <option>Select</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>Normal</option>
                                <option>High</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col">
                            <label  class="form-label">Description</label>
                            <textarea  class="form-control"></textarea>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Request') }}</button>  
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}" >{{ __('Cancel') }}</a>           
                    </div>
                </form> 
            </div> 
        </div>
    </div>
    <div class="col d-flex justify-content-center">
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Remarks')}}</h3>
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