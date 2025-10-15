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

          <!-- Assessment Form -->
          <div class="col-lg-7">
            <form id="assess-form-{{ $fault->id }}" action="{{ route('assessments.update', $fault->id ) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="row g-3">
                <div class="mb-3 col-md-6">
                  <label class="form-label">Fault Type</label>
                  <select class="form-select @error('faultType') is-invalid @enderror" name="faultType" required>
                    <option selected disabled>Select Fault Type</option>
                    <option value="Carrier/Mux">Carrier/Mux</option>
                    <option value="logical">logical</option>
                    <option value="Cable">Cable</option>
                    <option value="Power">Power</option>
                    <option value="Active Equipments">Active Equipments</option>
                  </select>
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label">Confirmed Reason For Outage</label>
                  <select class="form-select @error('confirmedRfo_id') is-invalid @enderror" name="confirmedRfo_id" required>
                    <option selected disabled>Select RFO</option>
                    @foreach($confirmedRFO as $confirmed_rfo)
                      <option value="{{ $confirmed_rfo->id }}">{{ $confirmed_rfo->RFO }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row g-3">
                <div class="mb-3 col-md-6">
                  <label class="form-label">Actioning Department</label>
                  <select class="form-select @error('section_id') is-invalid @enderror" name="section_id" required>
                    <option selected disabled>Select Department</option>
                    @foreach($sections as $section)
                      <option value="{{ $section->id }}">{{ $section->section }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label">Priority Level</label>
                  <select class="form-select @error('priorityLevel') is-invalid @enderror" name="priorityLevel" required>
                    <option selected disabled>Select</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="assess-form-{{ $fault->id }}" class="btn btn-primary">Save Assessment</button>
      </div>
    </div>
  </div>
</div>