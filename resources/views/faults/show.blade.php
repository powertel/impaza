@extends('layouts.admin')

@section('title')
Fault
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-100">
            <div class="card-header">
                <h3 class="card-title">
                  {{_('View Details')}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>CUSTOMER</strong>
                        <p class="text-muted">{{ $fault->customer }}</p>
                    </div>

                    <div class="col">
                        <strong>CITY/TOWN</strong>
                        <p class="text-muted">{{ $fault->city }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-md-6">
                        <strong>CONTACT NAME</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col-md-2">
                        <strong>LOCATION</strong>
                        <p class="text-muted">{{ $fault->suburb }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>LINK</strong>
                        <p class="text-muted">{{ $fault->link }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>POP</strong>
                        <p class="text-muted">{{ $fault->pop }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>PHONE NUMBER</strong>
                        <p class="text-muted">{{ $fault->phoneNumber }}</p>
                    </div>

                    <div class="col">
                        <strong>SERVICE TYPE</strong>
                        <p class="text-muted">{{ $fault->serviceType }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>EMAIL ADDRESS</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>

                    <div class="col">
                        <strong>SUSPECTED REASON FOR OUTAGE</strong>
                        <p class="text-muted">{{ $SuspectedRFO->RFO}}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>PHYSICAL ADDRESS</strong>
                        <p class="text-muted">{{ $fault->address }}</p>
                    </div>

                    <div class="col">
                        <strong>ACCOUNT MANAGER</strong>
                        <p class="text-muted">{{ $fault->accountManager }}</p>
                    </div>
                </div>
                <hr>

                <div class="row g-2">
                    <div class="col">
                        <strong>CONFIRMED REASON FOR OUTAGE</strong>
                        <p class="text-muted">{{ $ConfirmedRFO->RFO}}</p>
                    </div>

                    <div class="col">
                        <strong>FAULT TYPE</strong>
                        <p class="text-muted">{{ $fault->faultType }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>PRIORITY LEVEL</strong>
                        <p class="text-muted">{{ $fault->priorityLevel }}</p>
                    </div>
                </div>
                <hr>

                <div class="card-footer">
                    @can('fault-assessment')
                        <a type="button" class="btn btn-primary btn-sm" href="{{ route('assessments.edit',$fault->id) }}">Assess</a>
                    @endcan
                    @can('assign-fault')
                    <a type="button" class="btn btn-Success btn-sm" href="{{ route('assign.edit',$fault->id) }}">Assign</a>
                    @endcan
                    <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Close') }}</a>
                </div>
            </div> 
        </div>
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Remarks')}}</h3>
            </div>
            <div class="card-body" style="height: 0px; overflow-y: auto">
                @foreach($remarks as $remark)
                <div class="callout callout-info">
                    <h5 class="font-weight-bold">{{ $remark->name}}</h5>
                    <h4 class="text-muted text-sm">
                        <strong>
                        Added Remark  {{Carbon\Carbon::parse($remark->created_at)->diffForHumans()}}
                       </strong>
                    </h4>
                    <h5 class="font-weight-bold">{{ $remark->activity}}</h5>
                    <p>{{$remark->remark}} </p>
                    <h4 class="text-muted text-sm">
                        <strong>
                        Attachment
                       </strong>
                    </h4>
                    <img src="{{asset('storage/'.$remark->file_path)}}"alt="Not here!" title="Attachment" style="height:100px; width:auto">
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#PicModal-{{ $remark->id }}">View Attachment</button>
                </div>
                @endforeach
            </div> 
        </div>
    </div>
<!-- Modal -->
<div class="modal custom-modal fade" id="PicModal-{{ $remark->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="PicModalLabel-{{ $remark->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="PicModalLabel-{{ $remark->id }}">Remark Attachment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="{{asset('storage/'.$remark->file_path)}}" alt="Not here!" style="height:500px; max-width:100%" title="Attachment">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
  </div>
</div>
</section>
@endsection

@section('scripts')

@endsection