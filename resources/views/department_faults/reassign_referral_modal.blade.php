@php
  $latestRemarkForReassignReferral = collect($remarks ?? [])->sortByDesc('created_at')->first();
@endphp
<div class="modal custom-modal fade" id="reassignReferralModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="reassignReferralModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="reassignReferralModalLabel-{{ $fault->id }}"><i class="fas fa-user-plus me-2"></i>Reassign Referral</h5>
          <div class="text-muted small mt-1">Accept this referral into your section and assign it to the right technician.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('referred_faults.reassign', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>This action accepts the referral, moves the fault into your section workflow, and hands it to the selected technician.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-users-gear"></i></span>
              <div>
                <div class="fault-modal-section-title">Assignment Details</div>
                <div class="fault-modal-section-subtitle">Choose the technician and add the handoff note.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-3">
                <label for="assignedTo-{{ $fault->id }}" class="form-label">Technician</label>
                @if($technicians->isEmpty())
                    <div class="alert alert-warning mb-0">
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

              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label" for="switch_name-{{ $fault->id }}">Switch</label>
                  <input id="switch_name-{{ $fault->id }}" type="text" class="form-control" name="switch_name" value="{{ old('switch_name', $latestRemarkForReassignReferral->switch_name ?? '') }}" placeholder="Enter switch name or identifier">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="port-{{ $fault->id }}">Port</label>
                  <input id="port-{{ $fault->id }}" type="text" class="form-control" name="port" value="{{ old('port', $latestRemarkForReassignReferral->port ?? '') }}" placeholder="Enter port number or label">
                </div>
                <div class="col-md-12">
                  <label for="remark-{{ $fault->id }}" class="form-label">Remark</label>
                  <textarea class="form-control" id="remark-{{ $fault->id }}" name="remark" rows="3" required placeholder="Enter remark for reassignment..."></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill">Reassign</button>
        </div>
      </form>
    </div>
  </div>
</div>
