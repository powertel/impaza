@extends('layouts.admin')

@section('title')
Connectivity Survey #{{ $survey->id }}
@endsection


@section('content')
<section class="content ux-unified">
  <link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
  @php
    $p = is_array($payload) ? $payload : (array) $payload;
    $meta = is_array($p['meta'] ?? null) ? $p['meta'] : [];
    $general = is_array($p['general'] ?? null) ? $p['general'] : [];
    $service = is_array($p['serviceRequirements'] ?? null) ? $p['serviceRequirements'] : [];
    $permissions = is_array($p['permissions'] ?? null) ? $p['permissions'] : [];
    $outdoor = is_array($p['outdoor'] ?? null) ? $p['outdoor'] : [];
    $indoor = is_array($p['indoor'] ?? null) ? $p['indoor'] : [];
    $boq = is_array($p['boq'] ?? null) ? $p['boq'] : [];
    $civils = is_array($boq['civils'] ?? null) ? $boq['civils'] : [];
    $nte = is_array($boq['nte'] ?? null) ? $boq['nte'] : [];

    $customerName = $survey->customer_name ?: ($general['customerName'] ?? 'Customer');
    $siteName = $survey->site_name ?: ($general['siteName'] ?? 'Site');
    $accountOrJc = $survey->account_or_jc_number ?: ($general['accountOrJcNumber'] ?? '-');
    $coords = $survey->coordinates ?: ($general['coordinates'] ?? '-');
    $lat = $survey->latitude ?: ($general['latitude'] ?? null);
    $lng = $survey->longitude ?: ($general['longitude'] ?? null);
    $mapsUrl = ($lat !== null && $lng !== null) ? ('https://www.google.com/maps?q=' . $lat . ',' . $lng) : null;

    $surveyDate = $survey->survey_date ? $survey->survey_date->format('Y-m-d') : ($meta['date'] ?? '-');
    $performedBy = $survey->survey_performed_by ?: ($meta['surveyPerformedBy'] ?? '-');
    $capturedBy = optional($survey->user)->name ?: '-';

    $statusPill = function ($v) {
      $s = is_string($v) ? strtolower(trim($v)) : '';
      if ($s === 'submitted') return '<span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Submitted</span>';
      return '<span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="fas fa-pen me-1"></i> Draft</span>';
    };

    $valueBadge = function ($v) {
      $s = is_string($v) ? trim($v) : (is_numeric($v) ? (string) $v : '');
      if ($s === '') return '<span class="badge bg-light text-dark border">-</span>';
      return '<span class="badge bg-light text-dark border">' . e($s) . '</span>';
    };

    $yesNoBadge = function ($v) {
      if (is_bool($v)) {
        return $v
          ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Yes</span>'
          : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">No</span>';
      }
      $s = is_string($v) ? strtolower(trim($v)) : '';
      if ($s === 'yes' || $s === 'y' || $s === 'true' || $s === '1') return '<span class="badge bg-success-subtle text-success border border-success-subtle">Yes</span>';
      if ($s === 'no' || $s === 'n' || $s === 'false' || $s === '0') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">No</span>';
      if ($s === '') return '<span class="badge bg-light text-dark border">-</span>';
      return '<span class="badge bg-light text-dark border">' . e(ucfirst($s)) . '</span>';
    };

    $prettyLabel = function ($v) {
      $s = is_string($v) ? trim($v) : '';
      if ($s === '') return '-';
      $s = str_replace(['_', '-'], ' ', $s);
      return ucwords($s);
    };
  @endphp

  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
          <h3 class="card-title mb-1 text-2xl font-bold text-gray-800">
            <i class="fas fa-wifi text-primary me-2"></i>
            {{ $customerName }} • {{ $siteName }}
          </h3>
          <div class="text-sm text-gray-600 d-flex flex-wrap gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Survey #{{ $survey->id }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Account/JC: {{ $accountOrJc }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Performed By: {{ $performedBy }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Captured By: {{ $capturedBy }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Created: {{ optional($survey->created_at)->format('Y-m-d H:i') }}</span>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
          {!! $statusPill($survey->status) !!}
          <div class="btn-group" role="group" aria-label="PDF Actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-cc-pdf-action="preview" {{ $survey->status !== 'submitted' ? 'disabled' : '' }}>
              <i class="fas fa-eye me-1"></i> Preview PDF
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-cc-pdf-action="download" {{ $survey->status !== 'submitted' ? 'disabled' : '' }}>
              <i class="fas fa-download me-1"></i> Download PDF
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" data-cc-pdf-action="regenerate" {{ $survey->status !== 'submitted' ? 'disabled' : '' }}>
              <i class="fas fa-sync-alt me-1"></i> Regenerate
            </button>
          </div>
          @can('survey-edit')
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ccSurveyEditModal">
              <i class="fas fa-edit me-1"></i> Edit
            </button>
          @endcan
          @if($mapsUrl)
            <a href="{{ $mapsUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-map-marker-alt me-1"></i> Open Map
            </a>
          @endif
          <a href="{{ route('customer-connectivity-surveys.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
          </a>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="px-4 py-4 border-top bg-gray-50">
        <div class="row g-3">
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--blue h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="cc-kpi-title">Survey Status</div>
              </div>
              <div class="cc-kpi-value">{{ strtoupper($survey->status ?: 'DRAFT') }}</div>
              <div class="cc-kpi-sub">Survey Date: {{ $surveyDate }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--green h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-map-pin"></i></div>
                <div class="cc-kpi-title">Coordinates</div>
              </div>
              <div class="cc-kpi-value" style="font-size:18px;">{{ $coords }}</div>
              <div class="cc-kpi-sub">Lat: {{ $lat ?? '-' }} • Lng: {{ $lng ?? '-' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--amber h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-network-wired"></i></div>
                <div class="cc-kpi-title">Service</div>
              </div>
              <div class="cc-kpi-value">{{ strtoupper($service['serviceType'] ?? '-') }}</div>
              <div class="cc-kpi-sub">Bandwidth: {{ ($service['bandwidthDown'] ?? '-') . ' / ' . ($service['bandwidthUp'] ?? '-') }} Mbps</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--slate h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-camera"></i></div>
                <div class="cc-kpi-title">Attachments</div>
              </div>
              <div class="cc-kpi-value">{{ (int) $survey->photos->count() }}</div>
              <div class="cc-kpi-sub">Files uploaded</div>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 pb-4">
        <div class="row g-4 mt-1">
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>General Information</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Survey Date</td>
                      <td class="fw-semibold">{{ $surveyDate }}</td>
                      <td class="text-muted" style="width: 35%;">Survey Performed By</td>
                      <td class="fw-semibold">{{ $performedBy }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Customer</td>
                      <td class="fw-semibold">{{ $customerName }}</td>
                      <td class="text-muted">Account/JC</td>
                      <td class="fw-semibold">{{ $accountOrJc }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Site</td>
                      <td class="fw-semibold">{{ $siteName }}</td>
                      <td class="text-muted">Coordinates</td>
                      <td class="fw-semibold">{{ $coords }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Latitude</td>
                      <td class="fw-semibold">{{ $lat ?? '-' }}</td>
                      <td class="text-muted">Longitude</td>
                      <td class="fw-semibold">{{ $lng ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Physical Address</td>
                      <td colspan="3" class="fw-semibold">{{ $survey->physical_address ?: ($general['physicalAddress'] ?? '-') }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Customer Contact</td>
                      <td colspan="3" class="fw-semibold">
                        {{ $general['customerContactName'] ?? '-' }}
                        @if(!empty($general['customerContactPhone']))
                          • {{ $general['customerContactPhone'] }}
                        @endif
                        @if(!empty($general['customerContactEmail']))
                          • {{ $general['customerContactEmail'] }}
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-sitemap text-primary me-2"></i>Service Requirements</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Service Type</td>
                      <td class="fw-semibold">{!! $valueBadge($service['serviceType'] ?? '') !!}</td>
                      <td class="text-muted" style="width: 35%;">Handover Interface</td>
                      <td class="fw-semibold">{!! $valueBadge($service['handoverInterface'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Bandwidth Down (Mbps)</td>
                      <td class="fw-semibold">{{ $service['bandwidthDown'] ?? '-' }}</td>
                      <td class="text-muted">Bandwidth Up (Mbps)</td>
                      <td class="fw-semibold">{{ $service['bandwidthUp'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Redundancy Required</td>
                      <td class="fw-semibold">{!! $yesNoBadge($service['redundancyRequired'] ?? '') !!}</td>
                      <td class="text-muted">Public IPs Required</td>
                      <td class="fw-semibold">{!! $yesNoBadge($service['publicIpsRequired'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Public IP Qty</td>
                      <td class="fw-semibold">{{ $service['publicIpsQty'] ?? '-' }}</td>
                      <td class="text-muted">Purpose</td>
                      <td class="fw-semibold">{{ $service['servicePurpose'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">VLAN / Routing Notes</td>
                      <td colspan="3" class="fw-semibold" style="white-space: pre-wrap;">{{ $service['vlanNotes'] ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-id-card text-primary me-2"></i>Site Access & Permissions</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Access Contact</td>
                      <td class="fw-semibold">{{ $permissions['accessContact'] ?? '-' }}</td>
                      <td class="text-muted" style="width: 35%;">Survey Done With</td>
                      <td class="fw-semibold">{{ $permissions['surveyDoneWith'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Working Hours / Restrictions</td>
                      <td colspan="3" class="fw-semibold">{{ $permissions['workingHours'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Permissions Required</td>
                      <td colspan="3" class="fw-semibold">{{ $permissions['permissionsRequired'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Notes</td>
                      <td colspan="3" class="fw-semibold" style="white-space: pre-wrap;">{{ $permissions['notes'] ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-route text-primary me-2"></i>Outdoor Connectivity</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Nearest POP / Node</td>
                      <td class="fw-semibold">{{ $outdoor['nearestPopNode'] ?? '-' }}</td>
                      <td class="text-muted" style="width: 35%;">Feeder / Switch / OLT</td>
                      <td class="fw-semibold">{{ $outdoor['feederSwitchOlt'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Free Port Available</td>
                      <td class="fw-semibold">{!! $yesNoBadge($outdoor['freePortAvailable'] ?? '') !!}</td>
                      <td class="text-muted">Port ID</td>
                      <td class="fw-semibold">{{ $outdoor['portId'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Estimated Distance</td>
                      <td class="fw-semibold">{{ $outdoor['estimatedDistance'] ?? '-' }}</td>
                      <td class="text-muted">Route Type</td>
                      <td class="fw-semibold">{!! $valueBadge($outdoor['routeType'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Existing Infrastructure</td>
                      <td colspan="3" class="fw-semibold">{{ $outdoor['existingInfrastructure'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Obstructions / Risks</td>
                      <td colspan="3" class="fw-semibold">{{ $outdoor['obstructionsRisks'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Nearest Manhole / Pole Ref</td>
                      <td class="fw-semibold">{{ $outdoor['nearestManholePoleReference'] ?? '-' }}</td>
                      <td class="text-muted">Manhole / JB Details</td>
                      <td class="fw-semibold">{{ $outdoor['manholeJbDetails'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Proposed Refs</td>
                      <td colspan="3" class="fw-semibold" style="white-space: pre-wrap;">{{ $outdoor['proposedRefs'] ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-plug text-primary me-2"></i>Indoor Assessment</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Space for Equipment</td>
                      <td class="fw-semibold">{{ $indoor['spaceForEquipment'] ?? '-' }}</td>
                      <td class="text-muted" style="width: 35%;">Cabinet / Rack Available</td>
                      <td class="fw-semibold">{!! $yesNoBadge($indoor['cabinetAvailable'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Cabinet Size / U</td>
                      <td class="fw-semibold">{{ $indoor['cabinetSize'] ?? '-' }}</td>
                      <td class="text-muted">New Cabinet Required</td>
                      <td class="fw-semibold">{!! $yesNoBadge($indoor['newCabinetRequired'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Power Available</td>
                      <td class="fw-semibold">{!! $yesNoBadge($indoor['powerAvailable'] ?? '') !!}</td>
                      <td class="text-muted">Socket Type</td>
                      <td class="fw-semibold">{!! $valueBadge($indoor['socketType'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Distance to Socket (m)</td>
                      <td class="fw-semibold">{{ $indoor['distanceToSocket'] ?? '-' }}</td>
                      <td class="text-muted">Back-up Power</td>
                      <td class="fw-semibold">{{ $indoor['backupPower'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Air-conditioning</td>
                      <td class="fw-semibold">{!! $yesNoBadge($indoor['airConditioning'] ?? '') !!}</td>
                      <td class="text-muted">Earthing</td>
                      <td class="fw-semibold">{{ $indoor['earthing'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Internal Cabling Route</td>
                      <td colspan="3" class="fw-semibold">{{ $indoor['internalCablingRoute'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Notes</td>
                      <td colspan="3" class="fw-semibold" style="white-space: pre-wrap;">{{ $indoor['notes'] ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-boxes text-primary me-2"></i>BoQ (Civils + NTE)</div>
              <div class="row g-4">
                <div class="col-lg-6">
                  <div class="fw-semibold mb-2">Civils</div>
                  <div class="table-responsive">
                    <table class="table table-sm align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Description</th>
                          <th style="width:80px;">Unit</th>
                          <th style="width:90px;" class="text-end">Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($civils as $r)
                          <tr>
                            <td class="fw-semibold">{{ data_get($r, 'description') ?: '' }}</td>
                            <td class="text-muted">{{ data_get($r, 'unit') ?: '' }}</td>
                            <td class="text-end">{{ data_get($r, 'qty') ?: '' }}</td>
                          </tr>
                        @empty
                          <tr><td colspan="3" class="text-muted">No items</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="fw-semibold mb-2">NTE</div>
                  <div class="table-responsive">
                    <table class="table table-sm align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Description</th>
                          <th style="width:80px;">Unit</th>
                          <th style="width:90px;" class="text-end">Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($nte as $r)
                          <tr>
                            <td class="fw-semibold">{{ data_get($r, 'description') ?: '' }}</td>
                            <td class="text-muted">{{ data_get($r, 'unit') ?: '' }}</td>
                            <td class="text-end">{{ data_get($r, 'qty') ?: '' }}</td>
                          </tr>
                        @empty
                          <tr><td colspan="3" class="text-muted">No items</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold"><i class="fas fa-camera text-primary me-2"></i>Photos & Attachments</div>
                <span class="badge bg-light text-dark border">{{ $survey->photos->count() }} files</span>
              </div>
              @if($survey->photos->count() === 0)
                <div class="text-muted">No photos uploaded.</div>
              @else
                <div class="row g-3">
                  @foreach($survey->photos as $ph)
                    <div class="col-6 col-md-4 col-lg-3">
                      <a href="{{ route('customer-connectivity-surveys.photos.file', $ph->id) }}" target="_blank" class="text-decoration-none d-block">
                        <div class="border rounded-3 overflow-hidden" style="background:#fff;">
                          @if(\Illuminate\Support\Str::startsWith((string)($ph->mime_type ?? ''), 'image/'))
                            <img src="{{ route('customer-connectivity-surveys.photos.file', $ph->id) }}" alt="{{ $ph->label }}" style="width:100%; height:140px; object-fit:cover;">
                          @else
                            <div class="d-flex align-items-center justify-content-center text-muted" style="height:140px;">
                              <div class="text-center">
                                <i class="fas fa-file-alt fs-4"></i>
                                <div class="small mt-1">Open</div>
                              </div>
                            </div>
                          @endif
                          <div class="px-2 py-2 border-top">
                            <div class="small text-muted text-truncate">{{ $prettyLabel($ph->label) }}</div>
                            <div class="small fw-semibold text-truncate">{{ $ph->original_name ?: '' }}</div>
                          </div>
                        </div>
                      </a>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@can('survey-edit')
  <div class="modal fade custom-modal cc-edit-modal" id="ccSurveyEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered modal-fullscreen-md-down">
      <div class="modal-content border-0 shadow-lg cc-edit-modal-content" style="border-radius: 16px; overflow: hidden;">
        <div class="modal-header cc-edit-modal-header" style="background: var(--bs-primary); color: #fff; border-bottom: 0;">
          <div>
            <h5 class="modal-title mb-0">Edit Customer Connectivity Survey</h5>
            <div class="small" style="color: rgba(255,255,255,0.85);">Progressive form (step-by-step)</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.9;"></button>
        </div>
        <div class="modal-body cc-edit-modal-body" style="background:#f7f9fc;">
          @if (session('error') || $errors->any())
            <div class="alert alert-danger">
              <div class="fw-semibold">Unable to save. Please fix the following and try again.</div>
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

          <div class="mb-3 cc-edit-top" style="background:#fff; border: 1px solid #eef2f7; border-radius: 14px; padding: 14px;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold" id="ccEditStepTitle">General</div>
              <div class="text-muted small"><span id="ccEditStepNo">1</span> / <span id="ccEditStepTotal">8</span></div>
            </div>
            <div class="progress mt-2" style="height: 8px;">
              <div id="ccEditProgressBar" class="progress-bar" role="progressbar" style="width: 12%"></div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3" id="ccEditStepNav">
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="0">1. General</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="1">2. Service</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="2">3. Permissions</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="3">4. Outdoor</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="4">5. Indoor</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="5">6. BoQ</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="6">7. Photos</button>
              <button type="button" class="btn btn-sm cc-edit-step-btn" data-step="7">8. Overview</button>
            </div>
          </div>

          <form method="POST" action="{{ route('customer-connectivity-surveys.update', $survey->id) }}" enctype="multipart/form-data" id="ccEditSurveyForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="ccEditSurveyStatus" value="draft">

            <div class="cc-edit-step" data-step="0">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">General</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Date (YYYY-MM-DD)</label>
                        <input type="text" name="meta[date]" class="form-control form-control-sm" value="{{ old('meta.date', data_get($meta, 'date', $survey->survey_date ? $survey->survey_date->format('Y-m-d') : date('Y-m-d'))) }}">
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="mb-3">
                        <label class="form-label">Survey Performed By</label>
                        @php
                          $performedByNameVal = trim((string) old('meta.surveyPerformedBy', $performedBy));
                          if ($performedByNameVal === '-') $performedByNameVal = '';
                          $performedByUserId = (int) old('meta.surveyPerformedByUserId', data_get($meta, 'surveyPerformedByUserId', 0));
                          if ($performedByUserId <= 0 && $performedByNameVal !== '' && isset($users)) {
                            $match = collect($users)->firstWhere('name', $performedByNameVal);
                            if ($match) $performedByUserId = (int) $match->id;
                          }
                        @endphp
                        <select name="meta[surveyPerformedByUserId]" class="form-select form-select-sm js-select2 cc-edit-performed-by-user" data-placeholder="Select user">
                          <option value=""></option>
                          @foreach(($users ?? []) as $u)
                            <option value="{{ $u->id }}" {{ (int)$performedByUserId === (int)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                          @endforeach
                        </select>
                        <input type="hidden" name="meta[surveyPerformedBy]" class="cc-edit-performed-by-name" value="{{ $performedByNameVal }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="general[customerName]" class="form-control form-control-sm" value="{{ old('general.customerName', data_get($general, 'customerName', $survey->customer_name)) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Account / JC Number</label>
                        <input type="text" name="general[accountOrJcNumber]" class="form-control form-control-sm" value="{{ old('general.accountOrJcNumber', data_get($general, 'accountOrJcNumber', $survey->account_or_jc_number)) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Site Name / Location</label>
                        <input type="text" name="general[siteName]" class="form-control form-control-sm" value="{{ old('general.siteName', data_get($general, 'siteName', $survey->site_name)) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Physical Address</label>
                        <input type="text" name="general[physicalAddress]" class="form-control form-control-sm" value="{{ old('general.physicalAddress', data_get($general, 'physicalAddress', $survey->physical_address)) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="general[latitude]" class="form-control form-control-sm cc-edit-lat" value="{{ old('general.latitude', data_get($general, 'latitude', $survey->latitude)) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="general[longitude]" class="form-control form-control-sm cc-edit-lng" value="{{ old('general.longitude', data_get($general, 'longitude', $survey->longitude)) }}">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <input type="hidden" name="general[coordinates]" class="cc-edit-coords" value="{{ old('general.coordinates', data_get($general, 'coordinates', $survey->coordinates)) }}">
                      <div class="alert alert-info mb-0">Capture latitude and longitude for accurate location.</div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3 mt-3">
                        <label class="form-label">Customer Contact (Name)</label>
                        <input type="text" name="general[customerContactName]" class="form-control form-control-sm" value="{{ old('general.customerContactName', data_get($general, 'customerContactName')) }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3 mt-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="general[customerContactPhone]" class="form-control form-control-sm" value="{{ old('general.customerContactPhone', data_get($general, 'customerContactPhone')) }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3 mt-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="general[customerContactEmail]" class="form-control form-control-sm" value="{{ old('general.customerContactEmail', data_get($general, 'customerContactEmail')) }}">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="cc-edit-step d-none" data-step="1">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">Service Requirements</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Service Type</label>
                        @php $v = old('serviceRequirements.serviceType', data_get($service, 'serviceType')); @endphp
                        <select name="serviceRequirements[serviceType]" class="form-select form-select-sm">
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
                        <input type="text" name="serviceRequirements[bandwidthDown]" class="form-control form-control-sm" value="{{ old('serviceRequirements.bandwidthDown', data_get($service, 'bandwidthDown')) }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Bandwidth Up (Mbps)</label>
                        <input type="text" name="serviceRequirements[bandwidthUp]" class="form-control form-control-sm" value="{{ old('serviceRequirements.bandwidthUp', data_get($service, 'bandwidthUp')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Service Purpose</label>
                        <input type="text" name="serviceRequirements[servicePurpose]" class="form-control form-control-sm" value="{{ old('serviceRequirements.servicePurpose', data_get($service, 'servicePurpose')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Redundancy Required</label>
                        @php $v = old('serviceRequirements.redundancyRequired', data_get($service, 'redundancyRequired')); @endphp
                        <select name="serviceRequirements[redundancyRequired]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Handover Interface</label>
                        @php $v = old('serviceRequirements.handoverInterface', data_get($service, 'handoverInterface')); @endphp
                        <select name="serviceRequirements[handoverInterface]" class="form-select form-select-sm">
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
                        @php $v = old('serviceRequirements.publicIpsRequired', data_get($service, 'publicIpsRequired')); @endphp
                        <select name="serviceRequirements[publicIpsRequired]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Public IP Qty</label>
                        <input type="text" name="serviceRequirements[publicIpsQty]" class="form-control form-control-sm" value="{{ old('serviceRequirements.publicIpsQty', data_get($service, 'publicIpsQty')) }}">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="mb-0">
                        <label class="form-label">VLAN / Routing Notes</label>
                        <textarea name="serviceRequirements[vlanNotes]" class="form-control form-control-sm" rows="2">{{ old('serviceRequirements.vlanNotes', data_get($service, 'vlanNotes')) }}</textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="cc-edit-step d-none" data-step="2">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">Site Access & Permissions</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Access Contact (authority)</label>
                        <input type="text" name="permissions[accessContact]" class="form-control form-control-sm" value="{{ old('permissions.accessContact', data_get($permissions, 'accessContact')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Survey Done With</label>
                        <input type="text" name="permissions[surveyDoneWith]" class="form-control form-control-sm" value="{{ old('permissions.surveyDoneWith', data_get($permissions, 'surveyDoneWith')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Working Hours / Restrictions</label>
                        <input type="text" name="permissions[workingHours]" class="form-control form-control-sm" value="{{ old('permissions.workingHours', data_get($permissions, 'workingHours')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Permissions Required</label>
                        <input type="text" name="permissions[permissionsRequired]" class="form-control form-control-sm" value="{{ old('permissions.permissionsRequired', data_get($permissions, 'permissionsRequired')) }}">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="mb-0">
                        <label class="form-label">Notes</label>
                        <textarea name="permissions[notes]" class="form-control form-control-sm" rows="2">{{ old('permissions.notes', data_get($permissions, 'notes')) }}</textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="cc-edit-step d-none" data-step="3">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">Outdoor Connectivity</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Nearest POP / Node</label>
                        <input type="text" name="outdoor[nearestPopNode]" class="form-control form-control-sm" value="{{ old('outdoor.nearestPopNode', data_get($outdoor, 'nearestPopNode')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Feeder / Switch / OLT Name</label>
                        <input type="text" name="outdoor[feederSwitchOlt]" class="form-control form-control-sm" value="{{ old('outdoor.feederSwitchOlt', data_get($outdoor, 'feederSwitchOlt')) }}">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Free Port Available</label>
                        @php $v = old('outdoor.freePortAvailable', data_get($outdoor, 'freePortAvailable')); @endphp
                        <select name="outdoor[freePortAvailable]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Port ID</label>
                        <input type="text" name="outdoor[portId]" class="form-control form-control-sm" value="{{ old('outdoor.portId', data_get($outdoor, 'portId')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Estimated Distance (POP to Site)</label>
                        <input type="text" name="outdoor[estimatedDistance]" class="form-control form-control-sm" value="{{ old('outdoor.estimatedDistance', data_get($outdoor, 'estimatedDistance')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Route Type</label>
                        @php $v = old('outdoor.routeType', data_get($outdoor, 'routeType')); @endphp
                        <select name="outdoor[routeType]" class="form-select form-select-sm">
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
                        <input type="text" name="outdoor[existingInfrastructure]" class="form-control form-control-sm" value="{{ old('outdoor.existingInfrastructure', data_get($outdoor, 'existingInfrastructure')) }}">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="mb-3">
                        <label class="form-label">Obstructions / Risks</label>
                        <input type="text" name="outdoor[obstructionsRisks]" class="form-control form-control-sm" value="{{ old('outdoor.obstructionsRisks', data_get($outdoor, 'obstructionsRisks')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Nearest Manhole / Pole Reference</label>
                        <input type="text" name="outdoor[nearestManholePoleReference]" class="form-control form-control-sm" value="{{ old('outdoor.nearestManholePoleReference', data_get($outdoor, 'nearestManholePoleReference')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Manhole / JB Details</label>
                        <input type="text" name="outdoor[manholeJbDetails]" class="form-control form-control-sm" value="{{ old('outdoor.manholeJbDetails', data_get($outdoor, 'manholeJbDetails')) }}">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="mb-0">
                        <label class="form-label">New Proposed Manholes / Poles (Grid refs)</label>
                        <textarea name="outdoor[proposedRefs]" class="form-control form-control-sm" rows="2">{{ old('outdoor.proposedRefs', data_get($outdoor, 'proposedRefs')) }}</textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="cc-edit-step d-none" data-step="4">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">Indoor Assessment</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Space for Terminal Equipment</label>
                        <input type="text" name="indoor[spaceForEquipment]" class="form-control form-control-sm" value="{{ old('indoor.spaceForEquipment', data_get($indoor, 'spaceForEquipment')) }}">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Cabinet / Rack Available</label>
                        @php $v = old('indoor.cabinetAvailable', data_get($indoor, 'cabinetAvailable')); @endphp
                        <select name="indoor[cabinetAvailable]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Cabinet Size / U</label>
                        <input type="text" name="indoor[cabinetSize]" class="form-control form-control-sm" value="{{ old('indoor.cabinetSize', data_get($indoor, 'cabinetSize')) }}">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">New Cabinet Required</label>
                        @php $v = old('indoor.newCabinetRequired', data_get($indoor, 'newCabinetRequired')); @endphp
                        <select name="indoor[newCabinetRequired]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Power Available</label>
                        @php $v = old('indoor.powerAvailable', data_get($indoor, 'powerAvailable')); @endphp
                        <select name="indoor[powerAvailable]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Socket Type</label>
                        @php $v = old('indoor.socketType', data_get($indoor, 'socketType')); @endphp
                        <select name="indoor[socketType]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Round" {{ ($v === 'Round') ? 'selected' : '' }}>Round</option>
                          <option value="Square" {{ ($v === 'Square') ? 'selected' : '' }}>Square</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label class="form-label">Distance to Socket (m)</label>
                        <input type="text" name="indoor[distanceToSocket]" class="form-control form-control-sm" value="{{ old('indoor.distanceToSocket', data_get($indoor, 'distanceToSocket')) }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Back-up Power</label>
                        <input type="text" name="indoor[backupPower]" class="form-control form-control-sm" value="{{ old('indoor.backupPower', data_get($indoor, 'backupPower')) }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Air-conditioning</label>
                        @php $v = old('indoor.airConditioning', data_get($indoor, 'airConditioning')); @endphp
                        <select name="indoor[airConditioning]" class="form-select form-select-sm">
                          <option value="" {{ ($v === null || $v === '') ? 'selected' : '' }}>Select</option>
                          <option value="Yes" {{ ($v === 'Yes') ? 'selected' : '' }}>Yes</option>
                          <option value="No" {{ ($v === 'No') ? 'selected' : '' }}>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Earthing</label>
                        <input type="text" name="indoor[earthing]" class="form-control form-control-sm" value="{{ old('indoor.earthing', data_get($indoor, 'earthing')) }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Internal Cabling Route</label>
                        <input type="text" name="indoor[internalCablingRoute]" class="form-control form-control-sm" value="{{ old('indoor.internalCablingRoute', data_get($indoor, 'internalCablingRoute')) }}">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="mb-0">
                        <label class="form-label">Notes</label>
                        <textarea name="indoor[notes]" class="form-control form-control-sm" rows="2">{{ old('indoor.notes', data_get($indoor, 'notes')) }}</textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="cc-edit-step d-none" data-step="5">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">BoQ (Civils + NTE)</div>
                <div class="card-body">
                  @php
                    $civilsQtyByDesc = [];
                    foreach ((array) $civils as $r) {
                      $d = (string) data_get($r, 'description', '');
                      if ($d !== '') $civilsQtyByDesc[$d] = (string) data_get($r, 'qty', '');
                    }
                    $nteQtyByDesc = [];
                    foreach ((array) $nte as $r) {
                      $d = (string) data_get($r, 'description', '');
                      if ($d !== '') $nteQtyByDesc[$d] = (string) data_get($r, 'qty', '');
                    }
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
                    $civilsOld = old('boq.civils', []);
                    $nteOld = old('boq.nte', []);
                  @endphp
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
                            @foreach ($civilsDefaults as $idx => $row)
                              @php
                                $desc = $row['description'];
                                $qtyVal = data_get($civilsOld, $idx . '.qty');
                                if ($qtyVal === null) $qtyVal = $civilsQtyByDesc[$desc] ?? '';
                              @endphp
                              <tr>
                                <td>
                                  <input type="hidden" name="boq[civils][{{ $idx }}][description]" value="{{ $desc }}">
                                  <span class="fw-semibold">{{ $desc }}</span>
                                </td>
                                <td>
                                  <input type="hidden" name="boq[civils][{{ $idx }}][unit]" value="{{ $row['unit'] }}">
                                  <span class="text-muted">{{ $row['unit'] }}</span>
                                </td>
                                <td>
                                  <input type="text" name="boq[civils][{{ $idx }}][qty]" class="form-control form-control-sm" value="{{ $qtyVal }}">
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
                            @foreach ($nteDefaults as $idx => $row)
                              @php
                                $desc = $row['description'];
                                $qtyVal = data_get($nteOld, $idx . '.qty');
                                if ($qtyVal === null) $qtyVal = $nteQtyByDesc[$desc] ?? '';
                              @endphp
                              <tr>
                                <td>
                                  <input type="hidden" name="boq[nte][{{ $idx }}][description]" value="{{ $desc }}">
                                  <span class="fw-semibold">{{ $desc }}</span>
                                </td>
                                <td>
                                  <input type="hidden" name="boq[nte][{{ $idx }}][unit]" value="{{ $row['unit'] }}">
                                  <span class="text-muted">{{ $row['unit'] }}</span>
                                </td>
                                <td>
                                  <input type="text" name="boq[nte][{{ $idx }}][qty]" class="form-control form-control-sm" value="{{ $qtyVal }}">
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

            <div class="cc-edit-step d-none" data-step="6">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">Photos</div>
                <div class="card-body">
                  <div class="alert alert-info">Uploading here adds new files. Use the red X to remove an existing upload.</div>

                  <div class="row">
                    @php $photosByLabel = collect($survey->photos ?? [])->groupBy('label'); @endphp
                    @foreach (($photoLabels ?? []) as $key => $label)
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $label }}</label>
                          <input type="file" name="photos[{{ $key }}][]" class="form-control form-control-sm" multiple accept="image/*,application/pdf">
                          @php $items = $photosByLabel->get($key, collect()); @endphp
                          @if($items->count())
                            <div class="d-flex flex-wrap gap-2 mt-2">
                              @foreach($items as $ph)
                                @php
                                  $isImage = str_starts_with((string)($ph->mime_type ?? ''), 'image/');
                                  $fileUrl = route('customer-connectivity-surveys.photos.file', $ph->id);
                                  $delUrl = route('customer-connectivity-surveys.photos.destroy', $ph->id);
                                @endphp
                                @if($isImage)
                                  <div class="d-inline-block" style="position:relative" data-cc-photo-item>
                                    <a href="{{ $fileUrl }}" target="_blank" class="d-inline-block">
                                      <img src="{{ $fileUrl }}" alt="" style="width:90px;height:68px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">
                                    </a>
                                    <button
                                      type="button"
                                      class="btn btn-danger btn-sm"
                                      style="position:absolute;top:-8px;right:-8px;width:24px;height:24px;border-radius:999px;padding:0;display:flex;align-items:center;justify-content:center"
                                      data-cc-photo-delete
                                      data-url="{{ $delUrl }}"
                                    >
                                      <i class="fas fa-times" style="font-size:11px"></i>
                                    </button>
                                  </div>
                                @else
                                  <div class="d-inline-block" style="position:relative" data-cc-photo-item>
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                      <i class="fas fa-paperclip me-1"></i> Open
                                    </a>
                                    <button
                                      type="button"
                                      class="btn btn-danger btn-sm"
                                      style="position:absolute;top:-8px;right:-8px;width:24px;height:24px;border-radius:999px;padding:0;display:flex;align-items:center;justify-content:center"
                                      data-cc-photo-delete
                                      data-url="{{ $delUrl }}"
                                    >
                                      <i class="fas fa-times" style="font-size:11px"></i>
                                    </button>
                                  </div>
                                @endif
                              @endforeach
                            </div>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

            <div class="cc-edit-step d-none" data-step="7">
              <div class="card cc-edit-card" style="border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 1px 2px rgba(16,24,40,.04);">
                <div class="card-header cc-edit-card-header" style="border-bottom: 1px solid #d2e4ff; font-weight: 700; background: #eaf2ff;">Overview</div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <div class="p-3 border rounded-3 bg-white cc-edit-overview-tile">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-bold" id="ccEditOvCustomer">-</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-3 border rounded-3 bg-white cc-edit-overview-tile">
                        <div class="text-muted small">Site</div>
                        <div class="fw-bold" id="ccEditOvSite">-</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-3 border rounded-3 bg-white cc-edit-overview-tile">
                        <div class="text-muted small">Account/JC</div>
                        <div class="fw-bold" id="ccEditOvAccount">-</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-3 border rounded-3 bg-white cc-edit-overview-tile">
                        <div class="text-muted small">Date</div>
                        <div class="fw-bold" id="ccEditOvDate">-</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-3 border rounded-3 bg-white cc-edit-overview-tile">
                        <div class="text-muted small">Performed By</div>
                        <div class="fw-bold" id="ccEditOvPerformedBy">-</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-3 border rounded-3 bg-white cc-edit-overview-tile">
                        <div class="text-muted small">New Attachments</div>
                        <div class="fw-bold" id="ccEditOvPhotos">0</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-3 cc-edit-footerbar" style="position: sticky; bottom: 0; background: rgba(247,249,252,0.95); backdrop-filter: blur(6px); border-top: 1px solid #e5e7eb; padding-top: 12px; padding-bottom: 12px;">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="ccEditPrevBtn">
                <i class="fas fa-chevron-left"></i> Back
              </button>
              <div class="d-flex align-items-center gap-2">
                <div id="ccEditNextWrap">
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="ccEditNextBtn">
                    Next <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
                <div id="ccEditSubmitWrap" class="d-none">
                  <button type="button" class="btn btn-warning btn-sm" id="ccEditSaveDraftBtn">
                    <i class="fas fa-save"></i> Save Draft
                  </button>
                  <button type="button" class="btn btn-primary btn-sm" id="ccEditSubmitBtn">
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

<div class="modal fade" id="ccSurveyPdfPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer Connectivity Survey PDF Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="height: 80vh;">
        <iframe id="ccSurveyPdfPreviewFrame" style="width:100%; height:100%; border:0;"></iframe>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@php
  $payloadArr = is_array($survey->payload) ? $survey->payload : (array) $survey->payload;
  $pdfData = [
    'id' => $survey->id,
    'status' => $survey->status,
    'customer_name' => $survey->customer_name,
    'account_or_jc_number' => $survey->account_or_jc_number,
    'site_name' => $survey->site_name,
    'coordinates' => $survey->coordinates,
    'latitude' => $survey->latitude,
    'longitude' => $survey->longitude,
    'survey_performed_by' => $survey->survey_performed_by,
    'created_at' => optional($survey->created_at)->toIso8601String(),
    'captured_by' => optional($survey->user)->name,
    'payload' => $payloadArr,
    'photos' => $survey->photos->map(function ($ph) {
      return [
        'id' => $ph->id,
        'label' => $ph->label,
        'mime_type' => $ph->mime_type,
        'original_name' => $ph->original_name,
        'url' => route('customer-connectivity-surveys.photos.file', $ph->id),
      ];
    })->values()->all(),
  ];
@endphp
<script>
  window.__CC_SURVEY_PDF_ASSETS__ = {
    logoUrl: @json(asset('images/powertel.png')),
    jspdfChunkUrl: @json(asset('js/node_modules_jspdf_dist_jspdf_es_min_js.js')),
    autotableChunkUrl: @json(asset('js/node_modules_jspdf-autotable_dist_jspdf_plugin_autotable_js.js')),
    html2canvasChunkUrl: @json(asset('js/node_modules_html2canvas_dist_html2canvas_js.js')),
  };
  window.__CC_SURVEY_PDF_DATA__ = @json($pdfData);
</script>
<script src="{{ asset('js/survey-pdf.js') }}?v={{ file_exists(public_path('js/survey-pdf.js')) ? filemtime(public_path('js/survey-pdf.js')) : time() }}"></script>
@can('survey-edit')
<style>
  .cc-edit-modal .swal2-popup {
    border-radius: 18px;
  }
  .cc-edit-step-btn { border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #111827; font-weight: 600; }
  .cc-edit-step-btn.is-active { background: rgba(10, 126, 164, 0.12); border-color: rgba(10, 126, 164, 0.35); color: #0a7ea4; }
  html[data-theme="dark"] .cc-edit-modal .modal-content,
  html[data-theme="dark"] .cc-edit-modal .cc-edit-modal-content {
    background: linear-gradient(180deg, #0b1220 0%, #101a31 100%);
    color: #e5eefb;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-modal-body {
    background: linear-gradient(180deg, #0b1220 0%, #101a31 100%) !important;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-top {
    background: rgba(15, 23, 42, 0.94) !important;
    border-color: rgba(148, 163, 184, 0.18) !important;
  }
  html[data-theme="dark"] .cc-edit-modal .text-muted,
  html[data-theme="dark"] .cc-edit-modal .small {
    color: #94a3b8 !important;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-step-btn {
    background: #0b1220;
    border-color: rgba(148, 163, 184, 0.18);
    color: #dbe5f6;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-step-btn.is-active {
    background: rgba(37, 99, 235, 0.2);
    border-color: rgba(96, 165, 250, 0.35);
    color: #bfdbfe;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-card {
    border-color: rgba(148, 163, 184, 0.16) !important;
    background: linear-gradient(180deg, #0f172a 0%, #111c33 100%) !important;
    color: #e5eefb;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-card-header {
    background: linear-gradient(180deg, #172554 0%, #1e3a8a 100%) !important;
    border-bottom-color: rgba(148, 163, 184, 0.16) !important;
    color: #e5eefb;
  }
  html[data-theme="dark"] .cc-edit-modal .form-control,
  html[data-theme="dark"] .cc-edit-modal .form-select,
  html[data-theme="dark"] .cc-edit-modal textarea,
  html[data-theme="dark"] .cc-edit-modal input[type="file"] {
    background: rgba(2, 6, 23, 0.45);
    border-color: rgba(148, 163, 184, 0.18);
    color: #e2e8f0;
  }
  html[data-theme="dark"] .cc-edit-modal .form-control:focus,
  html[data-theme="dark"] .cc-edit-modal .form-select:focus,
  html[data-theme="dark"] .cc-edit-modal textarea:focus,
  html[data-theme="dark"] .cc-edit-modal input[type="file"]:focus {
    background: rgba(2, 6, 23, 0.62);
    border-color: rgba(96, 165, 250, 0.45);
    color: #f8fafc;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.18);
  }
  html[data-theme="dark"] .cc-edit-modal .form-control::placeholder,
  html[data-theme="dark"] .cc-edit-modal textarea::placeholder {
    color: rgba(148, 163, 184, 0.75);
  }
  html[data-theme="dark"] .cc-edit-modal .table,
  html[data-theme="dark"] .cc-edit-modal .table > :not(caption) > * > * {
    color: #e2e8f0;
    --bs-table-bg: transparent;
    --bs-table-color: #e2e8f0;
  }
  html[data-theme="dark"] .cc-edit-modal .table thead th {
    background: rgba(30, 41, 59, 0.9);
    border-bottom-color: rgba(148, 163, 184, 0.16);
    color: #cbd5e1;
  }
  html[data-theme="dark"] .cc-edit-modal .table tbody td {
    border-top-color: rgba(148, 163, 184, 0.12);
  }
  html[data-theme="dark"] .cc-edit-modal .alert-info {
    background: rgba(8, 145, 178, 0.16);
    border-color: rgba(34, 211, 238, 0.2);
    color: #c8f1ff;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-overview-tile {
    background: rgba(15, 23, 42, 0.92) !important;
    border-color: rgba(148, 163, 184, 0.16) !important;
    color: #e5eefb;
  }
  html[data-theme="dark"] .cc-edit-modal .cc-edit-footerbar {
    background: rgba(11, 18, 32, 0.95) !important;
    border-top-color: rgba(148, 163, 184, 0.18) !important;
  }
  html[data-theme="dark"] .swal2-popup {
    background: linear-gradient(180deg, #0f172a 0%, #111c33 100%);
    color: #e5eefb;
  }
  html[data-theme="dark"] .swal2-title,
  html[data-theme="dark"] .swal2-html-container {
    color: #e5eefb;
  }
  html[data-theme="dark"] .swal2-icon.swal2-warning {
    border-color: rgba(251, 191, 36, 0.8);
    color: #fbbf24;
  }
</style>
<script>
  (function () {
    var modalEl = document.getElementById('ccSurveyEditModal');
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

    var formEl = document.getElementById('ccEditSurveyForm');
    var statusEl = document.getElementById('ccEditSurveyStatus');
    var steps = Array.prototype.slice.call(modalEl.querySelectorAll('.cc-edit-step'));
    var current = 0;

    var stepTitle = document.getElementById('ccEditStepTitle');
    var stepNo = document.getElementById('ccEditStepNo');
    var stepTotal = document.getElementById('ccEditStepTotal');
    var progressBar = document.getElementById('ccEditProgressBar');
    var prevBtn = document.getElementById('ccEditPrevBtn');
    var nextBtn = document.getElementById('ccEditNextBtn');
    var nextWrap = document.getElementById('ccEditNextWrap');
    var submitWrap = document.getElementById('ccEditSubmitWrap');
    var saveDraftBtn = document.getElementById('ccEditSaveDraftBtn');
    var submitBtn = document.getElementById('ccEditSubmitBtn');
    var stepNav = document.getElementById('ccEditStepNav');
    var stepBtns = stepNav ? Array.prototype.slice.call(stepNav.querySelectorAll('.cc-edit-step-btn')) : [];

    if (stepTotal) stepTotal.textContent = String(steps.length);

    function syncCoordinates() {
      if (!formEl) return;
      var latEl = formEl.querySelector('.cc-edit-lat');
      var lngEl = formEl.querySelector('.cc-edit-lng');
      var coordsEl = formEl.querySelector('.cc-edit-coords');
      if (!coordsEl) return;
      var lat = latEl ? (latEl.value || '').trim() : '';
      var lng = lngEl ? (lngEl.value || '').trim() : '';
      if (lat !== '' && lng !== '') coordsEl.value = lat + ', ' + lng;
    }

    function syncPerformedByName() {
      if (!formEl) return;
      var sel = formEl.querySelector('select[name="meta[surveyPerformedByUserId]"]');
      var hid = formEl.querySelector('input[name="meta[surveyPerformedBy]"]');
      if (!sel || !hid) return;
      var opt = sel.options && sel.selectedIndex >= 0 ? sel.options[sel.selectedIndex] : null;
      hid.value = opt ? (opt.text || '').trim() : '';
    }

    if (formEl) {
      var perfSel = formEl.querySelector('select[name="meta[surveyPerformedByUserId]"]');
      if (perfSel) {
        perfSel.addEventListener('change', syncPerformedByName);
        syncPerformedByName();
      }
    }

    function getCsrfToken() {
      var el = document.querySelector('meta[name="csrf-token"]');
      return el ? el.getAttribute('content') : '';
    }

    function isDarkTheme() {
      var html = document.documentElement;
      return !!html && html.getAttribute('data-theme') === 'dark';
    }

    function showDeleteConfirm() {
      if (window.swal && typeof window.swal.fire === 'function') {
        return window.swal.fire({
          title: 'Remove Attachment?',
          text: 'This file will be deleted from the survey immediately.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, remove it',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: isDarkTheme() ? '#475569' : '#cbd5e1',
          background: isDarkTheme() ? '#0f172a' : '#ffffff',
          color: isDarkTheme() ? '#e5eefb' : '#111827',
        }).then(function (res) {
          return !!res && !!res.isConfirmed;
        });
      }
      return Promise.resolve(window.confirm('Remove this image/attachment?'));
    }

    function showMessage(title, text, icon) {
      if (window.swal && typeof window.swal.fire === 'function') {
        return window.swal.fire({
          title: title,
          text: text,
          icon: icon,
          confirmButtonColor: '#2563eb',
          background: isDarkTheme() ? '#0f172a' : '#ffffff',
          color: isDarkTheme() ? '#e5eefb' : '#111827',
        });
      }
      window.alert(text || title);
      return Promise.resolve();
    }

    document.addEventListener('click', function (e) {
      var btn = e.target && e.target.closest ? e.target.closest('[data-cc-photo-delete]') : null;
      if (!btn) return;
      e.preventDefault();

      var url = btn.getAttribute('data-url') || '';
      if (!url) return;
      if (btn.dataset.busy === '1') return;

      showDeleteConfirm().then(function (ok) {
        if (!ok) return;

        btn.dataset.busy = '1';
        btn.disabled = true;

        return fetch(url, {
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
              return showMessage('Delete Failed', msg, 'error');
            }
            var wrap = btn.closest('[data-cc-photo-item]');
            if (wrap && wrap.parentNode) wrap.parentNode.removeChild(wrap);
            return null;
          })
          .catch(function () {
            return showMessage('Delete Failed', 'Failed to remove image.', 'error');
          })
          .finally(function () {
            btn.disabled = false;
            btn.dataset.busy = '0';
          });
      });
    });

    function countSelectedFiles() {
      var total = 0;
      modalEl.querySelectorAll('input[type=\"file\"]').forEach(function (inp) {
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
      setText('ccEditOvCustomer', get('input[name=\"general[customerName]\"]'));
      setText('ccEditOvSite', get('input[name=\"general[siteName]\"]'));
      setText('ccEditOvAccount', get('input[name=\"general[accountOrJcNumber]\"]'));
      setText('ccEditOvDate', get('input[name=\"meta[date]\"]'));
      setText('ccEditOvPerformedBy', get('input[name=\"meta[surveyPerformedBy]\"]'));
      var photosEl = document.getElementById('ccEditOvPhotos');
      if (photosEl) photosEl.textContent = String(countSelectedFiles());
    }

    function setSubmitRequired(on) {
      if (!formEl) return;
      var req = [
        'input[name=\"general[customerName]\"]',
        'input[name=\"general[siteName]\"]'
      ];
      req.forEach(function (sel) {
        var el = formEl.querySelector(sel);
        if (!el) return;
        if (on) el.setAttribute('required', 'required');
        else el.removeAttribute('required');
      });
    }

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

    function submitWithStatus(val) {
      if (statusEl) statusEl.value = val;
      var isSubmit = val === 'submitted';
      setSubmitRequired(isSubmit);
      syncCoordinates();

      if (isSubmit && formEl && typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
        var invalid = findFirstInvalidControl();
        if (invalid) {
          var stepEl = invalid.closest ? invalid.closest('.cc-edit-step') : null;
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

    if (formEl) {
      var latEl = formEl.querySelector('.cc-edit-lat');
      var lngEl = formEl.querySelector('.cc-edit-lng');
      if (latEl) latEl.addEventListener('input', syncCoordinates);
      if (lngEl) lngEl.addEventListener('input', syncCoordinates);
    }

    if (saveDraftBtn) saveDraftBtn.addEventListener('click', function () { submitWithStatus('draft'); });
    if (submitBtn) submitBtn.addEventListener('click', function () { submitWithStatus('submitted'); });
    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });
    stepBtns.forEach(function (b) { b.addEventListener('click', function () { goTo(b.getAttribute('data-step')); }); });

    modalEl.addEventListener('hidden.bs.modal', function () { current = 0; render(); });
    render();

    @if ($errors->any() || request()->query('edit'))
      try {
        var m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
      } catch (e) {}
    @endif
  })();
</script>
@endcan
@endsection
