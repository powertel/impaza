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
                <h3 class="card-title">{{_('Fault Rectification')}}</h3>
            </div>
            <div class="card-body">
            <form action="{{ route('rectify.update', $fault->id ) }}" method="POST">
                @csrf
                    @method('PUT')
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

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Restore') }}</button>  
                        <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Cancel') }}</a>           
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
                    <img src="{{asset('storage/'.$remark->file_path)}}"alt="Not here!" title="Attachment" style="height:100px; width:auto" onclick="enlargeImg()">
                </div>
                @endforeach
            </div> 
  <!-- Modal -->
<div class="modal fade bd-example-modal-xl"  id="PicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> REMARK ATTACHMENT</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <img id="show_it" src=""alt="Not here!" style="height:500px; max-width:100%" title="Attachment">
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
            <div class="card-footer">
                <form action="/faults/{{$fault->id}}/remarks" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Enter Your Remarks and Attach Your File Below If Any" rows="1"></textarea>
                        <input type="hidden" name="activity" value="ON RECTIFICATION"> 
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" id="fileToUpload">
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