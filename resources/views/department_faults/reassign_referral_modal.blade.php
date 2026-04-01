<div class="modal fade" id="reassignReferralModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="reassignReferralModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reassignReferralModalLabel-{{ $fault->id }}">Reassign Fault to Technician</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('referred_faults.reassign', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <p>This will accept the referral, move the fault to your section, and assign it to the selected technician.</p>
          
          <div class="mb-3">
            <label for="assignedTo-{{ $fault->id }}" class="form-label">Technician</label>
            @if($technicians->isEmpty())
                <div class="alert alert-warning">
                    No assignable technicians found in your section. Please ensure technicians have "Assignable" status.
                </div>
            @else
                <select class="form-select" id="assignedTo-{{ $fault->id }}" name="assignedTo" required>
                    <option value="" selected disabled>Select Technician</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
            @endif
          </div>

          <div class="mb-3">
            <label for="remark-{{ $fault->id }}" class="form-label">Remark</label>
            <textarea class="form-control" id="remark-{{ $fault->id }}" name="remark" rows="3" required placeholder="Enter remark for reassignment..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Reassign</button>
        </div>
      </form>
    </div>
  </div>
</div>
