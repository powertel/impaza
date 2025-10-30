@can('link-edit')
<div class="modal fade" id="editExistingLinksModal" tabindex="-1" aria-labelledby="editExistingLinksLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editExistingLinksLabel">Edit Existing Links</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Customer</label>
          <select id="editLinksCustomer" class="form-select">
            <option value="" selected disabled>Select Customer</option>
            @foreach($customers as $cust)
              <option value="{{ $cust->id }}">{{ $cust->customer }}</option>
            @endforeach
          </select>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th style="width: 24%">Link</th>
                <th style="width: 14%">City/Town</th>
                <th style="width: 14%">Location</th>
                <th style="width: 14%">Pop</th>
                <th style="width: 13%">Service Type</th>
                <th style="width: 11%">Capacity</th>
                <th style="width: 10%">Link Type</th>
                <th style="width: 6%">#</th>
              </tr>
            </thead>
            <tbody id="editExistingLinksBody">
              <tr class="text-center text-muted"><td colspan="8">Select a customer to load links…</td></tr>
            </tbody>
          </table>
        </div>

        <!-- Hidden templates for select options -->
        <div id="editLinksSelectTemplates" class="d-none">
          <select id="editLinksCitiesTpl">
            
            @php $uniqueCities = collect($cities)->unique('id'); @endphp
            @foreach($uniqueCities as $city)
              <option value="{{ $city->id }}">{{ $city->city }}</option>
            @endforeach
          </select>
          <select id="editLinksLinkTypesTpl">
            @foreach($linkTypes as $lt)
              <option value="{{ $lt->id }}">{{ $lt->linkType }}</option>
            @endforeach
          </select>
          <select id="editLinksServiceTypesTpl">
            <option value="">Select Service</option>
            <option value="Internet">Internet</option>
            <option value="VPN">VPN</option>
            <option value="Carrier Services">Carrier Services</option>
            <option value="E-Vending">E-Vending</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal" onclick="location.reload()">
          <i class="bi bi-x-lg"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
@endcan