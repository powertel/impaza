<!-- Show User Modal -->
<div class="modal custom-modal fade" id="showUserModal-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showUserModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0">
        <div class="d-flex align-items-center">
          <span class="badge bg-primary me-2"><i class="fas fa-user"></i></span>
          <h5 class="modal-title mb-0" id="showUserModalLabel-{{ $user->id }}">View User</h5>
        </div>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0">
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 text-secondary"><i class="fas fa-id-card me-2 text-primary"></i>Basic Info</h6>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Name</small>
                    <div class="fw-semibold">{{ $user->name }}</div>
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Email</small>
                    <div class="fw-semibold">{{ $user->email }}</div>
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Department</small>
                    <div class="fw-semibold">{{ $user->department }}</div>
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Section</small>
                    <div class="fw-semibold">{{ $user->section }}</div>
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Position</small>
                    <div class="fw-semibold">{{ $user->position }}</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 text-secondary"><i class="fas fa-user-shield me-2 text-primary"></i>Roles & Status</h6>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Roles</small>
                    <div class="fw-semibold">
                      @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                          <span class="badge bg-success me-1">{{ $v }}</span>
                        @endforeach
                      @endif
                    </div>
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Status</small>
                    <div class="fw-semibold">
                      <span class="badge rounded-pill" style="background-color: {{ App\Models\UserStatus::STATUS_COLOR[ $user->status_name ] ?? '#6c757d' }}; color: #fff;">{{ $user->status_name }}</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>