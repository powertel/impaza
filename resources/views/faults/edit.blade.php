@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card card-primary  w-100">
            <div class="card-header">
                <h3 class="card-title">
                    <h3 class="text-center" style="text-transform: uppercase;font-family: Times New Roman, Times, serif;">{{_('Assess Fault')}}</h3> 
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('faults.store') }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="customerName" class="form-label">Customer Name </label>
                            <select class="custom-select " name="customerName">
                                <option selected disabled >Select Customer Name</option>
                                @foreach($customer as $customer)
{{-- {{$customer->id==$fault->id? 'selected':''}} --}}<option value="{{ $customer->id}}" {{$customer->id==1? 'selected':''}}>{{ $customer->customerName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="serviceType" class="form-label">Service Type</label>
                            <select type="text"  class="custom-select " name="serviceType">
                                <option></option>
                                <option>VOIP</option>
                                <option>VPN</option>
                                <option>INTERNET</option>
                                <option>CARRIER SERVICE</option>
                                <option>POWERTRACK</option>
                                <option>CDMA VOICE</option>
                                <option>CDMA VOICE</option>
                                <option>E-VENDING</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="contactName" class="form-label">Contact Name</label>
                            <input type="text" class="form-control" name="contactName">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="city" class="form-label">Fault Locale</label>
                            <select  class="custom-select " name="city">
                                @foreach($city as $city)
                                <option value="{{ $city->id}}" @selected(true)>{{ $city->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="suburb" class="form-label">Suburb</label>
                            <select   class="custom-select" name="suburb">
                                @foreach($suburb as $suburb)
{{-- {{$customer->id==$fault->id? 'selected':''}} --}}<option value="{{ $suburb->city_id}}" selected >{{ $suburb->suburb }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="pop" class="form-label">POP</label>
                            <select  class="custom-select " name="pop" >
                                @foreach($pop as $pop)
{{-- {{$customer->id==$fault->id? 'selected':''}} --}}<option value="{{ $pop->suburb_id}}" selected >{{ $pop->pop }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phoneNumber">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="linkName" class="form-label">Link</label>
                            <select class="custom-select " name="linkName">
                                @foreach($link as $link)
{{-- {{$customer->id==$fault->id? 'selected':''}} --}}<option value="{{ $link->customer_id}}" selected >{{ $link->linkName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email AddressS</label>
                            <input type="email" class="form-control" name="contactEmail">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="suspectedRfo" class="form-label">Suspected Reason For Outage</label>
                            <select  class="custom-select " name="suspectedRfo">
                                <option>Choose</option>
                                <option>No fx Light</option>
                                <option>No PON Light</option>
                                <option>BTS Down</option>
                                <option>Node Down</option>
                                <option>Unknown</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="adress" class="form-label">Address</label>
                            <input type="text" class="form-control"  name="address">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="serviceAtrr" class="form-label">Service Attribute</label>
                            <select  class="custom-select "  name="serviceAttribute">
                                <option>Choose</option>
                                <option>Port</option>
                                <option>VPN</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="accManager" class="form-label">Account Manager</label>
                            <input type="text" class="form-control"  name="accountManager">
                        </div>
    
                        <div class="mb-3 col-md-6">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="1" ></textarea>
                        </div>
    
                    </div>
        
                    <div class="card-footer">
                        <a type="button" class="btn btn-danger" href="javascript:history.back()">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-success float-right">{{ __('Save') }}</button>
                    </div>
                </form> 
            </div> 
        </div>
    </div>
 
</section>
@endsection

@section('scripts')
 <script type="text/javascript">
        jQuery(document).ready(function()
        {
            jQuery('select[name="city"]').on('change',function(){
                var CityID =jQuery(this).val();
                if(CityID)
                {
                    //call suburb on city
                    jQuery.ajax({
                        url : '/suburb/' +CityID,
                        type: "GET",
                        dataType: "json",
                        success:function(data)
                        {
                            jQuery('select[name="suburb"]').html('<option  selected Disabled>Select Suburb</option>');
                            jQuery.each(data,function(key,value){
                                $('select[name="suburb"]').append('<option value="'+ key+'">'+ value +'</option>');
                            });
                        }
                    });
                }
                else
                {
                    $('select[name="suburb"]').html('<option value="">Select Suburb</option>');
                }
            });

            jQuery('select[name="suburb"]').on('change',function(){
                var suburbID =jQuery(this).val();
                if(suburbID)
                {
                    //select pop on suburb
                    jQuery.ajax({
                        url : '/pop/' +suburbID,
                        type: "GET",
                        dataType: "json",
                        success:function(data)
                        {
                            jQuery('select[name="pop"]').html('<option selected Disabled>Select Pop</option>');
                            jQuery.each(data,function(key,value){
                                $('select[name="pop"]').append('<option value="'+ key+'">'+ value +'</option>');
                            });
                        }
                    });
                }
                else
                {
                    $('select[name="pop"]').html('<option value="">Select Pop</option>');
                }
            });
        });
    </script>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        jQuery('select[name="customerName"]').on('change',function(){
            var customerID =jQuery(this).val();
            if(customerID)
            {
                //call link on customer
                jQuery.ajax({
                    url : '/link/' +customerID,
                    type: "GET",
                    dataType: "json",
                    success:function(data)
                    {
                        jQuery('select[name="linkName"]').html('<option selected Disabled>Select Link</option>');
                        jQuery.each(data,function(key,value){
                            $('select[name="linkName"]').append('<option value="'+ key+'">'+ value +'</option>');
                        });
                    }
                });
            }
            else
            {
                $('select[name="linkName"]').html('<option value="">Select Link</option>');
            }
        });
    });
</script>


@endsection