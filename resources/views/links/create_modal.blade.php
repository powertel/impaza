@can('link-create')
<div class="modal custom-modal fade" id="createLinkModal" tabindex="-1" aria-labelledby="createLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="createLinkModalLabel"><i class="fas fa-link me-2"></i>Create Links</h5>
          <div class="text-muted small mt-1">Create one or more service links with the refreshed business modal workflow, including location mapping and service details.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-layer-group"></i> Bulk Create</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map-marker-alt"></i> Full Mapping</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('links.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Select the customer once, then add one or more link rows with service, contract, and location details.</div>
          </div>
          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-users"></i></span>
              <div>
                <div class="fault-modal-section-title">Customer Context</div>
                <div class="fault-modal-section-subtitle">Choose the customer that owns the links being created.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-0">
                <label for="customer_id" class="form-label">Customer</label>
                <select id="customer_id" name="customer_id" class="form-select select2 @error('customer_id') is-invalid @enderror" required>
                  <option disabled selected>Select Customer</option>
                  @foreach($customers as $cust)
                    <option value="{{ $cust->id }}" data-contract-number="{{ $cust->contract_number }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->customer }}</option>
                  @endforeach
                </select>
                @error('customer_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div id="linkRepeater" class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-link"></i></span>
              <div>
                <div class="fault-modal-section-title">Link Items</div>
                <div class="fault-modal-section-subtitle">Add each service link with the required mapping and service metadata.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">Link Items</h6>
            </div>
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3 position-relative">
                <!-- <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 remove-item-btn"><i class="fas fa-times"></i></button> -->
                <div class="row g-3 align-items-end">
                  <!-- Row 1: Link (paired visually with the Customer select above) -->
                  <div class="col-md-6">
                    <label class="form-label">Link</label>
                    <input type="text" name="items[0][link]" class="form-control link-name-input @error('items.0.link') is-invalid @enderror" placeholder="e.g. HRE-ZB-Magetsi" required>
                    @error('items.0.link')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Link Type</label>
                    <select name="items[0][linkType_id]" class="form-select @error('items.0.linkType_id') is-invalid @enderror" required>
                      <option value="" disabled selected>Select Type</option>
                      @foreach($linkTypes as $lt)
                        <option value="{{ $lt->id }}">{{ $lt->linkType }}</option>
                      @endforeach
                    </select>
                    @error('items.0.linkType_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 d-none d-md-block"></div>

                  <!-- Row 2: JCC Number, Service Type, Capacity -->
                  <div class="w-100"></div>
                  <div class="col-md-4">
                    <label class="form-label">JCC Number</label>
                    <input type="text" name="items[0][jcc_number]" class="form-control jcc-number-input" placeholder="e.g. JCC-12345">
                    <div class="invalid-feedback">JCC number already exists.</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Service Type</label>
                    <select name="items[0][service_type]" class="form-select">
                      <option value="" selected disabled >Select Service Type</option>
                      <option value="Internet">Internet</option>
                      <option value="Metro VPN">Metro VPN</option>
                      <option value="Intercity VPN">Intercity VPN</option>
                      <option value="Carrier Services">Carrier Services</option>
                      <option value="E-Vending">E-Vending</option>
                      <option value="Dark-Fibre">Dark-Fibre</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Capacity</label>
                    <input type="text" name="items[0][capacity]" class="form-control" placeholder="e.g. 100Mbps">
                  </div>

                  <!-- Row 2b: Contract Number, SAP Codes, Quantity, Comment -->
                  <div class="w-100"></div>
                  <div class="col-md-3">
                    <label class="form-label">Contract Number</label>
                    <input type="text" name="items[0][contract_number]" class="form-control" placeholder="Auto-filled from customer" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">SAP Codes</label>
                    <input type="text" name="items[0][sapcodes]" class="form-control" placeholder="e.g. SAP-ABC-123">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="items[0][quantity]" class="form-control" min="0" placeholder="e.g. 1">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Comment</label>
                    <input type="text" name="items[0][comment]" class="form-control" placeholder="Optional notes">
                  </div>
                  <!-- Row 3: City/Town, Location, Pop -->
                  <div class="w-100"></div>
                  <div class="col-md-4">
                    <label class="form-label">City/Town</label>
                    <select name="items[0][city_id]" class="form-select @error('items.0.city_id') is-invalid @enderror" required>
                      <option value="" disabled selected>Select City</option>
                      @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->city }}</option>
                      @endforeach
                    </select>
                    @error('items.0.city_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Location</label>
                    <select name="items[0][suburb_id]" class="form-select @error('items.0.suburb_id') is-invalid @enderror" required>
                      <option value="" disabled selected>Select Location</option>
                      @foreach($suburbs as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->suburb }}</option>
                      @endforeach
                    </select>
                    @error('items.0.suburb_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Pop</label>
                    <select name="items[0][pop_id]" class="form-select @error('items.0.pop_id') is-invalid @enderror" required>
                      <option value="" disabled selected>Select Pop</option>
                      @foreach($pops as $p)
                        <option value="{{ $p->id }}">{{ $p->pop }}</option>
                      @endforeach
                    </select>
                    @error('items.0.pop_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-3 d-none d-md-block"></div>

                  <!-- Row 4: Link Type -->
                  <div class="w-100"></div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-start align-items-center mt-2">
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="addLinkRepeaterItem"><i class="fas fa-plus-circle me-1"></i> Add</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="removeLinkRepeaterItem"><i class="fas fa-minus-circle me-1"></i> Remove Last</button>
              </div>
            </div>
          </div>
          </div>
          <!-- Hidden templates for repeater option cloning -->
          <div id="linkSelectTemplates" class="d-none">
            <select id="linkCitiesTemplate">
              @foreach($cities as $city)
                <option value="{{ $city->id }}">{{ $city->city }}</option>
              @endforeach
            </select>
            <select id="linkSuburbsTemplate">
              @foreach($suburbs as $sub)
                <option value="{{ $sub->id }}">{{ $sub->suburb }}</option>
              @endforeach
            </select>
            <select id="linkPopsTemplate">
              @foreach($pops as $p)
                <option value="{{ $p->id }}">{{ $p->pop }}</option>
              @endforeach
            </select>
            <select id="linkTypesTemplate">
              @foreach($linkTypes as $lt)
                <option value="{{ $lt->id }}">{{ $lt->linkType }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
