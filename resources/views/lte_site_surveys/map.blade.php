@extends('layouts.admin')

@section('title')
LTE Site Surveys Map
@endsection

@section('content')
<link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

<section class="content ux-unified">
  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
            <i class="fas fa-map-marked-alt text-primary me-3"></i>
            LTE Site Surveys Map
          </h3>
          <p class="text-sm text-gray-600 mb-0 mt-1 me-3">All survey coordinates plotted on a map</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('lte-site-surveys.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Surveys
          </a>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="bg-gray-50 px-4 py-3 border-bottom">
        <form method="get" action="{{ route('lte-site-surveys.map') }}" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-between gap-3">
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-flag me-1"></i>Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="" {{ empty($status ?? '') ? 'selected' : '' }}>All</option>
              <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="submitted" {{ ($status ?? '') === 'submitted' ? 'selected' : '' }}>Submitted</option>
            </select>
          </div>

          <div class="cc-filter-actions ms-auto">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
              <i class="fas fa-filter me-1"></i> Apply
            </button>
            <a href="{{ route('lte-site-surveys.map') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
              <i class="fas fa-undo me-1"></i> Reset
            </a>
          </div>
        </form>
      </div>

      <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="cc-kpi cc-kpi--blue h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-database"></i></div>
                <div class="cc-kpi-title">Surveys</div>
              </div>
              <div class="cc-kpi-value">{{ number_format((int)($total ?? 0)) }}</div>
              <div class="cc-kpi-sub">Loaded (after filters)</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="cc-kpi cc-kpi--green h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-map-pin"></i></div>
                <div class="cc-kpi-title">Plotted</div>
              </div>
              <div class="cc-kpi-value">{{ number_format((int)($plotted ?? 0)) }}</div>
              <div class="cc-kpi-sub">With valid lat/lng</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="cc-kpi cc-kpi--slate h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="cc-kpi-title">Missing</div>
              </div>
              <div class="cc-kpi-value">{{ number_format((int)($missing ?? 0)) }}</div>
              <div class="cc-kpi-sub">No coordinates captured</div>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 pb-4">
        <div class="cc-chart-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">Survey Map</div>
            <a href="{{ route('lte-site-surveys.reports') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
              <i class="fas fa-chart-bar me-1"></i> Reports
            </a>
          </div>
          <div id="lteSurveyMap" style="height: 80vh; border-radius: 12px; overflow: hidden;"></div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
  (function () {
    if (typeof L === 'undefined') return;

    var points = @json($points ?? []);
    var map = L.map('lteSurveyMap', { zoomControl: true, scrollWheelZoom: true });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var zimbabweCenter = [-19.015438, 29.154857];
    map.setView(zimbabweCenter, 6);

    function esc(s) {
      return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    var pinIcon = L.icon({
      iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-red.png',
      iconRetinaUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-red.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    });

    var bounds = [];
    points.forEach(function (p) {
      if (!p || typeof p.lat !== 'number' || typeof p.lng !== 'number') return;
      var title = esc(p.site_name || 'Untitled');
      var region = esc(p.province_region || '-');
      var status = esc(p.status || '-');
      var created = esc(p.created_at || '');
      var link = "{{ route('lte-site-surveys.index') }}" + "?q=" + encodeURIComponent(p.site_name || '');
      var marker = L.marker([p.lat, p.lng], { icon: pinIcon }).addTo(map);
      marker.bindPopup(
        '<div style="min-width:220px">' +
          '<div style="font-weight:800; color:#0f172a; margin-bottom:4px">' + title + '</div>' +
          '<div style="font-size:12px; color:#475569">' +
            '<div><strong>Region:</strong> ' + region + '</div>' +
            '<div><strong>Status:</strong> ' + status + '</div>' +
            (created ? ('<div><strong>Created:</strong> ' + created + '</div>') : '') +
          '</div>' +
          '<div style="margin-top:10px">' +
            '<a href="' + link + '" style="display:inline-block; padding:6px 10px; border-radius:999px; border:1px solid #cbd5e1; text-decoration:none; font-size:12px; color:#0f172a;">Open</a>' +
          '</div>' +
        '</div>'
      );

      bounds.push([p.lat, p.lng]);
    });

    if (bounds.length) {
      map.fitBounds(bounds, { padding: [24, 24] });
    }
  })();
</script>
@endsection
