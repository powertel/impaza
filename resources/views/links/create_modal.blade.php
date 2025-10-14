@can('link-create')
<div class="modal fade" id="createLinkModal" tabindex="-1" aria-labelledby="createLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createLinkModalLabel">Create Links</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('links.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
              <option value="" disabled selected>Select Customer</option>
              @foreach($customers as $cust)
                <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->customer }}</option>
              @endforeach
            </select>
            @error('customer_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div id="linkRepeater" class="">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">Link Items</h6>
              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary me-1" id="removeLinkRepeaterItem"><i class="fas fa-minus-circle"></i> Remove Last</button>
                <button type="button" class="btn btn-outline-primary" id="addLinkRepeaterItem"><i class="fas fa-plus-circle"></i> Add</button>
              </div>
            </div>
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3 position-relative">
                <!-- <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 remove-item-btn"><i class="fas fa-times"></i></button> -->
                <div class="row g-3 align-items-end">
                  <div class="col-md-3">
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
                  <div class="col-md-3">
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
                  <div class="col-md-3">
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
                  <div class="col-md-3">
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
                  <div class="col-md-6">
                    <label class="form-label">Link</label>
                    <input type="text" name="items[0][link]" class="form-control @error('items.0.link') is-invalid @enderror" placeholder="e.g. MPLS-001" required>
                    @error('items.0.link')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
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
          <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan