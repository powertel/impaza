@can('customer-create')
<div class="modal fade" id="customerCreateModal" tabindex="-1" aria-labelledby="customerCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerCreateModalLabel">Create Customer(s)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-3">Add one or more customers. Use “Add another” to insert additional rows.</p>
          <div class="repeater" id="customerRepeater">
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3">
                <div class="row g-3 align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <input type="text" name="items[0][customer]" class="form-control customer-name-input" placeholder="e.g. Acme Corp" required>
                    <div class="invalid-feedback">This customer name already exists.</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="items[0][account_number]" class="form-control account-number-input" placeholder="e.g. 123456789" required>
                    <div class="invalid-feedback">This account number already exists。</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Account Manager</label>
                    <select name="items[0][account_manager_id]" class="form-select">
                      <option value="">None</option>
                      @isset($accountManagers)
                        @foreach($accountManagers as $am)
                          <option value="{{ $am->am_id }}">{{ $am->name ?? ('User #'.$am->user_id) }}</option>
                        @endforeach
                      @endisset
                    </select>
                    <input type="text" name="address" class="form-control" placeholder="Address" />
                    <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" />
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-light btn-sm" id="addCustomerRepeaterItem"><i class="fas fa-plus"></i> Add another</button>
              <button type="button" class="btn btn-light btn-sm" id="removeCustomerRepeaterItem"><i class="fas fa-minus"></i> Remove last</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

{{-- Repeater item fields --}}
@can('customer-create')
<div class="modal fade" id="customerCreateModal" tabindex="-1" aria-labelledby="customerCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerCreateModalLabel">Create Customer(s)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-3">Add one or more customers. Use “Add another” to insert additional rows.</p>
          <div class="repeater" id="customerRepeater">
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3">
                <div class="row g-3 align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <input type="text" name="customer" class="form-control" placeholder="Customer" />
                    <div class="invalid-feedback">This customer name already exists.</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control" placeholder="Account Number" />
                    <div class="invalid-feedback">This account number already exists。</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Account Manager</label>
                    <select name="account_manager_id" class="form-control">
                      <option value="">None</option>
                      @isset($accountManagers)
                        @foreach($accountManagers as $am)
                          <option value="{{ $am->am_id }}">{{ $am->name ?? ('User #'.$am->user_id) }}</option>
                        @endforeach
                      @endisset
                    </select>
                    <input type="text" name="address" class="form-control" placeholder="Address" />
                    <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" />
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-light btn-sm" id="addCustomerRepeaterItem"><i class="fas fa-plus"></i> Add another</button>
              <button type="button" class="btn btn-light btn-sm" id="removeCustomerRepeaterItem"><i class="fas fa-minus"></i> Remove last</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
