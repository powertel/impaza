@can('link-edit')
@php
  $full = DB::table('links')
    ->leftJoin('customers','links.customer_id','=','customers.id')
    ->leftJoin('cities','links.city_id','=','cities.id')
    ->leftJoin('suburbs','links.suburb_id','=','suburbs.id')
    ->leftJoin('pops','links.pop_id','=','pops.id')
    ->leftJoin('link_types','links.linkType_id','=','link_types.id')
    ->where('links.id', $link->id)
    ->select('links.id','links.link','links.customer_id','links.city_id','links.suburb_id','links.pop_id','links.linkType_id','links.contract_number','links.jcc_number','links.sapcodes','links.comment','links.quantity','links.service_type','links.capacity')
    ->first();
@endphp
<div class="modal fade" id="linkEditModal{{ $link->id }}" tabindex="-1" aria-labelledby="linkEditModalLabel{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="linkEditModalLabel{{ $link->id }}">Edit Link</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('links.update', $link->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Customer</label>
              <select name="customer_id" class="form-select" required>
                @foreach($customers as $cust)
                  <option value="{{ $cust->id }}" {{ ($full && $full->customer_id == $cust->id) ? 'selected' : '' }}>{{ $cust->customer }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Link</label>
              <input type="text" name="link" value="{{ $full ? $full->link : $link->link }}" class="form-control link-name-input" data-ignore-id="{{ $link->id }}" required>
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
              <label class="form-label">JCC Number</label>
              <input type="text" name="jcc_number" value="{{ $full ? $full->jcc_number : '' }}" class="form-control jcc-number-input" data-ignore-id="{{ $link->id }}" placeholder="e.g. JCC-12345">
              <div class="invalid-feedback">JCC number already exists.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Service Type</label>
              <select name="service_type" class="form-select">
                <option value="" {{ empty($full?->service_type) ? 'selected disabled' : '' }}>Select Service Type</option>
                <option value="Internet" {{ ($full && $full->service_type === 'Internet') ? 'selected' : '' }}>Internet</option>

                <option value="VPN" {{ ($full && $full->service_type === 'VPN') ? 'selected' : '' }}>VPN</option>
                <option value="Carrier Services" {{ ($full && $full->service_type === 'Carrier Services') ? 'selected' : '' }}>Carrier Services</option>
                <option value="E-Vending" {{ ($full && $full->service_type === 'E-Vending') ? 'selected' : '' }}>E-Vending</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Capacity</label>
              <input type="text" name="capacity" value="{{ $full ? $full->capacity : '' }}" class="form-control" placeholder="e.g. 100Mbps">
            </div>

           <div class="col-md-4">
             <label class="form-label">Contract Number</label>
             <input type="text" name="contract_number" value="{{ $full ? $full->contract_number : '' }}" class="form-control" placeholder="e.g. CTR-2025-001" readonly>
           </div>
           <div class="col-md-4"> 
            <label class="form-label">SAP Codes</label> 
            <input type="text" name="sapcodes" value="{{ $full ? $full->sapcodes : '' }}" class="form-control" placeholder="e.g. SAP-ABC-123" readonly>
           </div>
           <div class="col-md-4">
             <label class="form-label">Quantity</label>
             <input type="number" name="quantity" value="{{ $full ? $full->quantity : '' }}" class="form-control" min="0" placeholder="e.g. 1" readonly>
           </div>
           <div class="col-md-12">
             <label class="form-label">Comment</label>
             <input type="text" name="comment" value="{{ $full ? $full->comment : '' }}" class="form-control" placeholder="Optional notes" readonly>
           </div>
            <div class="col-md-4">
              <label class="form-label">City/Town</label>
              <select name="city_id" class="form-select" required>
                 <option value="" selected disabled>Select City</option>
                @foreach($cities as $city)
                  <option value="{{ $city->id }}">{{ $city->city }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Location</label>
              @php $uniqueSuburbs = collect($suburbs)->unique('id'); @endphp
              <select name="suburb_id" class="form-select" required>
                <option value="" disabled {{ empty($full?->suburb_id) ? 'selected' : '' }}>Select Location</option>
                @foreach($uniqueSuburbs as $sub)
                  <option value="{{ $sub->id }}" {{ ($full && $full->suburb_id == $sub->id) ? 'selected' : '' }}>{{ $sub->suburb }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Pop</label>
              @php $uniquePops = collect($pops)->unique('id'); @endphp
              <select name="pop_id" class="form-select" required>
                <option value="" disabled {{ empty($full?->pop_id) ? 'selected' : '' }}>Select Pop</option>
                @foreach($uniquePops as $p)
                  <option value="{{ $p->id }}" {{ ($full && $full->pop_id == $p->id) ? 'selected' : '' }}>{{ $p->pop }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Link Type</label>
              <select name="linkType_id" class="form-select" required>
                @foreach($linkTypes as $lt)
                  <option value="{{ $lt->id }}" {{ ($full && $full->linkType_id == $lt->id) ? 'selected' : '' }}>{{ $lt->linkType }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-success btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
