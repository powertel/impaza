@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('content')
<section class="content">
    <div class="container-fluid justify-content-center">
        <div class="card-header">
            <h3 class="card-title">{{_('Add Fault')}}</h3>
            <div class="float-end">
                 <a type="button" class="btn grey btn-outline-secondary" href="javascript:history.back()">{{ __('Back') }}</a>
            </div>
        </div>
        <div class="card-body">
            
            <form>
                <div class="row g-2">
                    <div class="mb-3 col-md-6">
                        <label for="customerName" class="form-label">Customer Name </label>
                        <select class="form-select"formControlName="customer_id">
                            <option value="0" >Select Customer Name</option>
                            <option></option>
                        </select>  
        
                    </div>
        
                    <div class="mb-3 col-md-6">
                        <label for="serviceType" class="form-label">Service Type</label>
                        <select type="text"  class="form-select" formControlName="serviceType">
                            <option selected>Choose</option>
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
                        <input type="text" class="form-control"  placeholder="Contact Name">
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="city" class="form-label">Fault Locale</label>
                        <select  class="form-select" name="city">
                            <option selected="false" >Select City/Town</option>
                            @foreach($city as $city)
                            <option value="{{ $city->id}}">{{ $city->city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="suburb" class="form-label">Suburb</label>
                        <select   class="form-select" name="suburb">
                            <option selected="false" >Select Suburb</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="pop" class="form-label">POP</label>
                        <select id="pop" name="pop"  class="form-select">
                            <option>Select Pop</option>
                            <option></option>
                        </select>
                    </div>
                </div>
        
                <div class="row g-2">
                    <div class="mb-3 col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control"  placeholder="Phone Number">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="linkName" class="form-label">Link Name</label>
                        <select class="form-select" >
                            <option>select link</option>
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="email">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="suspectedRfo" class="form-label">Suspected Reason For Outage</label>
                        <select id="suspectedRfo" class="form-select">
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
                        <input type="text" class="form-control"  placeholder="Address" >
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="serviceAtrr" class="form-label">Service Attribute</label>
                        <select  class="form-select" placeholder="service attribute">
                            <option>Port</option>
                            <option>VPN</option>
                        </select>
                    </div>
                </div>
        
                <div class="row g-2">
                    <div class="mb-3 col-md-6">
                        <label for="accManager" class="form-label">Account Manager</label>
                        <input type="text" class="form-control"  placeholder="Address">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select  class="form-select" placeholder="status">
                            <option>new fault</option>
                            <option>unaccessed</option>
                            <option>cleared</option>
                            <option>parked</option>
                            <option>finished</option>
                        </select>
                    </div>
                </div>
        
                <div class="mb-3 col-md-6">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" placeholder="Enter any additional comments" ></textarea>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                        <a type="button" class="btn grey btn-outline-secondary" href="javascript:history.back()">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                </div>
            </form> 
        </div> 
    </div>


   <!--  <div class="modal fade text-left" id="ModalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Create New User') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>                    
                </div>
                <div class="modal-body">

                <form action="">
                {{ csrf_field() }}
                <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>{{ __('Name') }}:</strong>
                            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>{{ __('Email') }}:</strong>
                            {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>{{ __('Password') }}:</strong>
                            {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>{{ __('Confirm Password') }}:</strong>
                            {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>{{ __('Role') }}:</strong>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">{{ __('Back') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div> -->

</section>
@endsection

@section('scripts')
<script>
        jQuery(document).ready(function()
        {
            jQuery('select[name="city"]').on('change',function(){
                var CityID =jQuery(this).val();
                if(CityID)
                {
                    jQuery.ajax({
                        url : '/getSuburb/' +CityID,
                        type: "GET",
                        dataType: "json",
                        success:function(data)
                        {
                            jQuery('select[name="suburb"]').empty();
                            jQuery.each(data,function(key,value){
                                $('select[name="suburb"]').append('<option value="'+ key+'">'+ value +'</option>');
                            });
                        }
                    });
                }
                else
                {
                    $('select[name="suburb"]').empty();
                }
            });

            jQuery('select[name="suburb"]').on('change',function(){
                var suburbID =jQuery(this).val();
                if(subrubID)
                {
                    jQuery.ajax({
                        url : '/getPop/' +suburbID,
                        type: "GET",
                        dataType: "json",
                        success:function(data)
                        {
                            jQuery('select[name="pop"]').empty();
                            jQuery.each(data,function(key,value){
                                $('select[name="pop"]').append('<option value="'+ key+'">'+ value +'</option>');
                            });
                        }
                    });
                }
                else
                {
                    $('select[name="pop"]').empty();
                }
            });
        });
        
    </script> 

   <!--  <script type="text/javascript">
	$(document).ready(function(){

		$(document).on('change','.productcategory',function(){


			var cat_id=$(this).val();

			var div=$(this).parent();

			var op=" ";

			$.ajax({
				type:'get',
				url:'{!!URL::to('findProductName')!!}',
				data:{'id':cat_id},
				success:function(data){

					op+='<option value="0" selected disabled>chose product</option>';
					for(var i=0;i<data.length;i++){
					op+='<option value="'+data[i].id+'">'+data[i].productname+'</option>';
				   }

				   div.find('.productname').html(" ");
				   div.find('.productname').append(op);
				},
				error:function(){

				}
			});
		});

		$(document).on('change','.productname',function () {
			var prod_id=$(this).val();

			var a=$(this).parent();
			console.log(prod_id);
			var op="";
			$.ajax({
				type:'get',
				url:'{!!URL::to('findPrice')!!}',
				data:{'id':prod_id},
				dataType:'json',
				success:function(data){
					console.log("price");
					console.log(data.price);

					a.find('.prod_price').val(data.price);

				},
				error:function(){

				}
			});


		});

	});
</script> 
 -->
@endsection