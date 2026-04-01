@can('customer-edit')
@foreach($customers as $customer)
<div class="modal fade" id="customerEditModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerEditModalLabel{{ $customer->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerEditModalLabel{{ $customer->id }}">Edit Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Customer</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-building"></i></span>
              <input type="text" name="customer" class="form-control customer-name-input" value="{{ $customer->customer }}" required data-ignore-id="{{ $customer->id }}">
            </div>
            <div class="invalid-feedback">This customer name already exists.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Account Number</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
              <input type="text" name="account_number" class="form-control account-number-input" value="{{ $customer->account_number ?? '' }}" required data-ignore-id="{{ $customer->id }}" readonly>
            </div>
            <div class="invalid-feedback">This account number already exists.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Contract Number</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
              <input type="text" name="contract_number" class="form-control" value="{{ $customer->contract_number ?? '' }}" placeholder="e.g. CTR-00001">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Account Manager</label>
            <select name="account_manager_id" class="form-select" required>
              <option value="" disabled {{ empty($customer->account_manager_id) ? 'selected' : '' }}>Select Account Manager</option>
              @isset($accountManagers)
                @foreach($accountManagers as $am)
                  <option value="{{ $am->am_id }}" {{ (int)$customer->account_manager_id === (int)$am->am_id ? 'selected' : '' }}>
                    {{ $am->name ?? ('User #'.$am->user_id) }}
                  </option>
                @endforeach
              @endisset
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ $customer->address ?? '' }}" placeholder="Address">
          </div>
          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control" value="{{ $customer->contact_number ?? '' }}" placeholder="Contact Number">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_pop_aggregator" value="1" id="is_pop_aggregator_{{ $customer->id }}" {{ !empty($customer->is_pop_aggregator) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_pop_aggregator_{{ $customer->id }}">
              POP Aggregator Customer
            </label>
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
@endforeach
@endcan
