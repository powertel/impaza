@extends('layouts.admin')

@section('title')
LTE Site Survey #{{ $survey->id }}
@endsection

@section('pageName')
LTE Site Survey
@endsection

@section('content')
<section class="content">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div>
        <h3 class="card-title mb-0">{{ $survey->site_name ?: 'Untitled Site' }}</h3>
        <div class="text-muted small">
          JC: {{ $survey->jc_number ?: '-' }} |
          Region: {{ $survey->province_region ?: '-' }} |
          By: {{ optional($survey->user)->name ?: '-' }} |
          Created: {{ optional($survey->created_at)->format('Y-m-d H:i') }}
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        @if($survey->status === 'submitted')
          <span class="badge bg-success">Submitted</span>
        @else
          <span class="badge bg-warning text-dark">Draft</span>
        @endif
        <a href="{{ route('lte-site-surveys.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left"></i> Back
        </a>
      </div>
    </div>

    <div class="card-body">
      @php
        $p = is_array($survey->payload) ? $survey->payload : (array) $survey->payload;
        $meta = $p['meta'] ?? [];
        $general = $p['general'] ?? [];
        $access = $p['accessSecurity'] ?? [];
        $tower = $p['tower'] ?? [];
        $tx = $p['transmission'] ?? [];
        $power = $p['power'] ?? [];
        $civil = $p['civilWorks'] ?? [];
        $materials = $p['materials'] ?? [];
      @endphp

      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">General Site Information</div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="text-muted small">Date</div>
                  <div class="fw-semibold">{{ $meta['date'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Survey Performed By</div>
                  <div class="fw-semibold">{{ $meta['surveyPerformedBy'] ?? '-' }}</div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-6">
                  <div class="text-muted small">Site Name</div>
                  <div class="fw-semibold">{{ $general['siteName'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">JC Number</div>
                  <div class="fw-semibold">{{ $general['jcNumber'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Latitude</div>
                  <div class="fw-semibold">{{ $general['latitude'] ?? ($survey->latitude ?? '-') }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Longitude</div>
                  <div class="fw-semibold">{{ $general['longitude'] ?? ($survey->longitude ?? '-') }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Coordinates</div>
                  <div class="fw-semibold">{{ $general['coordinates'] ?? ($survey->coordinates ?? '-') }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Province/Region</div>
                  <div class="fw-semibold">{{ $general['provinceRegion'] ?? '-' }}</div>
                </div>
              </div>
              <div class="mt-2">
                <div class="text-muted small">Physical Address</div>
                <div class="fw-semibold">{{ $general['physicalAddress'] ?? '-' }}</div>
              </div>
              <div class="mt-2">
                <div class="text-muted small">Contact Details</div>
                <div class="fw-semibold">{{ $general['contactDetails'] ?? '-' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Access, Security & Tower</div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="text-muted small">Security Fence</div>
                  <div class="fw-semibold">{{ !empty($access['securityFenceAvailable']) ? 'Yes' : 'No' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Fence Condition</div>
                  <div class="fw-semibold">{{ $access['conditionOfFence'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">24 Hour Access</div>
                  <div class="fw-semibold">{{ !empty($access['siteAccess24h']) ? 'Yes' : 'No' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Guard</div>
                  <div class="fw-semibold">{{ !empty($access['guardAvailable']) ? 'Yes' : 'No' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Line of Sight</div>
                  <div class="fw-semibold">{{ !empty($access['lineOfSightAvailability']) ? 'Yes' : 'No' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Terrain</div>
                  <div class="fw-semibold">{{ $tower['terrainType'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Tower Owner</div>
                  <div class="fw-semibold">{{ $tower['towerOwner'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Allocated Height</div>
                  <div class="fw-semibold">{{ $tower['allocatedHeight'] ?? '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Transmission Details</div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="text-muted small">Nearest Manhole Coordinates</div>
                  <div class="fw-semibold">{{ $tx['nearestManholeCoordinates'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Distance from Existing Fibre</div>
                  <div class="fw-semibold">{{ $tx['distanceFromExistingFibre'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Distance from Nearest POP</div>
                  <div class="fw-semibold">{{ $tx['distanceFromNearestPop'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Distance from Nearest POP (Alt)</div>
                  <div class="fw-semibold">{{ $tx['distanceFromNearestPop2'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Allocated Port</div>
                  <div class="fw-semibold">{{ $tx['allocatedPort'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Backhaul</div>
                  <div class="fw-semibold">{{ $tx['backhaulType'] ?? '-' }}</div>
                </div>
              </div>
              <div class="mt-2">
                <div class="text-muted small">Required Backhaul Capacity</div>
                <div class="fw-semibold">{{ $tx['requiredBackhaulCapacity'] ?? '-' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Power Details</div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="text-muted small">Power Source</div>
                  <div class="fw-semibold">{{ $power['powerSourceType'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Phase</div>
                  <div class="fw-semibold">{{ $power['phase'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Input Voltage</div>
                  <div class="fw-semibold">{{ $power['inputVoltage'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Battery Capacity</div>
                  <div class="fw-semibold">{{ $power['batteryCapacity'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Battery Autonomy (hrs)</div>
                  <div class="fw-semibold">{{ $power['batteryAutonomyHrs'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Earthing System</div>
                  <div class="fw-semibold">{{ $power['earthingSystemInstalled'] ?? '-' }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <div class="text-muted small">Cable Utility → Site</div>
                  <div class="fw-semibold">{{ $power['cableUtilitySourceToSite'] ?? '-' }}</div>
                </div>
                <div class="col-6">
                  <div class="text-muted small">Condition of DB</div>
                  <div class="fw-semibold">{{ $power['conditionOfDb'] ?? '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Civil Works Requirement</div>
            <div class="card-body">
              <div class="row">
                <div class="col-6"><span class="text-muted small">Trenching</span><div class="fw-semibold">{{ !empty($civil['trenchingRequired']) ? 'Yes' : 'No' }}</div></div>
                <div class="col-6"><span class="text-muted small">Breaking Concrete/Tar</span><div class="fw-semibold">{{ !empty($civil['breakingConcreteTar']) ? 'Yes' : 'No' }}</div></div>
              </div>
              <div class="row mt-2">
                <div class="col-6"><span class="text-muted small">Pole Planting</span><div class="fw-semibold">{{ !empty($civil['polePlantingRequired']) ? 'Yes' : 'No' }}</div></div>
                <div class="col-6"><span class="text-muted small">Plinth Construction</span><div class="fw-semibold">{{ !empty($civil['constructionOfPlinth']) ? 'Yes' : 'No' }}</div></div>
              </div>
              <div class="row mt-2">
                <div class="col-6"><span class="text-muted small">New Manhole</span><div class="fw-semibold">{{ !empty($civil['newManholeRequired']) ? 'Yes' : 'No' }}</div></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header">Photos</div>
            <div class="card-body">
              @if($survey->photos->count() === 0)
                <div class="text-muted">No photos uploaded.</div>
              @else
                <div class="row">
                  @foreach($survey->photos as $ph)
                    <div class="col-md-6 mb-3">
                      <div class="text-muted small mb-1">{{ $ph->label }}</div>
                      <a href="{{ Storage::url($ph->file_path) }}" target="_blank" class="d-block">
                        <img src="{{ Storage::url($ph->file_path) }}" alt="{{ $ph->label }}" style="width:100%; height:180px; object-fit:cover; border-radius:10px; border:1px solid #eef2f7;">
                      </a>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">Materials</div>
            <div class="card-body">
              @php
                $civils = is_array($materials['civils'] ?? null) ? $materials['civils'] : [];
                $nte = is_array($materials['nte'] ?? null) ? $materials['nte'] : [];
              @endphp
              <div class="row">
                <div class="col-md-6">
                  <div class="fw-semibold mb-2">Civils</div>
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead><tr><th>Description</th><th style="width:70px;">Unit</th><th style="width:90px;">Qty</th></tr></thead>
                      <tbody>
                        @foreach($civils as $r)
                          <tr>
                            <td>{{ $r['description'] ?? '' }}</td>
                            <td>{{ $r['unit'] ?? '' }}</td>
                            <td>{{ $r['qty'] ?? '' }}</td>
                          </tr>
                        @endforeach
                        @if(count($civils) === 0)
                          <tr><td colspan="3" class="text-muted">No items</td></tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fw-semibold mb-2">NTE</div>
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead><tr><th>Description</th><th style="width:70px;">Unit</th><th style="width:90px;">Qty</th></tr></thead>
                      <tbody>
                        @foreach($nte as $r)
                          <tr>
                            <td>{{ $r['description'] ?? '' }}</td>
                            <td>{{ $r['unit'] ?? '' }}</td>
                            <td>{{ $r['qty'] ?? '' }}</td>
                          </tr>
                        @endforeach
                        @if(count($nte) === 0)
                          <tr><td colspan="3" class="text-muted">No items</td></tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
