<!-- Assess Fault Modal -->
<div class="modal custom-modal fade" id="assessFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="assessFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assessFaultModalLabel-{{ $fault->id }}">
          <i class="fas fa-clipboard-check me-2"></i>Assess Fault
        </h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4">
          <!-- Fault Summary -->
          <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 text-secondary"><i class="fas fa-info-circle me-2 text-primary"></i>Fault Summary</h6>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <small class="text-muted">Customer</small>
                  <div class="fw-semibold">{{ $fault->customer }}</div>
                </li>
                <li class="list-group-item">
                  <small class="text-muted">Link</small>
                  <div class="fw-semibold">{{ $fault->link }}</div>
                </li>
                <li class="list-group-item">
                  <small class="text-muted">Account Manager</small>
                  <div class="fw-semibold">{{ $fault->accountManager }}</div>
                </li>
                <li class="list-group-item">
                  <small class="text-muted">Contact Name</small>
                  <div class="fw-semibold">{{ $fault->contactName }}</div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Assessment -->
          <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 text-secondary"><i class="fas fa-clipboard-check me-2 text-primary"></i>Assessment</h6>
              </div>
              <div class="card-body">
                <form id="assess-form-{{ $fault->id }}" action="{{ route('assessments.update', $fault->id ) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="row g-3">
                    <div class="mb-3 col-md-12">
                      <label class="form-label">Fault Type</label>
                      <select class="form-select @error('faultType') is-invalid @enderror" name="faultType" required>
                        <option disabled {{ old('faultType', $fault->faultType ?? null) ? '' : 'selected' }}>Select Fault Type</option>
                        <option value="Logical" {{ old('faultType', $fault->faultType ?? '') === 'Logical' ? 'selected' : '' }}>Logical</option>
                        <option value="Physical" {{ old('faultType', $fault->faultType ?? '') === 'Physical' ? 'selected' : '' }}>Physical</option>
                      </select>
                    </div>
                    <div class="mb-3 col-md-12">
                      <label class="form-label">Priority Level</label>
                      <select class="form-select @error('priorityLevel') is-invalid @enderror" name="priorityLevel" required>
                        <option disabled {{ old('priorityLevel', $fault->priorityLevel ?? null) ? '' : 'selected' }}>Select</option>
                        <option value="Low" {{ old('priorityLevel', $fault->priorityLevel ?? '') === 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priorityLevel', $fault->priorityLevel ?? '') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priorityLevel', $fault->priorityLevel ?? '') === 'High' ? 'selected' : '' }}>High</option>
                      </select>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" form="assess-form-{{ $fault->id }}" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-save me-1"></i> Save Assessment
        </button>
      </div>
    </div>
  </div>
</div>
