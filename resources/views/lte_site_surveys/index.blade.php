@extends('layouts.admin')

@section('title')
LTE Site Surveys
@endsection

@section('pageName')
LTE Site Surveys
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
      <a href="{{ route('lte-site-surveys.index', array_filter(['q' => $q, 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#6c757d"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-secondary"><i class="fas fa-clipboard-list"></i></span>
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
      <a href="{{ route('lte-site-surveys.index', array_filter(['q' => $q, 'status' => 'draft', 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#ffc107"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-warning text-dark"><i class="fas fa-pen"></i></span>
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
      <a href="{{ route('lte-site-surveys.index', array_filter(['q' => $q, 'status' => 'submitted', 'per_page' => $perPage])) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background:#20c997"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-success"><i class="fas fa-check-circle"></i></span>
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
      <a href="{{ route('lte-site-surveys.index', array_filter(['q' => $q, 'per_page' => $perPage])) }}" class="text-decoration-none">
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
          <h3 class="card-title mb-0">Manage and track LTE site surveys</h3>
        </div>
      </div>
      <div class="card-tools">
        <a href="{{ route('lte-site-surveys.map') }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="fas fa-map-marked-alt"></i> Map
        </a>
        <a href="{{ route('lte-site-surveys.reports') }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="fas fa-chart-bar"></i> Reports
        </a>
        @can('survey-create')
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#lteSiteSurveyCreateModal">
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
            <select id="lteSurveysPageSize" class="form-select form-select-sm" style="width:auto;">
              <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
              <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
              <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
              <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
            </select>
          </div>
          <form method="GET" action="{{ route('lte-site-surveys.index') }}" class="m-0">
            <div class="input-group input-group-sm" style="width: 760px; max-width: 100%;">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
              </div>
              <select name="status" id="lteSurveysStatusFilter" class="form-select form-select-sm" style="width:auto;">
                <option value="" {{ $status === '' ? 'selected' : '' }}>All</option>
                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Submitted</option>
              </select>
              <span class="input-group-text ms-2"><i class="fas fa-search"></i></span>
              <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search all records">
              <input type="hidden" name="per_page" value="{{ $perPage }}">
              <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
              <a href="{{ route('lte-site-surveys.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
            </div>
          </form>
        </div>

        <table class="table table-hover align-middle lte-mobile-stack" id="lte-site-surveys-list" style="font-size:14px">
          <thead>
            <tr>
              <th>No.</th>
              <th>Site</th>
              <th>JC</th>
              <th>Status</th>
              <th>Photos</th>
              <th>Captured By</th>
              <th>Created</th>
              <th>Action(s)</th>
            </tr>
          </thead>
          <tbody>
            @forelse($surveys as $s)
              <tr>
                <td class="text-muted" data-label="No.">{{ ++$i }}</td>
                <td data-label="Site">
                  <div class="fw-semibold">{{ $s->site_name ?: 'Untitled' }}</div>
                  <div class="text-muted small">
                    <span>{{ $s->province_region ?: 'No region' }}</span>
                    <span class="mx-1">•</span>
                    <span>{{ $s->coordinates ?: 'No coordinates' }}</span>
                  </div>
                </td>
                <td class="text-muted" data-label="JC">{{ $s->jc_number ?: '-' }}</td>
                <td class="text-nowrap" data-label="Status">
                  @if($s->status === 'submitted')
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Submitted</span>
                  @else
                    <span class="badge bg-warning text-dark"><i class="fas fa-pen me-1"></i> Draft</span>
                  @endif
                </td>
                <td data-label="Photos"><span class="badge bg-light text-dark border">{{ (int)($s->photos_count ?? 0) }}</span></td>
                <td data-label="Captured By">
                  <div class="fw-semibold">{{ optional($s->user)->name ?: '-' }}</div>
                  <div class="text-muted small">{{ $s->survey_performed_by ?: '—' }}</div>
                </td>
                <td class="text-nowrap" data-label="Created">{{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('j F Y h:i a') : '-' }}</td>
                <td class="text-nowrap" data-label="Actions">
                  <div class="btn-group btn-group gap-2" role="group" aria-label="Actions">
                    @if($s->status === 'submitted')
                      <a class="btn btn-outline-danger btn-sm" href="{{ route('lte-site-surveys.show', $s->id) }}">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                      </a>
                    @endif
                    @can('survey-edit')
                      <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#lteSurveyEditModal-{{ $s->id }}">
                        <i class="fas fa-edit me-1"></i> Edit
                      </button>
                    @endcan
                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#lteSurveyViewModal-{{ $s->id }}">
                      <i class="fas fa-eye me-1"></i> View
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr class="lte-empty-row">
                <td colspan="8" class="text-center text-muted py-5">No LTE site surveys to display</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">
            Showing {{ $surveys->firstItem() ?? 0 }} to {{ $surveys->lastItem() ?? 0 }} of {{ $surveys->total() }} results
          </small>
          {{ $surveys->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>

  @foreach($surveys as $survey)
    @include('lte_site_surveys.viewModal', ['survey' => $survey, 'photoLabels' => $photoLabels, 'remarks' => ($remarksBySurvey[$survey->id] ?? collect())])
    @can('survey-edit')
      @include('lte_site_surveys.editModal', ['survey' => $survey, 'users' => $users, 'materials' => $materials, 'photoLabels' => $photoLabels])
    @endcan
  @endforeach

  @can('survey-create')
    <div class="modal fade lte-modal" id="lteSiteSurveyCreateModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header lte-modal-header">
            <div>
              <h5 class="modal-title mb-0">LTE Site Survey Sheet</h5>
              <div class="lte-modal-subtitle small">Progressive form (step-by-step)</div>
            </div>
            <button type="button" class="btn-close lte-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body lte-modal-body">
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
          <div class="mb-3 lte-modal-top">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold" id="lteSurveyStepTitle">General Site Information</div>
              <div class="text-muted small"><span id="lteSurveyStepNo">1</span> / <span id="lteSurveyStepTotal">10</span></div>
            </div>
            <div class="progress mt-2" style="height: 8px;">
              <div id="lteSurveyProgressBar" class="progress-bar" role="progressbar" style="width: 10%"></div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 lte-stepper" id="lteStepNav">
              <button type="button" class="btn btn-sm lte-step-btn" data-step="0">1. General</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="1">2. Coordinates</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="2">3. Access/Tower</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="3">4. Transmission</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="4">5. Power</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="5">6. Civil</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="6">7. Materials</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="7">8. Notes</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="8">9. Images</button>
              <button type="button" class="btn btn-sm lte-step-btn" data-step="9">10. Overview</button>
            </div>
          </div>

          <form method="POST" action="{{ route('lte-site-surveys.store') }}" enctype="multipart/form-data" id="lteSiteSurveyForm">
            @csrf
            <input type="hidden" name="status" id="lteSurveyStatus" value="draft">

            <div class="survey-step" data-step="0">
              <div class="row">
                <div class="col-md-8">
                  <div class="mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="general[siteName]" class="form-control form-control-sm" value="{{ old('general.siteName') }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">JC Number</label>
                    <input type="text" name="general[jcNumber]" class="form-control form-control-sm" value="{{ old('general.jcNumber') }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Province/Region</label>
                    <input type="text" name="general[provinceRegion]" class="form-control form-control-sm" value="{{ old('general.provinceRegion') }}">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="meta[date]" class="form-control form-control-sm" value="{{ old('meta.date') }}">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Survey Performed By</label>
                    <select name="meta[surveyPerformedByUserId]" class="form-select form-select-sm js-select2" data-placeholder="Select user" required>
                      <option value=""></option>
                      @foreach(($users ?? []) as $u)
                        <option value="{{ $u->id }}" {{ (int)old('meta.surveyPerformedByUserId', optional(auth()->user())->id) === (int)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Physical Address</label>
                    <textarea name="general[physicalAddress]" class="form-control form-control-sm" rows="4">{{ old('general.physicalAddress') }}</textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Contact Details</label>
                    <textarea name="general[contactDetails]" class="form-control form-control-sm" rows="4">{{ old('general.contactDetails') }}</textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="1">
              <div class="card">
                <div class="card-header">Coordinates</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Site Latitude</label>
                        <input type="text" name="general[latitude]" class="form-control form-control-sm" value="{{ old('general.latitude') }}" placeholder="-17.8292">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Site Longitude</label>
                        <input type="text" name="general[longitude]" class="form-control form-control-sm" value="{{ old('general.longitude') }}" placeholder="31.0522">
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="general[coordinates]" value="{{ old('general.coordinates') }}">
                  <div class="alert alert-info mb-0">Capture latitude and longitude for accurate site location.</div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="2">
              <div class="row">
                <div class="col-md-6">
                  <div class="card mb-3">
                    <div class="card-header">Site Access and Security</div>
                    <div class="card-body">
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[securityFenceAvailable]" value="1" id="lteSecurityFenceAvailable" {{ old('accessSecurity.securityFenceAvailable') ? 'checked' : '' }}>
                        <label class="form-check-label" for="lteSecurityFenceAvailable">Security Fence Available</label>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Condition of Fence</label>
                        <select class="form-select form-select-sm" name="accessSecurity[conditionOfFence]">
                          <option value="">Select</option>
                          <option value="good" {{ old('accessSecurity.conditionOfFence') === 'good' ? 'selected' : '' }}>Available (Good)</option>
                          <option value="bad" {{ old('accessSecurity.conditionOfFence') === 'bad' ? 'selected' : '' }}>Available (Bad)</option>
                          <option value="not_available" {{ old('accessSecurity.conditionOfFence') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>

                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[siteAccess24h]" value="1" id="lteSiteAccess24h" {{ old('accessSecurity.siteAccess24h') ? 'checked' : '' }}>
                        <label class="form-check-label" for="lteSiteAccess24h">24 Hour Site Access</label>
                      </div>

                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[guardAvailable]" value="1" id="lteGuardAvailable" {{ old('accessSecurity.guardAvailable') ? 'checked' : '' }}>
                        <label class="form-check-label" for="lteGuardAvailable">Guard Available</label>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[lineOfSightAvailability]" value="1" id="lteLineOfSightAvailability" {{ old('accessSecurity.lineOfSightAvailability') ? 'checked' : '' }}>
                        <label class="form-check-label" for="lteLineOfSightAvailability">Line of sight Availability</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="card mb-3">
                    <div class="card-header">Tower / Structural Details</div>
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">Terrain Type</label>
                        <select class="form-select form-select-sm" name="tower[terrainType]">
                          <option value="">Select</option>
                          <option value="hilltop" {{ old('tower.terrainType') === 'hilltop' ? 'selected' : '' }}>Hilltop</option>
                          <option value="elevated_ground" {{ old('tower.terrainType') === 'elevated_ground' ? 'selected' : '' }}>Elevated Ground</option>
                          <option value="flat_terrain" {{ old('tower.terrainType') === 'flat_terrain' ? 'selected' : '' }}>Flat Terrain</option>
                          <option value="valley" {{ old('tower.terrainType') === 'valley' ? 'selected' : '' }}>Valley</option>
                          <option value="mountain_slope" {{ old('tower.terrainType') === 'mountain_slope' ? 'selected' : '' }}>Mountain Slope</option>
                          <option value="urban_rooftop" {{ old('tower.terrainType') === 'urban_rooftop' ? 'selected' : '' }}>Urban Rooftop</option>
                          <option value="other" {{ old('tower.terrainType') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Tower Owner</label>
                        <input type="text" name="tower[towerOwner]" class="form-control form-control-sm" value="{{ old('tower.towerOwner') }}">
                      </div>
                      <div class="mb-0">
                        <label class="form-label">Allocated Height</label>
                        <input type="text" name="tower[allocatedHeight]" class="form-control form-control-sm" value="{{ old('tower.allocatedHeight') }}" placeholder="e.g. 30m">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="3">
              <div class="card">
                <div class="card-header">Transmission Details</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Coordinates of nearest manhole</label>
                        <input type="text" name="transmission[nearestManholeCoordinates]" class="form-control form-control-sm" value="{{ old('transmission.nearestManholeCoordinates') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Distance from existing fibre</label>
                        <input type="text" name="transmission[distanceFromExistingFibre]" class="form-control form-control-sm" value="{{ old('transmission.distanceFromExistingFibre') }}">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Distance from nearest POP</label>
                        <input type="text" name="transmission[distanceFromNearestPop]" class="form-control form-control-sm" value="{{ old('transmission.distanceFromNearestPop') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Distance from nearest POP (alternative)</label>
                        <input type="text" name="transmission[distanceFromNearestPop2]" class="form-control form-control-sm" value="{{ old('transmission.distanceFromNearestPop2') }}">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Allocated Port</label>
                        <input type="text" name="transmission[allocatedPort]" class="form-control form-control-sm" value="{{ old('transmission.allocatedPort') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Required Backhaul Capacity</label>
                        <input type="text" name="transmission[requiredBackhaulCapacity]" class="form-control form-control-sm" value="{{ old('transmission.requiredBackhaulCapacity') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-0">
                        <label class="form-label">Backhaul type</label>
                        <select class="form-select form-select-sm" name="transmission[backhaulType]">
                          <option value="">Select</option>
                          <option value="fibre" {{ old('transmission.backhaulType') === 'fibre' ? 'selected' : '' }}>Fibre</option>
                          <option value="microwave" {{ old('transmission.backhaulType') === 'microwave' ? 'selected' : '' }}>Microwave</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="4">
              <div class="card">
                <div class="card-header">Power Details</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Power Source Type</label>
                        <select class="form-select form-select-sm" name="power[powerSourceType]">
                          <option value="">Select</option>
                          <option value="zesa" {{ old('power.powerSourceType') === 'zesa' ? 'selected' : '' }}>ZESA</option>
                          <option value="generator" {{ old('power.powerSourceType') === 'generator' ? 'selected' : '' }}>Generator</option>
                          <option value="solar" {{ old('power.powerSourceType') === 'solar' ? 'selected' : '' }}>Solar</option>
                          <option value="other" {{ old('power.powerSourceType') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Single/Three Phase</label>
                        <select class="form-select form-select-sm" name="power[phase]">
                          <option value="">Select</option>
                          <option value="single_phase" {{ old('power.phase') === 'single_phase' ? 'selected' : '' }}>Single Phase</option>
                          <option value="three_phase" {{ old('power.phase') === 'three_phase' ? 'selected' : '' }}>Three Phase</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Input Voltage</label>
                        <input type="text" name="power[inputVoltage]" class="form-control form-control-sm" value="{{ old('power.inputVoltage') }}">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Battery Capacity</label>
                        <input type="text" name="power[batteryCapacity]" class="form-control form-control-sm" value="{{ old('power.batteryCapacity') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Battery Autonomy (hrs)</label>
                        <input type="text" name="power[batteryAutonomyHrs]" class="form-control form-control-sm" value="{{ old('power.batteryAutonomyHrs') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Earthing System Installed</label>
                        <select class="form-select form-select-sm" name="power[earthingSystemInstalled]">
                          <option value="">Select</option>
                          <option value="available" {{ old('power.earthingSystemInstalled') === 'available' ? 'selected' : '' }}>Available</option>
                          <option value="not_available" {{ old('power.earthingSystemInstalled') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Cable from Utility Source to Site</label>
                        <select class="form-select form-select-sm" name="power[cableUtilitySourceToSite]">
                          <option value="">Select</option>
                          <option value="available" {{ old('power.cableUtilitySourceToSite') === 'available' ? 'selected' : '' }}>Available</option>
                          <option value="not_available" {{ old('power.cableUtilitySourceToSite') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-0">
                        <label class="form-label">Condition of DB</label>
                        <select class="form-select form-select-sm" name="power[conditionOfDb]">
                          <option value="">Select</option>
                          <option value="good" {{ old('power.conditionOfDb') === 'good' ? 'selected' : '' }}>Available (Good)</option>
                          <option value="bad" {{ old('power.conditionOfDb') === 'bad' ? 'selected' : '' }}>Available (Bad)</option>
                          <option value="not_available" {{ old('power.conditionOfDb') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="5">
              <div class="card">
                <div class="card-header">Civil Works Requirement</div>
                <div class="card-body">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="civilWorks[trenchingRequired]" value="1" id="lteTrenchingRequired" {{ old('civilWorks.trenchingRequired') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lteTrenchingRequired">Trenching Required</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="civilWorks[breakingConcreteTar]" value="1" id="lteBreakingConcreteTar" {{ old('civilWorks.breakingConcreteTar') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lteBreakingConcreteTar">Breaking Concrete/Tar</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="civilWorks[polePlantingRequired]" value="1" id="ltePolePlantingRequired" {{ old('civilWorks.polePlantingRequired') ? 'checked' : '' }}>
                    <label class="form-check-label" for="ltePolePlantingRequired">Pole Planting Required</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="civilWorks[constructionOfPlinth]" value="1" id="lteConstructionOfPlinth" {{ old('civilWorks.constructionOfPlinth') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lteConstructionOfPlinth">Construction of Plinth</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="civilWorks[newManholeRequired]" value="1" id="lteNewManholeRequired" {{ old('civilWorks.newManholeRequired') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lteNewManholeRequired">New Manhole Required</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="6">
              <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header">Civils</div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table table-sm mb-0 lte-mat-table">
                          <thead>
                            <tr>
                              <th>Description</th>
                              <th style="width: 70px;">Unit</th>
                              <th style="width: 90px;">Qty</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach(($materials['civils'] ?? []) as $i => $row)
                              <tr>
                                <td data-label="Description">
                                  <input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][description]" value="{{ old('materials.civils.' . $i . '.description', $row['description'] ?? '') }}">
                                </td>
                                <td data-label="Unit">
                                  <input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][unit]" value="{{ old('materials.civils.' . $i . '.unit', $row['unit'] ?? '') }}">
                                </td>
                                <td data-label="Qty">
                                  <input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][qty]" value="{{ old('materials.civils.' . $i . '.qty', $row['qty'] ?? '') }}">
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header">NTE</div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table table-sm mb-0 lte-mat-table">
                          <thead>
                            <tr>
                              <th>Description</th>
                              <th style="width: 70px;">Unit</th>
                              <th style="width: 90px;">Qty</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach(($materials['nte'] ?? []) as $i => $row)
                              <tr>
                                <td data-label="Description">
                                  <input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][description]" value="{{ old('materials.nte.' . $i . '.description', $row['description'] ?? '') }}">
                                </td>
                                <td data-label="Unit">
                                  <input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][unit]" value="{{ old('materials.nte.' . $i . '.unit', $row['unit'] ?? '') }}">
                                </td>
                                <td data-label="Qty">
                                  <input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][qty]" value="{{ old('materials.nte.' . $i . '.qty', $row['qty'] ?? '') }}">
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

            <div class="survey-step d-none" data-step="7">
              <div class="card">
                <div class="card-header">Notes (Optional)</div>
                <div class="card-body">
                  <div class="mb-0">
                    <label class="form-label">Notes</label>
                    <textarea name="notes[notes]" class="form-control form-control-sm" rows="5" placeholder="Any additional notes...">{{ old('notes.notes') }}</textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="8">
              <div class="card">
                <div class="card-header">Images & Attachments</div>
                <div class="card-body">
                  <div class="row">
                    @foreach(($photoLabels ?? []) as $key => $label)
                      <div class="col-md-6 mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="file" name="photos[{{ $key }}][]" class="form-control form-control-sm" accept="image/*,application/pdf" multiple>
                      </div>
                    @endforeach
                  </div>
                  <div class="alert alert-info mb-0">
                    Upload as many photos as needed per section.
                  </div>
                </div>
              </div>
            </div>

            <div class="survey-step d-none" data-step="9">
              <div class="card">
                <div class="card-header">Overview</div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <div class="card mb-0">
                        <div class="card-header">Site Summary</div>
                        <div class="card-body">
                          <div class="d-flex justify-content-between"><span class="text-muted">Site Name</span><span class="fw-semibold" id="lteOvSiteName">-</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">JC Number</span><span class="fw-semibold" id="lteOvJcNumber">-</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">Province/Region</span><span class="fw-semibold" id="lteOvProvince">-</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">Latitude</span><span class="fw-semibold" id="lteOvLat">-</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">Longitude</span><span class="fw-semibold" id="lteOvLng">-</span></div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card mb-0">
                        <div class="card-header">Meta</div>
                        <div class="card-body">
                          <div class="d-flex justify-content-between"><span class="text-muted">Date</span><span class="fw-semibold" id="lteOvDate">-</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">Performed By</span><span class="fw-semibold" id="lteOvPerformedBy">-</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">Photos Selected</span><span class="fw-semibold" id="lteOvPhotos">0</span></div>
                          <div class="d-flex justify-content-between mt-2"><span class="text-muted">Notes</span><span class="fw-semibold" id="lteOvNotes">-</span></div>
                        </div>
                      </div>
                      <div class="alert alert-info mt-3 mb-0">Review the details before you submit.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-3 lte-modal-footerbar">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="lteSurveyPrevBtn">
                <i class="fas fa-chevron-left"></i> Back
              </button>
              <div class="d-flex align-items-center gap-2">
                <div id="lteSurveyNavNextWrap">
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="lteSurveyNextBtn">
                    Next <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
                <div id="lteSurveySubmitWrap" class="d-none">
                  <button type="button" class="btn btn-warning btn-sm" id="lteSurveySaveDraftBtn">
                    <i class="fas fa-save"></i> Save Draft
                  </button>
                  <button type="button" class="btn btn-primary btn-sm" id="lteSurveySubmitBtn">
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
  .lte-modal .modal-content { border-radius: 16px; overflow: hidden; }
  .lte-modal-header { background: var(--bs-primary); color: #fff; border-bottom: 0; }
  .lte-modal-subtitle { color: rgba(255,255,255,0.85); }
  .lte-modal-close { filter: invert(1); opacity: 0.9; }
  .lte-modal-body { background: #f7f9fc; }
  .lte-modal-top { background: #fff; border: 1px solid #eef2f7; border-radius: 14px; padding: 14px; }
  .lte-stepper .lte-step-btn { border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #111827; font-weight: 600; }
  .lte-stepper .lte-step-btn.is-active { background: rgba(10, 126, 164, 0.12); border-color: rgba(10, 126, 164, 0.35); color: #0a7ea4; }
  .lte-modal-footerbar { position: sticky; bottom: 0; background: rgba(247,249,252,0.95); backdrop-filter: blur(6px); border-top: 1px solid #e5e7eb; padding-top: 12px; padding-bottom: 12px; }
  .lte-modal .card { border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
  .lte-modal .card-header { border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff; }
  .lte-modal .form-control, .lte-modal .form-select { border-radius: 10px; }

  .lte-badge-brand { background: rgba(10, 126, 164, 0.12); color: #0a7ea4; border: 1px solid rgba(10, 126, 164, 0.25); }
  .lte-btn-primary { background: #0a7ea4; border-color: #0a7ea4; }
  .lte-btn-primary:hover { background: #086a8a; border-color: #086a8a; }
  .lte-stat .card-body { padding: 14px 16px; }
  .lte-stat-value { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.1; }
  .lte-stat-value-sm { font-size: 18px; }
  .lte-table td, .lte-table th { padding: 14px 16px; }
  .lte-thead th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
  .lte-table tbody tr { border-top: 1px solid #eef2f7; }
  .lte-avatar { width: 30px; height: 30px; border-radius: 999px; background: #eaf2ff; color: #1d4ed8; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; border: 1px solid #dbeafe; flex: 0 0 auto; }

  @media (max-width: 768px) {
    #lte-site-surveys-list.lte-mobile-stack thead { display: none; }
    #lte-site-surveys-list.lte-mobile-stack,
    #lte-site-surveys-list.lte-mobile-stack tbody,
    #lte-site-surveys-list.lte-mobile-stack tr,
    #lte-site-surveys-list.lte-mobile-stack td { display: block; width: 100%; }

    #lte-site-surveys-list.lte-mobile-stack tr {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 12px;
      margin-bottom: 12px;
    }

    #lte-site-surveys-list.lte-mobile-stack tr.lte-empty-row {
      background: transparent;
      border: 0;
      padding: 0;
      margin: 0;
    }

    #lte-site-surveys-list.lte-mobile-stack td { border: 0; padding: 8px 0; }
    #lte-site-surveys-list.lte-mobile-stack td::before {
      content: attr(data-label);
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #6b7280;
      font-weight: 700;
      margin-bottom: 2px;
    }

    #lte-site-surveys-list.lte-mobile-stack .btn-group { display: flex; flex-wrap: wrap; gap: 8px; }
    #lte-site-surveys-list.lte-mobile-stack .btn-group .btn { flex: 1 1 auto; }
    #lte-site-surveys-list.lte-mobile-stack td.text-nowrap { white-space: normal !important; }
  }

  @media (max-width: 768px) {
    .lte-modal .modal-content { border-radius: 0; min-height: 100vh; }
    .lte-modal .modal-body { padding: 12px; }
    .lte-modal-header { position: sticky; top: 0; z-index: 1; }
    .lte-modal-top { padding: 12px; }
    #lteSurveyStepTitle { font-size: 14px; }

    .lte-stepper { flex-wrap: nowrap !important; overflow-x: auto; padding-bottom: 6px; }
    .lte-stepper::-webkit-scrollbar { height: 0; }
    .lte-stepper .lte-step-btn { white-space: nowrap; flex: 0 0 auto; }

    .lte-modal-footerbar { flex-direction: column; align-items: stretch !important; gap: 10px; }
    #lteSurveyPrevBtn { width: 100%; }
    #lteSurveyNavNextWrap, #lteSurveySubmitWrap { width: 100%; }
    #lteSurveyNavNextWrap .btn, #lteSurveySubmitWrap .btn { width: 100%; }
    #lteSurveySubmitWrap { display: flex; flex-direction: column; gap: 10px; }
  }

  @media (max-width: 768px) {
    .lte-mat-table thead { display: none; }
    .lte-mat-table,
    .lte-mat-table tbody,
    .lte-mat-table tr,
    .lte-mat-table td { display: block; width: 100%; }

    .lte-mat-table tr {
      border-top: 1px solid #e5e7eb;
      padding: 10px 12px;
    }

    .lte-mat-table td { border: 0; padding: 8px 0; }
    .lte-mat-table td::before {
      content: attr(data-label);
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #6b7280;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .lte-mat-table .form-control { width: 100%; }
  }
</style>
<script>
  (function () {
    var modalEl = document.getElementById('lteSiteSurveyCreateModal');
    if (!modalEl) return;

    var titles = [
      'General Site Information',
      'Coordinates',
      'Site Access + Tower Details',
      'Transmission Details',
      'Power Details',
      'Civil Works Requirement',
      'Materials (Civils + NTE)',
      'Notes (Optional)',
      'Images & Attachments',
      'Overview'
    ];

    var steps = Array.prototype.slice.call(modalEl.querySelectorAll('.survey-step'));
    var current = 0;

    var stepTitle = document.getElementById('lteSurveyStepTitle');
    var stepNo = document.getElementById('lteSurveyStepNo');
    var stepTotal = document.getElementById('lteSurveyStepTotal');
    var progressBar = document.getElementById('lteSurveyProgressBar');
    var prevBtn = document.getElementById('lteSurveyPrevBtn');
    var nextBtn = document.getElementById('lteSurveyNextBtn');
    var stepNav = document.getElementById('lteStepNav');
    var stepBtns = stepNav ? Array.prototype.slice.call(stepNav.querySelectorAll('.lte-step-btn')) : [];
    var nextWrap = document.getElementById('lteSurveyNavNextWrap');
    var submitWrap = document.getElementById('lteSurveySubmitWrap');
    var formEl = document.getElementById('lteSiteSurveyForm');

    var latEl = modalEl.querySelector('input[name="general[latitude]"]');
    var lngEl = modalEl.querySelector('input[name="general[longitude]"]');
    var coordsEl = modalEl.querySelector('input[name="general[coordinates]"]');

    function syncCoordinates() {
      if (!coordsEl) return;
      var lat = latEl ? (latEl.value || '').trim() : '';
      var lng = lngEl ? (lngEl.value || '').trim() : '';
      if (lat !== '' && lng !== '') coordsEl.value = lat + ', ' + lng;
    }
    if (latEl) latEl.addEventListener('input', syncCoordinates);
    if (lngEl) lngEl.addEventListener('input', syncCoordinates);

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

      setText('lteOvSiteName', get('input[name="general[siteName]"]'));
      setText('lteOvJcNumber', get('input[name="general[jcNumber]"]'));
      setText('lteOvProvince', get('input[name="general[provinceRegion]"]'));
      setText('lteOvLat', get('input[name="general[latitude]"]'));
      setText('lteOvLng', get('input[name="general[longitude]"]'));
      setText('lteOvDate', get('input[name="meta[date]"]'));

      var perfSel = modalEl.querySelector('select[name="meta[surveyPerformedByUserId]"]');
      var perf = '';
      if (perfSel && perfSel.options && perfSel.selectedIndex >= 0) {
        perf = (perfSel.options[perfSel.selectedIndex] || {}).text || '';
      }
      setText('lteOvPerformedBy', (perf || '').trim());

      var photos = countSelectedFiles();
      var photosEl = document.getElementById('lteOvPhotos');
      if (photosEl) photosEl.textContent = String(photos);

      var notes = get('textarea[name="notes[notes]"]');
      setText('lteOvNotes', notes !== '' ? 'Provided' : '-');
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

    var statusEl = document.getElementById('lteSurveyStatus');
    var saveDraftBtn = document.getElementById('lteSurveySaveDraftBtn');
    var submitBtn = document.getElementById('lteSurveySubmitBtn');
    function submitWithStatus(val) {
      if (statusEl) statusEl.value = val;
      if (formEl && typeof formEl.requestSubmit === 'function') {
        formEl.requestSubmit();
      } else if (formEl) {
        formEl.submit();
      }
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

<script>
  (function () {
    function syncCoords(form) {
      if (!form) return;
      var latEl = form.querySelector('.lte-lat');
      var lngEl = form.querySelector('.lte-lng');
      var coordsEl = form.querySelector('.lte-coords');
      if (!coordsEl) return;
      var lat = latEl ? (latEl.value || '').trim() : '';
      var lng = lngEl ? (lngEl.value || '').trim() : '';
      if (lat !== '' && lng !== '') coordsEl.value = lat + ', ' + lng;
    }

    document.querySelectorAll('.lte-edit-form').forEach(function (form) {
      var latEl = form.querySelector('.lte-lat');
      var lngEl = form.querySelector('.lte-lng');
      if (latEl) latEl.addEventListener('input', function () { syncCoords(form); });
      if (lngEl) lngEl.addEventListener('input', function () { syncCoords(form); });
      form.addEventListener('submit', function () { syncCoords(form); });

      var statusEl = form.querySelector('.lte-edit-status');
      var saveDraftBtn = form.querySelector('.lte-edit-save-draft');
      var submitBtn = form.querySelector('.lte-edit-submit');
      function submitWithStatus(val) {
        if (statusEl) statusEl.value = val;
        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit();
        } else {
          form.submit();
        }
      }
      if (saveDraftBtn) saveDraftBtn.addEventListener('click', function () { submitWithStatus('draft'); });
      if (submitBtn) submitBtn.addEventListener('click', function () { submitWithStatus('submitted'); });
    });

    function getCsrfToken() {
      var el = document.querySelector('meta[name="csrf-token"]');
      return el ? el.getAttribute('content') : '';
    }

    document.addEventListener('click', function (e) {
      var btn = e.target && e.target.closest ? e.target.closest('[data-lte-photo-delete]') : null;
      if (!btn) return;
      e.preventDefault();

      var url = btn.getAttribute('data-url') || '';
      if (!url) return;
      if (btn.dataset.busy === '1') return;

      var ok = window.confirm('Remove this image/attachment?');
      if (!ok) return;

      btn.dataset.busy = '1';
      btn.disabled = true;

      fetch(url, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
        },
      })
        .then(function (res) {
          return res.json().catch(function () { return null; }).then(function (json) {
            return { ok: res.ok, json: json };
          });
        })
        .then(function (out) {
          if (!out.ok || !out.json || out.json.success !== true) {
            var msg = out && out.json && out.json.message ? out.json.message : 'Failed to remove image.';
            alert(msg);
            return;
          }
          var wrap = btn.closest('[data-lte-photo-item]');
          if (wrap && wrap.parentNode) wrap.parentNode.removeChild(wrap);
        })
        .catch(function () {
          alert('Failed to remove image.');
        })
        .finally(function () {
          btn.disabled = false;
          btn.dataset.busy = '0';
        });
    });
  })();
</script>
<script>
  document.getElementById('lteSurveysPageSize')?.addEventListener('change', function () {
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', this.value);
    params.delete('page');
    window.location.search = params.toString();
  });
  document.getElementById('lteSurveysStatusFilter')?.addEventListener('change', function () {
    this.form?.submit();
  });
</script>
@endsection
