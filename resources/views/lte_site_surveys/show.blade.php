@extends('layouts.admin')

@section('title')
LTE Site Survey #{{ $survey->id }}
@endsection


@section('content')
<section class="content ux-unified">
  <link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
  @php
    $p = is_array($survey->payload) ? $survey->payload : (array) $survey->payload;
    $meta = $p['meta'] ?? [];
    $general = $p['general'] ?? [];
    $access = $p['accessSecurity'] ?? [];
    $tower = $p['tower'] ?? [];
    $tx = $p['transmission'] ?? [];
    $power = $p['power'] ?? [];
    $civil = $p['civilWorks'] ?? [];
    $notes = $p['notes'] ?? [];
    $materials = $p['materials'] ?? [];

    $yesNoBadge = function ($v) {
      if ($v) return '<span class="badge bg-success-subtle text-success border border-success-subtle">Yes</span>';
      return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">No</span>';
    };
    $statusBadge = function ($v) {
      $s = is_string($v) ? strtolower(trim($v)) : '';
      if ($s === 'good') return '<span class="badge bg-success-subtle text-success border border-success-subtle">GOOD</span>';
      if ($s === 'bad') return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">BAD</span>';
      if ($s === 'not_available' || $s === 'not available') return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">NOT AVAILABLE</span>';
      if ($s === 'available') return '<span class="badge bg-success-subtle text-success border border-success-subtle">AVAILABLE</span>';
      if ($s === 'not_available') return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">NOT AVAILABLE</span>';
      if ($s === '') return '<span class="badge bg-light text-dark border">-</span>';
      return '<span class="badge bg-light text-dark border">' . e(str_replace('_', ' ', $s)) . '</span>';
    };

    $siteName = $survey->site_name ?: ($general['siteName'] ?? 'Untitled Site');
    $region = $survey->province_region ?: ($general['provinceRegion'] ?? '-');
    $coords = $survey->coordinates ?: ($general['coordinates'] ?? '-');
    $lat = $survey->latitude ?: ($general['latitude'] ?? null);
    $lng = $survey->longitude ?: ($general['longitude'] ?? null);
    $mapsUrl = ($lat !== null && $lng !== null) ? ('https://www.google.com/maps?q=' . $lat . ',' . $lng) : null;
    $civils = is_array($materials['civils'] ?? null) ? $materials['civils'] : [];
    $nte = is_array($materials['nte'] ?? null) ? $materials['nte'] : [];
  @endphp

  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
          <h3 class="card-title mb-1 text-2xl font-bold text-gray-800">
            <i class="fas fa-broadcast-tower text-primary me-2"></i>
            {{ $siteName }}
          </h3>
          <div class="text-sm text-gray-600 d-flex flex-wrap gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Survey #{{ $survey->id }}</span>
            <span class="badge bg-light text-dark border rounded-pill">JC: {{ $survey->jc_number ?: ($general['jcNumber'] ?? '-') }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Region: {{ $region }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Performed By: {{ $meta['surveyPerformedBy'] ?? ($survey->survey_performed_by ?: '-') }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Captured By: {{ optional($survey->user)->name ?: '-' }}</span>
            <span class="badge bg-light text-dark border rounded-pill">Created: {{ optional($survey->created_at)->format('Y-m-d H:i') }}</span>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
          @if($survey->status === 'submitted')
            <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Submitted</span>
          @else
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="fas fa-pen me-1"></i> Draft</span>
          @endif

          <div class="btn-group" role="group" aria-label="PDF Actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-lte-pdf-action="preview" {{ $survey->status !== 'submitted' ? 'disabled' : '' }}>
              <i class="fas fa-eye me-1"></i> Preview PDF
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-lte-pdf-action="download" {{ $survey->status !== 'submitted' ? 'disabled' : '' }}>
              <i class="fas fa-download me-1"></i> Download PDF
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" data-lte-pdf-action="regenerate" {{ $survey->status !== 'submitted' ? 'disabled' : '' }}>
              <i class="fas fa-sync-alt me-1"></i> Regenerate
            </button>
          </div>

          @if($mapsUrl)
            <a href="{{ $mapsUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-map-marker-alt me-1"></i> Open Map
            </a>
          @endif

          <a href="{{ route('lte-site-surveys.index') }}" class="btn btn-outline-secondary btn-sm">
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
              <div class="cc-kpi-sub">Survey Date: {{ $meta['date'] ?? '-' }}</div>
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
                <div class="cc-kpi-title">Backhaul</div>
              </div>
              <div class="cc-kpi-value">{{ strtoupper($tx['backhaulType'] ?? '-') }}</div>
              <div class="cc-kpi-sub">Capacity: {{ $tx['requiredBackhaulCapacity'] ?? '-' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--slate h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-bolt"></i></div>
                <div class="cc-kpi-title">Power</div>
              </div>
              <div class="cc-kpi-value">{{ strtoupper($power['powerSourceType'] ?? '-') }}</div>
              <div class="cc-kpi-sub">DB: {{ strtoupper($power['conditionOfDb'] ?? '-') }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 pb-4">
        <div class="row g-4 mt-1">
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>General Site Information</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Survey Date</td>
                      <td class="fw-semibold">{{ $meta['date'] ?? '-' }}</td>
                      <td class="text-muted" style="width: 35%;">Survey Performed By</td>
                      <td class="fw-semibold">{{ $meta['surveyPerformedBy'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Site Name</td>
                      <td class="fw-semibold">{{ $general['siteName'] ?? $siteName }}</td>
                      <td class="text-muted">JC Number</td>
                      <td class="fw-semibold">{{ $general['jcNumber'] ?? ($survey->jc_number ?: '-') }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Province/Region</td>
                      <td class="fw-semibold">{{ $general['provinceRegion'] ?? $region }}</td>
                      <td class="text-muted">Coordinates</td>
                      <td class="fw-semibold">{{ $general['coordinates'] ?? $coords }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Latitude</td>
                      <td class="fw-semibold">{{ $general['latitude'] ?? ($survey->latitude ?? '-') }}</td>
                      <td class="text-muted">Longitude</td>
                      <td class="fw-semibold">{{ $general['longitude'] ?? ($survey->longitude ?? '-') }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Physical Address</td>
                      <td colspan="3" class="fw-semibold">{{ $general['physicalAddress'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Contact Details</td>
                      <td colspan="3" class="fw-semibold">{{ $general['contactDetails'] ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-shield-alt text-primary me-2"></i>Site Access, Security & Tower</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr>
                      <th class="text-muted">Item</th>
                      <th class="text-end text-muted">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td>Security Fence</td><td class="text-end">{!! $yesNoBadge(!empty($access['securityFenceAvailable'])) !!}</td></tr>
                    <tr><td>Fence Condition</td><td class="text-end">{!! $statusBadge($access['conditionOfFence'] ?? '') !!}</td></tr>
                    <tr><td>24 Hour Access</td><td class="text-end">{!! $yesNoBadge(!empty($access['siteAccess24h'])) !!}</td></tr>
                    <tr><td>Guard Availability</td><td class="text-end">{!! $yesNoBadge(!empty($access['guardAvailable'])) !!}</td></tr>
                    <tr><td>Line of Sight</td><td class="text-end">{!! $yesNoBadge(!empty($access['lineOfSightAvailability'])) !!}</td></tr>
                    <tr><td>Terrain Type</td><td class="text-end"><span class="badge bg-light text-dark border">{{ $tower['terrainType'] ?? '-' }}</span></td></tr>
                    <tr><td>Tower Owner</td><td class="text-end"><span class="badge bg-light text-dark border">{{ $tower['towerOwner'] ?? '-' }}</span></td></tr>
                    <tr><td>Allocated Height</td><td class="text-end"><span class="badge bg-light text-dark border">{{ $tower['allocatedHeight'] ?? '-' }}</span></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-project-diagram text-primary me-2"></i>Transmission Details</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Nearest Manhole Coordinates</td>
                      <td class="fw-semibold">{{ $tx['nearestManholeCoordinates'] ?? '-' }}</td>
                      <td class="text-muted" style="width: 35%;">Distance from Existing Fibre</td>
                      <td class="fw-semibold">{{ $tx['distanceFromExistingFibre'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">POP Name</td>
                      <td class="fw-semibold">{{ $tx['distanceFromNearestPop'] ?? '-' }}</td>
                      <td class="text-muted">Distance from POP</td>
                      <td class="fw-semibold">{{ $tx['distanceFromNearestPop2'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Allocated Port</td>
                      <td class="fw-semibold">{{ $tx['allocatedPort'] ?? '-' }}</td>
                      <td class="text-muted">Backhaul Type</td>
                      <td class="fw-semibold"><span class="badge bg-light text-dark border">{{ $tx['backhaulType'] ?? '-' }}</span></td>
                    </tr>
                    <tr>
                      <td class="text-muted">Required Backhaul Capacity</td>
                      <td colspan="3" class="fw-semibold">{{ $tx['requiredBackhaulCapacity'] ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-bolt text-primary me-2"></i>Power Details</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted" style="width: 35%;">Power Source</td>
                      <td class="fw-semibold"><span class="badge bg-light text-dark border">{{ $power['powerSourceType'] ?? '-' }}</span></td>
                      <td class="text-muted" style="width: 35%;">Phase</td>
                      <td class="fw-semibold"><span class="badge bg-light text-dark border">{{ $power['phase'] ?? '-' }}</span></td>
                    </tr>
                    <tr>
                      <td class="text-muted">Input Voltage</td>
                      <td class="fw-semibold">{{ $power['inputVoltage'] ?? '-' }}</td>
                      <td class="text-muted">Battery Capacity</td>
                      <td class="fw-semibold">{{ $power['batteryCapacity'] ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Battery Autonomy (hrs)</td>
                      <td class="fw-semibold">{{ $power['batteryAutonomyHrs'] ?? '-' }}</td>
                      <td class="text-muted">Earthing System</td>
                      <td class="fw-semibold">{!! $statusBadge($power['earthingSystemInstalled'] ?? '') !!}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Cable Utility → Site</td>
                      <td class="fw-semibold">{!! $statusBadge($power['cableUtilitySourceToSite'] ?? '') !!}</td>
                      <td class="text-muted">Distribution Board</td>
                      <td class="fw-semibold">{!! $statusBadge($power['conditionOfDb'] ?? '') !!}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-hard-hat text-primary me-2"></i>Civil Works Requirement</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr>
                      <th class="text-muted">Item</th>
                      <th class="text-end text-muted">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td>Trenching</td><td class="text-end">{!! $yesNoBadge(!empty($civil['trenchingRequired'])) !!}</td></tr>
                    <tr><td>Concrete/Tar Breaking</td><td class="text-end">{!! $yesNoBadge(!empty($civil['breakingConcreteTar'])) !!}</td></tr>
                    <tr><td>Pole Planting</td><td class="text-end">{!! $yesNoBadge(!empty($civil['polePlantingRequired'])) !!}</td></tr>
                    <tr><td>Plinth Construction</td><td class="text-end">{!! $yesNoBadge(!empty($civil['constructionOfPlinth'])) !!}</td></tr>
                    <tr><td>New Manhole Requirement</td><td class="text-end">{!! $yesNoBadge(!empty($civil['newManholeRequired'])) !!}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
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
                    <div class="col-6 col-md-4">
                      <a href="{{ route('lte-site-surveys.photos.file', $ph->id) }}" target="_blank" class="text-decoration-none d-block">
                        <div class="border rounded-3 overflow-hidden" style="background:#fff;">
                          @if(\Illuminate\Support\Str::startsWith((string)($ph->mime_type ?? ''), 'image/'))
                            <img src="{{ route('lte-site-surveys.photos.file', $ph->id) }}" alt="{{ $ph->label }}" style="width:100%; height:140px; object-fit:cover;">
                          @else
                            <div class="d-flex align-items-center justify-content-center text-muted" style="height:140px;">
                              <div class="text-center">
                                <i class="fas fa-file-alt fs-4"></i>
                                <div class="small mt-1">Open</div>
                              </div>
                            </div>
                          @endif
                          <div class="px-2 py-2 border-top">
                            <div class="small text-muted text-truncate">{{ $ph->label }}</div>
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

          <div class="col-12">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3"><i class="fas fa-boxes text-primary me-2"></i>Materials & Quantities</div>
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
                            <td class="fw-semibold">{{ $r['description'] ?? '' }}</td>
                            <td class="text-muted">{{ $r['unit'] ?? '' }}</td>
                            <td class="text-end">{{ $r['qty'] ?? '' }}</td>
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
                            <td class="fw-semibold">{{ $r['description'] ?? '' }}</td>
                            <td class="text-muted">{{ $r['unit'] ?? '' }}</td>
                            <td class="text-end">{{ $r['qty'] ?? '' }}</td>
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
              <div class="fw-semibold mb-2"><i class="fas fa-clipboard-list text-primary me-2"></i>Notes / Recommendations</div>
              <div class="text-muted small mb-2">Engineering notes captured during site survey.</div>
              <div class="p-3 border rounded-3" style="background:#fff;">
                <div class="fw-semibold" style="white-space: pre-wrap;">{{ data_get($notes, 'notes') ?: 'No notes captured.' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="surveyPdfContent" style="position:absolute; left:-10000px; top:0; width:794px;">
    <div style="font-family: Arial, sans-serif; color:#111827;">
      <div style="display:flex; justify-content:space-between; align-items:center; padding:18px 20px; border-bottom:2px solid #e5e7eb;">
        <div>
          <div style="font-size:18px; font-weight:800; margin-bottom:4px;">LTE Site Survey Report</div>
          <div style="font-size:12px; color:#6b7280;">{{ $survey->site_name ?: 'Untitled Site' }} • {{ $survey->province_region ?: '-' }} • {{ optional($survey->created_at)->format('Y-m-d') }}</div>
        </div>
        <div style="font-size:12px; color:#374151;">
          Survey ID: <span style="font-weight:700;">{{ $survey->id }}</span>
        </div>
      </div>
      <div style="padding:18px 20px;">
        <div style="background:#f3f4f6; padding:10px 12px; font-weight:800; border-radius:8px;">SECTION A — GENERAL SITE INFORMATION</div>
        <div style="margin-top:12px; display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
          <div>
            <div style="font-size:11px; color:#6b7280;">Survey Date</div>
            <div style="font-size:13px; font-weight:700;">{{ $meta['date'] ?? '-' }}</div>
          </div>
          <div>
            <div style="font-size:11px; color:#6b7280;">Surveyed By</div>
            <div style="font-size:13px; font-weight:700;">{{ $meta['surveyPerformedBy'] ?? '-' }}</div>
          </div>
          <div>
            <div style="font-size:11px; color:#6b7280;">JC Number</div>
            <div style="font-size:13px; font-weight:700;">{{ $general['jcNumber'] ?? '-' }}</div>
          </div>
          <div>
            <div style="font-size:11px; color:#6b7280;">Coordinates</div>
            <div style="font-size:13px; font-weight:700;">{{ $general['coordinates'] ?? ($survey->coordinates ?? '-') }}</div>
          </div>
          <div>
            <div style="font-size:11px; color:#6b7280;">Latitude</div>
            <div style="font-size:13px; font-weight:700;">{{ $general['latitude'] ?? ($survey->latitude ?? '-') }}</div>
          </div>
          <div>
            <div style="font-size:11px; color:#6b7280;">Longitude</div>
            <div style="font-size:13px; font-weight:700;">{{ $general['longitude'] ?? ($survey->longitude ?? '-') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="lteSurveyPdfPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">LTE Site Survey PDF Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="height: 80vh;">
        <iframe id="lteSurveyPdfPreviewFrame" style="width:100%; height:100%; border:0;"></iframe>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@php
  $payload = is_array($survey->payload) ? $survey->payload : (array) $survey->payload;
  $pdfData = [
    'id' => $survey->id,
    'status' => $survey->status,
    'site_name' => $survey->site_name,
    'jc_number' => $survey->jc_number,
    'province_region' => $survey->province_region,
    'coordinates' => $survey->coordinates,
    'latitude' => $survey->latitude,
    'longitude' => $survey->longitude,
    'survey_performed_by' => $survey->survey_performed_by,
    'created_at' => optional($survey->created_at)->toIso8601String(),
    'captured_by' => optional($survey->user)->name,
    'payload' => $payload,
    'photos' => $survey->photos->map(function ($ph) {
      return [
        'id' => $ph->id,
        'label' => $ph->label,
        'mime_type' => $ph->mime_type,
        'original_name' => $ph->original_name,
        'url' => route('lte-site-surveys.photos.file', $ph->id),
      ];
    })->values()->all(),
  ];
@endphp
<script>
  window.__LTE_SURVEY_PDF_ASSETS__ = {
    logoUrl: @json(asset('images/powertel.png')),
    jspdfChunkUrl: @json(asset('js/node_modules_jspdf_dist_jspdf_es_min_js.js')),
    autotableChunkUrl: @json(asset('js/node_modules_jspdf-autotable_dist_jspdf_plugin_autotable_js.js')),
    html2canvasChunkUrl: @json(asset('js/node_modules_html2canvas_dist_html2canvas_js.js')),
  };
  window.__LTE_SURVEY_PDF_DATA__ = @json($pdfData);
</script>
<script src="{{ asset('js/survey-pdf.js') }}?v={{ file_exists(public_path('js/survey-pdf.js')) ? filemtime(public_path('js/survey-pdf.js')) : time() }}"></script>
@endsection
