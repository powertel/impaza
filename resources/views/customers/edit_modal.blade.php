@can('customer-edit')
@foreach($customers as $customer)
<div class="modal fade" id="customerEditModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerEditModalLabel{{ $customer->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
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
            <input type="text" name="customer" class="form-control customer-name-input" value="{{ $customer->customer }}" required data-ignore-id="{{ $customer->id }}">
            <div class="invalid-feedback">This customer name already exists.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Account Number</label>
            <input type="text" name="account_number" class="form-control account-number-input" value="{{ $customer->account_number ?? '' }}" required data-ignore-id="{{ $customer->id }}">
            <div class="invalid-feedback">This account number already exists.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Account Manager</label>
            <select name="account_manager_id" class="form-select" required>
              <option value="" disabled {{ empty($customer->account_manager_id) ? 'selected' : '' }}>Select Account Manager</option>
              @isset($accountManagers)
                @foreach($accountManagers as $am)
                  <option value="{{ $am->am_id }}" {{ (int)$customer->account_manager_id === (int)$am->user_id ? 'selected' : '' }}>
                    {{ $am->name ?? ('User #'.$am->user_id) }}
                  </option>
                @endforeach
              @endisset
            </select>
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
@endforeach
@endcan
