@extends('layouts.admin')

@section('title')
Connectivity Survey #{{ $survey->id }}
@endsection

@section('pageName')
Customer Connectivity Survey
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
@endsection
