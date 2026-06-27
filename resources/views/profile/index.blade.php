@extends('layouts.admin')

@section('title')
Profile
@endsection
@include('partials.css')
@section('styles')
<style>
  .profile-page .profile-layout {
    display: grid;
    grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
    gap: 16px;
  }

  .profile-page .profile-hero {
    display: flex;
    flex-direction: column;
    gap: 14px;
    align-items: center;
    text-align: center;
  }

  .profile-page .profile-hero-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .profile-page .profile-hero-meta {
    font-size: .8rem;
    color: var(--impaza-muted);
  }

  .profile-page .profile-role-list {
    justify-content: center;
  }

  .profile-page .profile-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }

  .profile-page .profile-form-grid .full-span {
    grid-column: 1 / -1;
  }

  @media (max-width: 991.98px) {
    .profile-page .profile-layout {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 767.98px) {
    .profile-page .profile-form-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endsection
@section('content')
@php
  $user = auth()->user();
  $dept = optional($user)->department_id ? optional(\App\Models\Department::find($user->department_id))->department : null;
  $section = optional($user)->section_id ? optional(\App\Models\Section::find($user->section_id))->section : null;
  $position = optional($user)->position_id ? optional(\App\Models\Position::find($user->position_id))->position : null;
  $roleNames = $user ? $user->getRoleNames() : collect();
@endphp

<section class="content workflow-faults-page profile-page">
  <div class="workspace-summary-grid">
    <div class="workspace-summary-card" style="--summary-color:#6366F1;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user"></i></span>
          <div>
            <div class="workspace-summary-label">Profile Status</div>
            <div class="workspace-summary-title">{{ optional($user)->email_verified_at ? 'Verified account' : 'Pending verification' }}</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $roleNames->count() }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-building"></i></span>
          <div>
            <div class="workspace-summary-label">Department</div>
            <div class="workspace-summary-title">{{ $dept ?? 'Not assigned' }}</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $section ? 1 : 0 }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#10B981;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-map-marker-alt"></i></span>
          <div>
            <div class="workspace-summary-label">Region</div>
            <div class="workspace-summary-title">{{ optional($user)->region ?? 'Not set' }}</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $user && $user->weekly_standby ? 'W' : '-' }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Profile</h3>
        <div class="page-lead">Review your account information, update personal details, and manage password security from one modern account workspace.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-id-badge"></i> {{ $roleNames->count() }} active role{{ $roleNames->count() === 1 ? '' : 's' }}</span>
        @if ($user)
          <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="fas fa-key me-1"></i> Change Password
          </button>
        @endif
      </div>
    </div>

    <div class="card-body">
      <div class="profile-layout">
        <div class="workspace-panel">
          <div class="workspace-panel-body">
            <div class="profile-hero">
              <div class="workspace-avatar">
                {{ optional($user)->name ? strtoupper(substr($user->name,0,1)) : 'U' }}
              </div>
              <div>
                <div class="profile-hero-name">{{ optional($user)->name ?? 'Guest' }}</div>
                <div class="profile-hero-meta">{{ optional($user)->email ?? 'No email available' }}</div>
              </div>
              <div class="workspace-chip-stack profile-role-list">
                @forelse($roleNames as $role)
                  <span class="badge rounded-pill" style="background: rgba(16, 185, 129, .14); color: #047857;">{{ $role }}</span>
                @empty
                  <span class="workspace-cell-sub">No roles assigned</span>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        <div class="workspace-panel">
          <div class="workspace-panel-header">
            <div>
              <h5 class="workspace-panel-title mb-0"><i class="fas fa-circle-info me-1"></i>Account Overview</h5>
              <div class="workspace-panel-lead">Current identity, reporting line, and standby coverage details.</div>
            </div>
          </div>
          <div class="workspace-panel-body">
            <div class="workspace-stat-list">
              <div class="workspace-stat-row">
                <div>
                  <div class="workspace-stat-label">Phone</div>
                  <div class="workspace-cell-main">{{ optional($user)->phonenumber ?? '—' }}</div>
                </div>
                <div class="workspace-stat-value">Direct line</div>
              </div>
              <div class="workspace-stat-row">
                <div>
                  <div class="workspace-stat-label">Department</div>
                  <div class="workspace-cell-main">{{ $dept ?? '—' }}</div>
                </div>
                <div class="workspace-stat-value">{{ $section ?? 'No section' }}</div>
              </div>
              <div class="workspace-stat-row">
                <div>
                  <div class="workspace-stat-label">Position</div>
                  <div class="workspace-cell-main">{{ $position ?? '—' }}</div>
                </div>
                <div class="workspace-stat-value">{{ optional($user)->region ?? 'No region' }}</div>
              </div>
              <div class="workspace-stat-row">
                <div>
                  <div class="workspace-stat-label">Standby</div>
                  <div class="workspace-chip-stack">
                    <span class="badge rounded-pill {{ $user && $user->weekly_standby ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}">Weekly: {{ $user && $user->weekly_standby ? 'Yes' : 'No' }}</span>
                    <span class="badge rounded-pill {{ $user && $user->weekend_standby ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}">Weekend: {{ $user && $user->weekend_standby ? 'Yes' : 'No' }}</span>
                  </div>
                </div>
                <div class="workspace-stat-value">{{ optional($user)->email_verified_at ? optional($user)->email_verified_at->format('d M Y') : 'Not verified' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="workspace-panel mt-3">
        <div class="workspace-panel-header">
          <div>
            <h5 class="workspace-panel-title mb-0"><i class="fas fa-user-edit me-1"></i>Edit Profile</h5>
            <div class="workspace-panel-lead">Update your contact details while keeping reporting structure fields read-only for consistency.</div>
          </div>
        </div>
        <div class="workspace-panel-body">
          <form method="POST" action="{{ route('user.postProfile') }}">
            @csrf
            <div class="profile-form-grid">
              <div>
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ optional($user)->name }}" required placeholder="Name">
                @error('name')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
              <div>
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="phonenumber" id="phone" class="form-control @error('phonenumber') is-invalid @enderror" placeholder="e.g 263786533333" value="{{ optional($user)->phonenumber }}" required>
                @error('phonenumber')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
              <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ optional($user)->email }}" disabled>
              </div>
              <div>
                <label for="department" class="form-label">Department</label>
                <input type="text" id="department" class="form-control" value="{{ $dept ?? '—' }}" disabled>
              </div>
              <div>
                <label for="section" class="form-label">Section</label>
                <input type="text" id="section" class="form-control" value="{{ $section ?? '—' }}" disabled>
              </div>
              <div>
                <label for="position" class="form-label">Position</label>
                <input type="text" id="position" class="form-control" value="{{ $position ?? '—' }}" disabled>
              </div>
            </div>
            <div class="d-flex justify-content-start align-items-center flex-wrap gap-2 mt-3">
              <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-save me-1"></i> Update Profile</button>
              @if ($user)
              <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fas fa-key me-1"></i> Change Password
              </button>
              @endif
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal custom-modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="changePasswordLabel"><i class="fas fa-key me-1"></i>Change Password</h5>
            <div class="modal-subtitle">Choose a strong new password that meets the current security rules.</div>
          </div>
          <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('user.password.update') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="newpassword" class="form-label">New Password</label>
              <div class="password-wrapper">
                <input id="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" required autocomplete="new-password" placeholder="Enter new password" minlength="8" maxlength="30" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+">
                <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="newpassword">
                  <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                </button>
              </div>
              <div class="password-strength-meter"><div class="strength-bar"></div></div>
              <div class="password-strength-text"></div>
              <small class="text-muted">Minimum 8 characters with uppercase, lowercase, number, and special character.</small>
              @error('newpassword')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <label for="newpassword_confirmation" class="form-label">Confirm New Password</label>
              <div class="password-wrapper">
                <input id="newpassword_confirmation" type="password" class="form-control @error('newpassword_confirmation') is-invalid @enderror" name="newpassword_confirmation" required autocomplete="new-password" placeholder="Confirm new password" minlength="8" maxlength="30" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+">
                <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="newpassword_confirmation">
                  <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                </button>
              </div>
              @error('newpassword_confirmation')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
