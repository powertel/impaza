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
                    <h3 class="text-center" style="text-transform: uppercase;font-family: Times New Roman, Times, serif;">{{_('Create Fault')}}</h3> 
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
                                    <option value="{{ $customer->id}}">{{ $customer->customerName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="serviceType" class="form-label">Service Type</label>
                            <select type="text"  class="custom-select " name="serviceType">
                                <option selected>Choose</option>
                                <option>VOIP</option>
                                <option>VPN</option>
                                <option>INTERNET</option>
                                <option>CARRIER SERVICE</option>
                                <option>POWERTRACK</option>
                                <option>CDMA VOICE</option>
                                <option>CDMA VOICE</option>
                                <option>E-VENDING</option>
                                <option value="">0777430323</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="contactName" class="form-label">Contact Name</label>
                            <input type="text" class="form-control"  placeholder="Contact Name" name="contactName">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="city" class="form-label">Fault Locale</label>
                            <select  class="custom-select " name="city">
                                <option selected disabled  >Select City/Town</option>
                                @foreach($city as $city)
                                <option value="{{ $city->id}}">{{ $city->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="suburb" class="form-label">Suburb</label>
                            <select   class="custom-select" name="suburb">
                                <option selected disabled>Select Suburb</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="pop" class="form-label">POP</label>
                            <select  class="custom-select " name="pop" >
                                <option selected disabled>Select Pop</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control"  placeholder="Phone Number" name="phoneNumber">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="linkName" class="form-label">Link</label>
                            <select class="custom-select " name="linkName">
                                <option selected disabled>Select Link</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email AddressS</label>
                            <input type="email" class="form-control" placeholder="email" name="contactEmail">
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
                            <input type="text" class="form-control"  placeholder="Address" name="address">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="serviceAtrr" class="form-label">Service Attribute</label>
                            <select  class="custom-select " placeholder="service attribute" name="serviceAttribute">
                                <option>Choose</option>
                                <option>Port</option>
                                <option>VPN</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="accManager" class="form-label">Account Manager</label>
                            <input type="text" class="form-control"  placeholder="Address" name="accountManager">
                        </div>
    
                        <div class="mb-3 col-md-6">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" placeholder="Enter any additional comments" rows="1" ></textarea>
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