<!-- Show User Modal -->
<div class="modal custom-modal fade" id="showUserModal-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showUserModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0">
        <div class="d-flex align-items-center">
          <span class="badge bg-primary me-2"><i class="fas fa-user"></i></span>
          <h5 class="modal-title mb-0" id="showUserModalLabel-{{ $user->id }}">View User</h5>
        </div>
        <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <small class="text-muted">Last Login</small>
                    <div class="fw-semibold">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never' }}</div>
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
                      <span class="badge rounded-pill" style="background-color: {{ App\Models\UserStatus::STATUS_COLOR[ $user->status_name ] ?? '#6c757d' }}; color: black;">{{ $user->status_name }}</span>
                    </div>
                  </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Region</small>
                    <div class="fw-semibold">{{ $user->region }}</div>
                  </div>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <small class="text-muted">Phone Number</small>
                    <div class="fw-semibold">{{ $user->phonenumber }}</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="row g-4 mt-1">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 text-secondary"><i class="fas fa-clock me-2 text-primary"></i>Login History</h6>
              </div>
              <div class="card-body pt-0">
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th style="width: 180px;">When</th>
                        <th>Details</th>
                      </tr>
                    </thead>
                    <tbody id="loginHistoryBody-{{ $user->id }}" data-user-id="{{ $user->id }}"></tbody>
                  </table>
                </div>
                <div class="d-flex justify-content-end mt-2">
                  <button
                    type="button"
                    class="btn btn-outline-primary btn-sm login-history-load-more"
                    data-user-id="{{ $user->id }}"
                    data-initial-url="{{ route('users.login-history', $user->id) }}"
                  >
                    <i class="fas fa-plus me-1"></i> Load more
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary border" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
