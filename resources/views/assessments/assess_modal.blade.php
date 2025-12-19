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
          <!-- Fault Details -->
          <div class="col-lg-6">
            <div class="row g-4">
              <div class="col-12">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                  <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0 text-secondary"><i class="fas fa-info-circle me-2 text-primary"></i>Fault Details</h6>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Ref. No.</small>
                        <div class="fw-semibold">{{ $fault->fault_ref_number ?? 'N/A' }}</div>
                      </div>

                      <div>
                        <small class="text-muted">Customer Name</small>
                        <div class="fw-semibold">{{ $fault->customer }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">City/Town</small>
                        <div class="fw-semibold">{{ $fault->city ?? 'N/A' }}</div>
                      </div>
                      <div class="ms-4">
                        <small class="text-muted">Location</small>
                        <div class="fw-semibold">{{ $fault->suburb ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Link</small>
                        <div class="fw-semibold">{{ $fault->link }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">POP</small>
                        <div class="fw-semibold">{{ $fault->pop ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Service Type</small>
                        <div class="fw-semibold"><span class="badge bg-secondary">{{ $fault->serviceType ?? 'N/A' }}</span></div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Address</small>
                        <div class="fw-semibold">{{ $fault->address ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Account Manager</small>
                        <div class="fw-semibold">{{ $fault->accountManager ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Age</small>
                        <div class="fw-semibold">
                          <span class="badge bg-light text-dark border fault-age" data-age-start="{{ $ageStart ?? '' }}" data-age-end="{{ $ageEnd ?? '' }}">{{ $ageText ?? '' }}</span>
                        </div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Assessed By</small>
                        <div class="fw-semibold">{{ $fault->assessedBy ?? 'N/A' }}</div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="col-12">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                  <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0 text-secondary"><i class="fas fa-user-circle me-2 text-primary"></i>Contact & RFO</h6>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Contact Name</small>
                        <div class="fw-semibold">{{ $fault->contactName ?? 'N/A' }}</div>
                      </div>

                      <div>
                        <small class="text-muted">Phone Number</small>
                        <div class="fw-semibold">{{ $fault->phoneNumber ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Email Address</small>
                        <div class="fw-semibold">{{ $fault->contactEmail ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Suspected RFO</small>
                        <div class="fw-semibold">{{ $fault->RFO ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Confirmed RFO</small>
                        <div class="fw-semibold">{{ $fault->confirmedRFO ?? 'N/A' }}</div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Assessment -->
          <div class="col-lg-6">
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
                        <option value="Critical" {{ old('priorityLevel', $fault->priorityLevel ?? '') === 'Critical' ? 'selected' : '' }}>Critical</option>
                      </select>
                    </div>
                    <div class="mb-3 col-md-12">
                      <label class="form-label">Remarks</label>
                      <textarea class="form-control @error('remark') is-invalid @enderror" name="remark" rows="6" required>{{ old('remark') }}</textarea>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        @if(isset($remarks) && count($remarks))
        <div class="mt-4">
          <div class="d-flex align-items-center mb-2">
            <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
            <h6 class="mb-0 text-secondary">Conversation</h6>
          </div>

          <div id="remarksScroller-{{ $fault->id }}" style="max-height: 360px; overflow-y: auto; padding-right: 6px;">
            @foreach($remarks->sortBy('created_at') as $r)
              @php
                $currentName = optional(auth()->user())->name;
                $isOwn = $currentName && (strtolower(trim($r->name)) === strtolower(trim($currentName)));
              @endphp
              <div class="d-flex {{ $isOwn ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                <div class="rounded-3 shadow-sm px-3 py-2" style="max-width: 75%; background-color: {{ $isOwn ? '#e8f5e9' : '#eef5ff' }};">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge {{ $isOwn ? 'bg-success' : 'bg-secondary' }}">{{ $r->name ?? 'User' }}</span>
                    <small class="text-muted">{{ Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</small>
                    @if(!empty($r->activity))
                      <small class="text-muted">• {{ $r->activity }}</small>
                    @endif
                  </div>
                  <div class="fw-normal">{{ $r->remark }}</div>
                  @if($r->file_path)
                    <div class="mt-2">
                      <a href="{{ asset('storage/'.$r->file_path) }}" class="btn btn-link btn-sm text-decoration-none" download>
                        <i class="fas fa-download me-1"></i>
                      </a>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
        @endif
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
