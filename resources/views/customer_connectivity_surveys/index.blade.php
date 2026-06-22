@extends('layouts.admin')

@section('title')
Customer Connectivity Surveys
@endsection

@section('pageName')
Customer Connectivity Surveys
@endsection

@include('partials.css')

@section('content')
<section class="content">
  @php
    $i = ((int) $surveys->currentPage() - 1) * (int) $surveys->perPage();
    $latestCreatedAt = $stats['latest_created_at'] ?? null;
    $perPage = (int) ($perPage ?? $surveys->perPage());
  @endphp

  <div class="row row-cols-4 g-3 mb-3">
    <div class="col">
      <a href="{{ route('customer-connectivity-surveys.index', array_filter(['q' => $q, 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#6c757d"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-secondary"><i class="fas fa-wifi"></i></span>
              <div>
                <div class="text-muted small">All</div>
                <div class="fw-semibold">Surveys</div>
              </div>
            </div>
            <div class="fs-5 fw-bold text-dark">{{ (int)($stats['total'] ?? 0) }}</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="{{ route('customer-connectivity-surveys.index', array_filter(['q' => $q, 'status' => 'submitted', 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#198754"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-success"><i class="fas fa-paper-plane"></i></span>
              <div>
                <div class="text-muted small">Submitted</div>
                <div class="fw-semibold">Surveys</div>
              </div>
            </div>
            <div class="fs-5 fw-bold text-dark">{{ (int)($stats['submitted'] ?? 0) }}</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="{{ route('customer-connectivity-surveys.index', array_filter(['q' => $q, 'status' => 'draft', 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#f59e0b"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-warning text-dark"><i class="fas fa-save"></i></span>
              <div>
                <div class="text-muted small">Draft</div>
                <div class="fw-semibold">Surveys</div>
              </div>
            </div>
            <div class="fs-5 fw-bold text-dark">{{ (int)($stats['draft'] ?? 0) }}</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="{{ route('customer-connectivity-surveys.index', array_filter(['q' => $q, 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#0d6efd"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-primary"><i class="fas fa-calendar-day"></i></span>
              <div>
                <div class="text-muted small">Latest</div>
                <div class="fw-semibold">Capture</div>
              </div>
            </div>
            <div class="fw-bold text-dark">{{ $latestCreatedAt ? \Illuminate\Support\Carbon::parse($latestCreatedAt)->format('Y-m-d') : '-' }}</div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="d-flex align-items-center gap-3">
        <div>
          <h3 class="card-title mb-0">Manage and track customer connectivity surveys</h3>
        </div>
      </div>
      <div class="card-tools">
        @can('survey-create')
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ccSurveyCreateModal">
            <i class="fas fa-plus-circle"></i> New Survey
          </button>
        @endcan
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
          <div class="input-group input-group-sm" style="width: 200px;">
            <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
            <select id="ccSurveysPageSize" class="form-select form-select-sm" style="width:auto;">
              <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
              <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
              <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
              <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
            </select>
          </div>
          <form method="GET" action="{{ route('customer-connectivity-surveys.index') }}" class="m-0">
            <div class="input-group input-group-sm" style="width: 760px; max-width: 100%;">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
              </div>
              <select name="status" class="form-select form-select-sm" style="width: 140px;">
                <option value="" {{ ($status === null) ? 'selected' : '' }}>All</option>
                <option value="submitted" {{ ($status === 'submitted') ? 'selected' : '' }}>Submitted</option>
                <option value="draft" {{ ($status === 'draft') ? 'selected' : '' }}>Draft</option>
              </select>
              <input type="hidden" name="per_page" id="ccPerPageInput" value="{{ (int)$perPage }}">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search me-1"></i> Search</span>
              </div>
              <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Customer, account/JC, site, coords...">
              <div class="input-group-append">
                <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
              </div>
            </div>
          </form>
        </div>

        <table class="table table-hover align-middle cc-table cc-mobile-stack" id="cc-surveys-list" style="font-size:14px">
          <thead class="table-light cc-thead">
            <tr>
              <th>ID</th>
              <th>Status</th>
              <th>Customer</th>
              <th>Account/JC</th>
              <th>Site</th>
              <th>Photos</th>
              <th>Captured By</th>
              <th class="text-nowrap">Created</th>
              <th class="text-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($surveys as $s)
              <tr>
                <td class="text-nowrap text-muted" data-label="ID">#{{ $s->id }}</td>
                <td data-label="Status">
                  @if ($s->status === 'submitted')
                    <span class="badge bg-success">Submitted</span>
                  @else
                    <span class="badge bg-warning text-dark">Draft</span>
                  @endif
                </td>
                <td data-label="Customer">{{ $s->customer_name ?: '-' }}</td>
                <td data-label="Account/JC">{{ $s->account_or_jc_number ?: '-' }}</td>
                <td data-label="Site">{{ $s->site_name ?: '-' }}</td>
                <td data-label="Photos">{{ $s->photos_count ?? 0 }}</td>
                <td data-label="Captured By">{{ optional($s->user)->name ?: '-' }}</td>
                <td class="text-nowrap" data-label="Created">{{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('j F Y h:i a') : '-' }}</td>
                <td class="text-nowrap" data-label="Actions">
                  <div class="btn-group btn-group gap-2" role="group" aria-label="Actions">
                    @can('survey-edit')
                      <a class="btn btn-outline-primary btn-sm" href="{{ route('customer-connectivity-surveys.show', $s->id) }}?edit=1">
                        <i class="fas fa-edit me-1"></i> Edit
                      </a>
                    @endcan
                    <a class="btn btn-outline-success btn-sm" href="{{ route('customer-connectivity-surveys.show', $s->id) }}">
                      <i class="fas fa-eye me-1"></i> View
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr class="cc-empty-row">
                <td colspan="9" class="text-center text-muted py-5">No customer connectivity surveys to display</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
          Showing {{ $surveys->firstItem() ?? 0 }} to {{ $surveys->lastItem() ?? 0 }} of {{ $surveys->total() }} results
        </small>
        {{ $surveys->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

  @can('survey-create')
    <div class="modal fade cc-modal" id="ccSurveyCreateModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header cc-modal-header">
            <div>
              <h5 class="modal-title mb-0">Customer Connectivity Survey Sheet</h5>
              <div class="cc-modal-subtitle small">Progressive form (step-by-step)</div>
            </div>
            <button type="button" class="btn-close cc-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body cc-modal-body">
            @if (session('error') || $errors->any())
              <div class="alert alert-danger">
                <div class="fw-semibold">Unable to submit. Please fix the following and try again.</div>
                @if (session('error'))
                  <div class="mt-2">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                  <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $err)
                      <li>{{ $err }}</li>
                    @endforeach
                  </ul>
                @endif
              </div>
            @endif

            <div class="cc-modal-top mb-3">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold" id="ccSurveyStepTitle">General</div>
                <div class="text-muted small"><span id="ccSurveyStepNo">1</span> / <span id="ccSurveyStepTotal">8</span></div>
              </div>
              <div class="progress mt-2" style="height:8px;">
                <div class="progress-bar" id="ccSurveyProgressBar" role="progressbar" style="width: 0%"></div>
              </div>
              <div class="d-flex flex-wrap gap-2 mt-3 cc-stepper" id="ccStepNav">
                <button type="button" class="btn btn-sm cc-step-btn" data-step="0">1. General</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="1">2. Service</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="2">3. Permissions</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="3">4. Outdoor</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="4">5. Indoor</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="5">6. BoQ</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="6">7. Photos</button>
                <button type="button" class="btn btn-sm cc-step-btn" data-step="7">8. Overview</button>
              </div>
            </div>

            <form method="POST" action="{{ route('customer-connectivity-surveys.store') }}" enctype="multipart/form-data" id="ccSurveyForm">
              @csrf
              <input type="hidden" name="status" id="ccSurveyStatus" value="draft">

              <div class="survey-step" data-step="0">
                <div class="card">
                  <div class="card-header">General</div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Date (YYYY-MM-DD)</label>
                          <input type="text" name="meta[date]" class="form-control form-control-sm" value="{{ old('meta.date', date('Y-m-d')) }}">
                        </div>
                      </div>
                      <div class="col-md-8">
                        <div class="mb-3">
                          <label class="form-label">Survey Performed By</label>
                          <input type="text" name="meta[surveyPerformedBy]" class="form-control form-control-sm" value="{{ old('meta.surveyPerformedBy', optional(auth()->user())->name) }}" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Customer Name</label>
                          <input type="text" name="general[customerName]" class="form-control form-control-sm" value="{{ old('general.customerName') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Account / JC Number</label>
                          <input type="text" name="general[accountOrJcNumber]" class="form-control form-control-sm" value="{{ old('general.accountOrJcNumber') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Site Name / Location</label>
                          <input type="text" name="general[siteName]" class="form-control form-control-sm" value="{{ old('general.siteName') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Physical Address</label>
                          <input type="text" name="general[physicalAddress]" class="form-control form-control-sm" value="{{ old('general.physicalAddress') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Latitude</label>
                          <input type="text" name="general[latitude]" class="form-control form-control-sm cc-lat" value="{{ old('general.latitude') }}" placeholder="-17.8292">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Longitude</label>
                          <input type="text" name="general[longitude]" class="form-control form-control-sm cc-lng" value="{{ old('general.longitude') }}" placeholder="31.0522">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <input type="hidden" name="general[coordinates]" class="cc-coords" value="{{ old('general.coordinates') }}">
                        <div class="alert alert-info mb-0">Capture latitude and longitude for accurate location.</div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3 mt-3">
                          <label class="form-label">Customer Contact (Name)</label>
                          <input type="text" name="general[customerContactName]" class="form-control form-control-sm" value="{{ old('general.customerContactName') }}">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3 mt-3">
                          <label class="form-label">Phone</label>
                          <input type="text" name="general[customerContactPhone]" class="form-control form-control-sm" value="{{ old('general.customerContactPhone') }}">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3 mt-3">
                          <label class="form-label">Email</label>
                          <input type="text" name="general[customerContactEmail]" class="form-control form-control-sm" value="{{ old('general.customerContactEmail') }}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="1">
                <div class="card">
                  <div class="card-header">Service Requirements</div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Service Type</label>
                          <select name="serviceRequirements[serviceType]" class="form-select form-select-sm">
                            @php $v = old('serviceRequirements.serviceType'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Fibre" {{ ($v === 'Fibre') ? 'selected' : '' }}>Fibre</option>
                            <option value="Wireless" {{ ($v === 'Wireless') ? 'selected' : '' }}>Wireless</option>
                            <option value="Metro-E" {{ ($v === 'Metro-E') ? 'selected' : '' }}>Metro-E</option>
                            <option value="Other" {{ ($v === 'Other') ? 'selected' : '' }}>Other</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Bandwidth Down (Mbps)</label>
                          <input type="text" name="serviceRequirements[bandwidthDown]" class="form-control form-control-sm" value="{{ old('serviceRequirements.bandwidthDown') }}">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Bandwidth Up (Mbps)</label>
                          <input type="text" name="serviceRequirements[bandwidthUp]" class="form-control form-control-sm" value="{{ old('serviceRequirements.bandwidthUp') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Service Purpose</label>
                          <input type="text" name="serviceRequirements[servicePurpose]" class="form-control form-control-sm" value="{{ old('serviceRequirements.servicePurpose') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Redundancy Required</label>
                          <select name="serviceRequirements[redundancyRequired]" class="form-select form-select-sm">
                            @php $v = old('serviceRequirements.redundancyRequired'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Handover Interface</label>
                          <select name="serviceRequirements[handoverInterface]" class="form-select form-select-sm">
                            @php $v = old('serviceRequirements.handoverInterface'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="RJ45" {{ ($v === 'RJ45') ? 'selected' : '' }}>RJ45 (Copper)</option>
                            <option value="SFP" {{ ($v === 'SFP') ? 'selected' : '' }}>SFP (Fibre)</option>
                            <option value="Other" {{ ($v === 'Other') ? 'selected' : '' }}>Other</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Public IPs Required</label>
                          <select name="serviceRequirements[publicIpsRequired]" class="form-select form-select-sm">
                            @php $v = old('serviceRequirements.publicIpsRequired'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Public IP Qty</label>
                          <input type="text" name="serviceRequirements[publicIpsQty]" class="form-control form-control-sm" value="{{ old('serviceRequirements.publicIpsQty') }}">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="mb-0">
                          <label class="form-label">VLAN / Routing Notes</label>
                          <textarea name="serviceRequirements[vlanNotes]" class="form-control form-control-sm" rows="2">{{ old('serviceRequirements.vlanNotes') }}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="2">
                <div class="card">
                  <div class="card-header">Site Access & Permissions</div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Access Contact (authority)</label>
                          <input type="text" name="permissions[accessContact]" class="form-control form-control-sm" value="{{ old('permissions.accessContact') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Survey Done With</label>
                          <input type="text" name="permissions[surveyDoneWith]" class="form-control form-control-sm" value="{{ old('permissions.surveyDoneWith') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Working Hours / Restrictions</label>
                          <input type="text" name="permissions[workingHours]" class="form-control form-control-sm" value="{{ old('permissions.workingHours') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Permissions Required</label>
                          <input type="text" name="permissions[permissionsRequired]" class="form-control form-control-sm" value="{{ old('permissions.permissionsRequired') }}">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="mb-0">
                          <label class="form-label">Notes</label>
                          <textarea name="permissions[notes]" class="form-control form-control-sm" rows="2">{{ old('permissions.notes') }}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="3">
                <div class="card">
                  <div class="card-header">Outdoor Connectivity</div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Nearest POP / Node</label>
                          <input type="text" name="outdoor[nearestPopNode]" class="form-control form-control-sm" value="{{ old('outdoor.nearestPopNode') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Feeder / Switch / OLT Name</label>
                          <input type="text" name="outdoor[feederSwitchOlt]" class="form-control form-control-sm" value="{{ old('outdoor.feederSwitchOlt') }}">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Free Port Available</label>
                          <select name="outdoor[freePortAvailable]" class="form-select form-select-sm">
                            @php $v = old('outdoor.freePortAvailable'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Port ID</label>
                          <input type="text" name="outdoor[portId]" class="form-control form-control-sm" value="{{ old('outdoor.portId') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Estimated Distance (POP to Site)</label>
                          <input type="text" name="outdoor[estimatedDistance]" class="form-control form-control-sm" value="{{ old('outdoor.estimatedDistance') }}" placeholder="m / km">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Route Type</label>
                          <select name="outdoor[routeType]" class="form-select form-select-sm">
                            @php $v = old('outdoor.routeType'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Underground" {{ ($v === 'Underground') ? 'selected' : '' }}>Underground (duct)</option>
                            <option value="Overhead" {{ ($v === 'Overhead') ? 'selected' : '' }}>Overhead (poles)</option>
                            <option value="Mixed" {{ ($v === 'Mixed') ? 'selected' : '' }}>Mixed</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Existing Infrastructure</label>
                          <input type="text" name="outdoor[existingInfrastructure]" class="form-control form-control-sm" value="{{ old('outdoor.existingInfrastructure') }}">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="mb-3">
                          <label class="form-label">Obstructions / Risks</label>
                          <input type="text" name="outdoor[obstructionsRisks]" class="form-control form-control-sm" value="{{ old('outdoor.obstructionsRisks') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Nearest Manhole / Pole Reference</label>
                          <input type="text" name="outdoor[nearestManholePoleReference]" class="form-control form-control-sm" value="{{ old('outdoor.nearestManholePoleReference') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Manhole / JB Details</label>
                          <input type="text" name="outdoor[manholeJbDetails]" class="form-control form-control-sm" value="{{ old('outdoor.manholeJbDetails') }}">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="mb-0">
                          <label class="form-label">New Proposed Manholes / Poles (Grid refs)</label>
                          <textarea name="outdoor[proposedRefs]" class="form-control form-control-sm" rows="2">{{ old('outdoor.proposedRefs') }}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="4">
                <div class="card">
                  <div class="card-header">Indoor Assessment</div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Space for Terminal Equipment</label>
                          <input type="text" name="indoor[spaceForEquipment]" class="form-control form-control-sm" value="{{ old('indoor.spaceForEquipment') }}">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Cabinet / Rack Available</label>
                          <select name="indoor[cabinetAvailable]" class="form-select form-select-sm">
                            @php $v = old('indoor.cabinetAvailable'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Cabinet Size / U</label>
                          <input type="text" name="indoor[cabinetSize]" class="form-control form-control-sm" value="{{ old('indoor.cabinetSize') }}">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">New Cabinet Required</label>
                          <select name="indoor[newCabinetRequired]" class="form-select form-select-sm">
                            @php $v = old('indoor.newCabinetRequired'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Power Available</label>
                          <select name="indoor[powerAvailable]" class="form-select form-select-sm">
                            @php $v = old('indoor.powerAvailable'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Socket Type</label>
                          <select name="indoor[socketType]" class="form-select form-select-sm">
                            @php $v = old('indoor.socketType'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Round" {{ ($v === 'Round') ? 'selected' : '' }}>Round</option>
                            <option value="Square" {{ ($v === 'Square') ? 'selected' : '' }}>Square</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label class="form-label">Distance to Socket (m)</label>
                          <input type="text" name="indoor[distanceToSocket]" class="form-control form-control-sm" value="{{ old('indoor.distanceToSocket') }}">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Back-up Power</label>
                          <input type="text" name="indoor[backupPower]" class="form-control form-control-sm" value="{{ old('indoor.backupPower') }}">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Air-conditioning</label>
                          <select name="indoor[airConditioning]" class="form-select form-select-sm">
                            @php $v = old('indoor.airConditioning'); @endphp
                            <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                            <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Earthing</label>
                          <input type="text" name="indoor[earthing]" class="form-control form-control-sm" value="{{ old('indoor.earthing') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Internal Cabling Route</label>
                          <input type="text" name="indoor[internalCablingRoute]" class="form-control form-control-sm" value="{{ old('indoor.internalCablingRoute') }}">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="mb-0">
                          <label class="form-label">Notes</label>
                          <textarea name="indoor[notes]" class="form-control form-control-sm" rows="2">{{ old('indoor.notes') }}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="5">
                <div class="card">
                  <div class="card-header">BoQ (Civils + NTE)</div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="fw-semibold mb-2">Civils</div>
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                              <tr>
                                <th>Description</th>
                                <th style="width: 90px;">Unit</th>
                                <th style="width: 90px;">Qty</th>
                              </tr>
                            </thead>
                            <tbody>
                              @php
                                $civilsDefaults = [
                                  ['description' => 'Fibre Cable', 'unit' => 'm'],
                                  ['description' => 'PVC Trunking', 'unit' => 'm'],
                                  ['description' => 'Manholes', 'unit' => 'ea'],
                                  ['description' => 'Trenching Normal Ground', 'unit' => 'm'],
                                  ['description' => 'Trenching Gravel', 'unit' => 'm'],
                                  ['description' => 'Total Trenching (HDPE Ducts)', 'unit' => 'm'],
                                  ['description' => 'Steel Pipes', 'unit' => 'm'],
                                  ['description' => 'PVC pipes (90mm)', 'unit' => 'm'],
                                  ['description' => 'Poles', 'unit' => 'ea'],
                                  ['description' => 'Tar', 'unit' => 'm'],
                                  ['description' => 'Length requiring Wayleaves', 'unit' => 'm'],
                                  ['description' => 'Concrete pavement', 'unit' => 'm'],
                                ];
                                $civilsOld = old('boq.civils', []);
                              @endphp
                              @foreach ($civilsDefaults as $idx => $row)
                                <tr>
                                  <td>
                                    <input type="hidden" name="boq[civils][{{ $idx }}][description]" value="{{ $row['description'] }}">
                                    <span class="fw-semibold">{{ $row['description'] }}</span>
                                  </td>
                                  <td>
                                    <input type="hidden" name="boq[civils][{{ $idx }}][unit]" value="{{ $row['unit'] }}">
                                    <span class="text-muted">{{ $row['unit'] }}</span>
                                  </td>
                                  <td>
                                    <input type="text" name="boq[civils][{{ $idx }}][qty]" class="form-control form-control-sm" value="{{ data_get($civilsOld, $idx . '.qty') }}">
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-lg-6 mt-3 mt-lg-0">
                        <div class="fw-semibold mb-2">NTE</div>
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                              <tr>
                                <th>Description</th>
                                <th style="width: 90px;">Unit</th>
                                <th style="width: 90px;">Qty</th>
                              </tr>
                            </thead>
                            <tbody>
                              @php
                                $nteDefaults = [
                                  ['description' => 'Converter', 'unit' => 'ea'],
                                  ['description' => 'SFPs', 'unit' => 'ea'],
                                  ['description' => 'UTP Cable', 'unit' => 'm'],
                                  ['description' => 'RJ45 Connectors', 'unit' => 'ea'],
                                  ['description' => 'Switch', 'unit' => 'ea'],
                                  ['description' => 'Access Points', 'unit' => 'ea'],
                                  ['description' => 'Patch cord', 'unit' => 'm'],
                                  ['description' => 'Patch panel', 'unit' => 'ea'],
                                  ['description' => 'Mid-couplers', 'unit' => 'ea'],
                                  ['description' => 'Connectors', 'unit' => 'ea'],
                                  ['description' => 'Pig tails', 'unit' => 'm'],
                                  ['description' => 'Splice Protectors', 'unit' => 'ea'],
                                  ['description' => 'Dome Boxes way', 'unit' => 'ea'],
                                  ['description' => 'Cabinet', 'unit' => 'ea'],
                                  ['description' => 'Fiber Termination Box', 'unit' => 'ea'],
                                ];
                                $nteOld = old('boq.nte', []);
                              @endphp
                              @foreach ($nteDefaults as $idx => $row)
                                <tr>
                                  <td>
                                    <input type="hidden" name="boq[nte][{{ $idx }}][description]" value="{{ $row['description'] }}">
                                    <span class="fw-semibold">{{ $row['description'] }}</span>
                                  </td>
                                  <td>
                                    <input type="hidden" name="boq[nte][{{ $idx }}][unit]" value="{{ $row['unit'] }}">
                                    <span class="text-muted">{{ $row['unit'] }}</span>
                                  </td>
                                  <td>
                                    <input type="text" name="boq[nte][{{ $idx }}][qty]" class="form-control form-control-sm" value="{{ data_get($nteOld, $idx . '.qty') }}">
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="6">
                <div class="card">
                  <div class="card-header">Photos</div>
                  <div class="card-body">
                    <div class="row">
                      @foreach ($photoLabels as $key => $label)
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">{{ $label }}</label>
                            <input type="file" name="photos[{{ $key }}][]" class="form-control form-control-sm" multiple accept="image/*,application/pdf">
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>

              <div class="survey-step d-none" data-step="7">
                <div class="card">
                  <div class="card-header">Overview</div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white">
                          <div class="text-muted small">Customer</div>
                          <div class="fw-bold" id="ccOvCustomer">-</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white">
                          <div class="text-muted small">Site</div>
                          <div class="fw-bold" id="ccOvSite">-</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white">
                          <div class="text-muted small">Account/JC</div>
                          <div class="fw-bold" id="ccOvAccount">-</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white">
                          <div class="text-muted small">Date</div>
                          <div class="fw-bold" id="ccOvDate">-</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white">
                          <div class="text-muted small">Performed By</div>
                          <div class="fw-bold" id="ccOvPerformedBy">-</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white">
                          <div class="text-muted small">Attachments</div>
                          <div class="fw-bold" id="ccOvPhotos">0</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between mt-3 cc-modal-footerbar">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="ccSurveyPrevBtn">
                  <i class="fas fa-chevron-left"></i> Back
                </button>
                <div class="d-flex align-items-center gap-2">
                  <div id="ccSurveyNavNextWrap">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="ccSurveyNextBtn">
                      Next <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <div id="ccSurveySubmitWrap" class="d-none">
                    <button type="button" class="btn btn-warning btn-sm" id="ccSurveySaveDraftBtn">
                      <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="ccSurveySubmitBtn">
                      <i class="fas fa-paper-plane"></i> Submit
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endcan
</section>
@endsection

@section('scripts')
<style>
  .cc-modal .modal-content { border-radius: 16px; overflow: hidden; }
  .cc-modal-header { background: var(--bs-primary); color: #fff; border-bottom: 0; }
  .cc-modal-subtitle { color: rgba(255,255,255,0.85); }
  .cc-modal-close { filter: invert(1); opacity: 0.9; }
  .cc-modal-body { background: #f7f9fc; }
  .cc-modal-top { background: #fff; border: 1px solid #eef2f7; border-radius: 14px; padding: 14px; }
  .cc-stepper .cc-step-btn { border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #111827; font-weight: 600; }
  .cc-stepper .cc-step-btn.is-active { background: rgba(10, 126, 164, 0.12); border-color: rgba(10, 126, 164, 0.35); color: #0a7ea4; }
  .cc-modal-footerbar { position: sticky; bottom: 0; background: rgba(247,249,252,0.95); backdrop-filter: blur(6px); border-top: 1px solid #e5e7eb; padding-top: 12px; padding-bottom: 12px; }
  .cc-modal .card { border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
  .cc-modal .card-header { border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff; }
  .cc-modal .form-control, .cc-modal .form-select { border-radius: 10px; }

  .cc-table td, .cc-table th { padding: 14px 16px; }
  .cc-thead th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
  .cc-table tbody tr { border-top: 1px solid #eef2f7; }

  @media (max-width: 768px) {
    #cc-surveys-list.cc-mobile-stack thead { display: none; }
    #cc-surveys-list.cc-mobile-stack,
    #cc-surveys-list.cc-mobile-stack tbody,
    #cc-surveys-list.cc-mobile-stack tr,
    #cc-surveys-list.cc-mobile-stack td { display: block; width: 100%; }

    #cc-surveys-list.cc-mobile-stack tr {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 12px;
      margin-bottom: 12px;
    }

    #cc-surveys-list.cc-mobile-stack tr.cc-empty-row {
      background: transparent;
      border: 0;
      padding: 0;
      margin: 0;
    }

    #cc-surveys-list.cc-mobile-stack td { border: 0; padding: 8px 0; }
    #cc-surveys-list.cc-mobile-stack td::before {
      content: attr(data-label);
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #6b7280;
      font-weight: 700;
      margin-bottom: 2px;
    }

    #cc-surveys-list.cc-mobile-stack .btn-group { display: flex; flex-wrap: wrap; gap: 8px; }
    #cc-surveys-list.cc-mobile-stack .btn-group .btn { flex: 1 1 auto; }
    #cc-surveys-list.cc-mobile-stack td.text-nowrap { white-space: normal !important; }
  }

  @media (max-width: 768px) {
    .cc-modal .modal-content { border-radius: 0; min-height: 100vh; }
    .cc-modal .modal-body { padding: 12px; }
    .cc-modal-header { position: sticky; top: 0; z-index: 1; }
    .cc-modal-top { padding: 12px; }
    #ccSurveyStepTitle { font-size: 14px; }

    .cc-stepper { flex-wrap: nowrap !important; overflow-x: auto; padding-bottom: 6px; }
    .cc-stepper::-webkit-scrollbar { height: 0; }
    .cc-stepper .cc-step-btn { white-space: nowrap; flex: 0 0 auto; }

    .cc-modal-footerbar { flex-direction: column; align-items: stretch !important; gap: 10px; }
    #ccSurveyPrevBtn { width: 100%; }
    #ccSurveyNavNextWrap, #ccSurveySubmitWrap { width: 100%; }
    #ccSurveyNavNextWrap .btn, #ccSurveySubmitWrap .btn { width: 100%; }
    #ccSurveySubmitWrap { display: flex; flex-direction: column; gap: 10px; }
  }
</style>
<script>
  (function () {
    var per = document.getElementById('ccSurveysPageSize');
    var perInput = document.getElementById('ccPerPageInput');
    if (per && perInput) {
      per.addEventListener('change', function () {
        perInput.value = per.value;
        var form = per.closest('.filter-toolbar') ? per.closest('.filter-toolbar').querySelector('form') : null;
        if (form) form.submit();
      });
    }
  })();
</script>
<script>
  (function () {
    var modalEl = document.getElementById('ccSurveyCreateModal');
    if (!modalEl) return;

    var titles = [
      'General',
      'Service Requirements',
      'Site Access & Permissions',
      'Outdoor Connectivity',
      'Indoor Assessment',
      'BoQ (Civils + NTE)',
      'Photos',
      'Overview'
    ];

    var steps = Array.prototype.slice.call(modalEl.querySelectorAll('.survey-step'));
    var current = 0;

    var stepTitle = document.getElementById('ccSurveyStepTitle');
    var stepNo = document.getElementById('ccSurveyStepNo');
    var stepTotal = document.getElementById('ccSurveyStepTotal');
    var progressBar = document.getElementById('ccSurveyProgressBar');
    var prevBtn = document.getElementById('ccSurveyPrevBtn');
    var nextBtn = document.getElementById('ccSurveyNextBtn');
    var stepNav = document.getElementById('ccStepNav');
    var stepBtns = stepNav ? Array.prototype.slice.call(stepNav.querySelectorAll('.cc-step-btn')) : [];
    var nextWrap = document.getElementById('ccSurveyNavNextWrap');
    var submitWrap = document.getElementById('ccSurveySubmitWrap');
    var formEl = document.getElementById('ccSurveyForm');

    function syncCoordinates() {
      if (!formEl) return;
      var latEl = formEl.querySelector('.cc-lat');
      var lngEl = formEl.querySelector('.cc-lng');
      var coordsEl = formEl.querySelector('.cc-coords');
      if (!coordsEl) return;
      var lat = latEl ? (latEl.value || '').trim() : '';
      var lng = lngEl ? (lngEl.value || '').trim() : '';
      if (lat !== '' && lng !== '') coordsEl.value = lat + ', ' + lng;
    }

    if (formEl) {
      var latEl = formEl.querySelector('.cc-lat');
      var lngEl = formEl.querySelector('.cc-lng');
      if (latEl) latEl.addEventListener('input', syncCoordinates);
      if (lngEl) lngEl.addEventListener('input', syncCoordinates);
    }

    function countSelectedFiles() {
      var total = 0;
      modalEl.querySelectorAll('input[type="file"]').forEach(function (inp) {
        try { total += (inp.files ? inp.files.length : 0); } catch (e) {}
      });
      return total;
    }

    function fillOverview() {
      var get = function (sel) {
        var el = modalEl.querySelector(sel);
        return el ? (el.value || '').trim() : '';
      };
      var setText = function (id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val && String(val).trim() !== '' ? String(val) : '-';
      };

      setText('ccOvCustomer', get('input[name="general[customerName]"]'));
      setText('ccOvSite', get('input[name="general[siteName]"]'));
      setText('ccOvAccount', get('input[name="general[accountOrJcNumber]"]'));
      setText('ccOvDate', get('input[name="meta[date]"]'));
      setText('ccOvPerformedBy', get('input[name="meta[surveyPerformedBy]"]'));

      var photos = countSelectedFiles();
      var photosEl = document.getElementById('ccOvPhotos');
      if (photosEl) photosEl.textContent = String(photos);
    }

    if (stepTotal) stepTotal.textContent = String(steps.length);

    function goTo(idx) {
      current = Math.max(0, Math.min(steps.length - 1, parseInt(idx, 10) || 0));
      render();
      var body = modalEl.querySelector('.modal-body');
      if (body && body.scrollTo) body.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function render() {
      steps.forEach(function (el, i) {
        if (i === current) el.classList.remove('d-none');
        else el.classList.add('d-none');
      });
      if (stepTitle) stepTitle.textContent = titles[current] || ('Step ' + String(current + 1));
      if (stepNo) stepNo.textContent = String(current + 1);
      var pct = Math.round(((current + 1) / steps.length) * 100);
      if (progressBar) progressBar.style.width = String(pct) + '%';
      if (prevBtn) prevBtn.disabled = current === 0;
      if (nextBtn) nextBtn.disabled = current === steps.length - 1;
      stepBtns.forEach(function (b) {
        var s = parseInt(b.getAttribute('data-step') || '0', 10) || 0;
        if (s === current) b.classList.add('is-active');
        else b.classList.remove('is-active');
      });

      var isLast = current === steps.length - 1;
      if (nextWrap) nextWrap.classList.toggle('d-none', isLast);
      if (submitWrap) submitWrap.classList.toggle('d-none', !isLast);
      if (isLast) fillOverview();
    }

    function findFirstInvalidControl() {
      if (!formEl) return null;
      try {
        return formEl.querySelector(':invalid');
      } catch (e) {
      }
      var controls = Array.prototype.slice.call(formEl.querySelectorAll('input, select, textarea'));
      for (var i = 0; i < controls.length; i++) {
        var c = controls[i];
        if (c && typeof c.checkValidity === 'function' && !c.checkValidity()) return c;
      }
      return null;
    }

    if (formEl) {
      formEl.addEventListener('submit', function (e) {
        syncCoordinates();
        if (typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
          var invalid = findFirstInvalidControl();
          if (invalid) {
            var stepEl = invalid.closest ? invalid.closest('.survey-step') : null;
            if (stepEl) {
              var idx = parseInt(stepEl.getAttribute('data-step') || '0', 10) || 0;
              goTo(idx);
            } else {
              goTo(0);
            }
            setTimeout(function () {
              try {
                if (typeof invalid.reportValidity === 'function') invalid.reportValidity();
                else invalid.focus();
              } catch (err) {
              }
            }, 50);
          } else {
            goTo(0);
          }
        }
      });
    }

    var statusEl = document.getElementById('ccSurveyStatus');
    var saveDraftBtn = document.getElementById('ccSurveySaveDraftBtn');
    var submitBtn = document.getElementById('ccSurveySubmitBtn');

    function setSubmitRequired(on) {
      if (!formEl) return;
      var req = [
        'input[name="general[customerName]"]',
        'input[name="general[siteName]"]'
      ];
      req.forEach(function (sel) {
        var el = formEl.querySelector(sel);
        if (!el) return;
        if (on) el.setAttribute('required', 'required');
        else el.removeAttribute('required');
      });
    }

    function submitWithStatus(val) {
      if (statusEl) statusEl.value = val;
      var isSubmit = val === 'submitted';
      setSubmitRequired(isSubmit);
      syncCoordinates();

      if (isSubmit && formEl && typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
        var invalid = findFirstInvalidControl();
        if (invalid) {
          var stepEl = invalid.closest ? invalid.closest('.survey-step') : null;
          if (stepEl) {
            var idx = parseInt(stepEl.getAttribute('data-step') || '0', 10) || 0;
            goTo(idx);
          } else {
            goTo(0);
          }
          setTimeout(function () {
            try {
              if (typeof invalid.reportValidity === 'function') invalid.reportValidity();
              else invalid.focus();
            } catch (err) {
            }
          }, 50);
        } else {
          goTo(0);
        }
        return;
      }

      if (formEl) formEl.submit();
    }
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', function () { submitWithStatus('draft'); });
    if (submitBtn) submitBtn.addEventListener('click', function () { submitWithStatus('submitted'); });

    if (prevBtn) prevBtn.addEventListener('click', function () {
      goTo(current - 1);
    });

    if (nextBtn) nextBtn.addEventListener('click', function () {
      goTo(current + 1);
    });

    stepBtns.forEach(function (b) {
      b.addEventListener('click', function () {
        goTo(b.getAttribute('data-step'));
      });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      current = 0;
      render();
    });

    render();

    @if ($errors->any())
      try {
        var m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
      } catch (e) {}
    @endif
  })();
</script>
@endsection
