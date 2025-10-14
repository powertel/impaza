@can('customer-list')
@foreach($customers as $customer)
<div class="modal fade" id="customerViewModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerViewModalLabel{{ $customer->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerViewModalLabel{{ $customer->id }}">Customer Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Customer</dt>
          <dd class="col-sm-7">{{ $customer->customer }}</dd>
          <dt class="col-sm-5">Account Number</dt>
          <dd class="col-sm-7">{{ $customer->account_number }}</dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan