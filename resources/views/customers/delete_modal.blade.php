@can('customer-delete')
@foreach($customers as $customer)
<div class="modal fade" id="customerDeleteModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerDeleteModalLabel{{ $customer->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerDeleteModalLabel{{ $customer->id }}">Delete Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong>{{ $customer->customer }}</strong>?</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan