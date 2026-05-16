@extends('layouts.admin')

@section('title')
New LTE Site Survey
@endsection

@section('pageName')
New LTE Site Survey
@endsection

@section('content')
<section class="content">
  <div class="card">
    <div class="card-header">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <h3 class="card-title mb-0">LTE Site Survey Sheet</h3>
          <div class="text-muted small">Progressive form (step-by-step)</div>
        </div>
        <a href="{{ route('lte-site-surveys.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left"></i> Back
        </a>
      </div>
    </div>

    <div class="card-body">
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
      <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between">
          <div class="fw-semibold" id="stepTitle">General Site Information</div>
          <div class="text-muted small"><span id="stepNo">1</span> / <span id="stepTotal">10</span></div>
        </div>
        <div class="progress mt-2" style="height: 8px;">
          <div id="progressBar" class="progress-bar" role="progressbar" style="width: 10%"></div>
        </div>
      </div>

      <form method="POST" action="{{ route('lte-site-surveys.store') }}" enctype="multipart/form-data" id="surveyForm">
        @csrf

        <div class="survey-step" data-step="0">
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="meta[date]" class="form-control form-control-sm" value="{{ old('meta.date') }}">
              </div>
            </div>
            <div class="col-md-8">
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
                <label class="form-label">Site Name</label>
                <input type="text" name="general[siteName]" class="form-control form-control-sm" value="{{ old('general.siteName') }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">JC Number</label>
                <input type="text" name="general[jcNumber]" class="form-control form-control-sm" value="{{ old('general.jcNumber') }}">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Province/Region</label>
                <input type="text" name="general[provinceRegion]" class="form-control form-control-sm" value="{{ old('general.provinceRegion') }}">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Physical Address</label>
            <textarea name="general[physicalAddress]" class="form-control form-control-sm" rows="3">{{ old('general.physicalAddress') }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Contact Details</label>
            <textarea name="general[contactDetails]" class="form-control form-control-sm" rows="2">{{ old('general.contactDetails') }}</textarea>
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
                    <input class="form-check-input" type="checkbox" name="accessSecurity[securityFenceAvailable]" value="1" id="securityFenceAvailable" {{ old('accessSecurity.securityFenceAvailable') ? 'checked' : '' }}>
                    <label class="form-check-label" for="securityFenceAvailable">Security Fence Available</label>
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
                    <input class="form-check-input" type="checkbox" name="accessSecurity[siteAccess24h]" value="1" id="siteAccess24h" {{ old('accessSecurity.siteAccess24h') ? 'checked' : '' }}>
                    <label class="form-check-label" for="siteAccess24h">24 Hour Site Access</label>
                  </div>

                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="accessSecurity[guardAvailable]" value="1" id="guardAvailable" {{ old('accessSecurity.guardAvailable') ? 'checked' : '' }}>
                    <label class="form-check-label" for="guardAvailable">Guard Available</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accessSecurity[lineOfSightAvailability]" value="1" id="lineOfSightAvailability" {{ old('accessSecurity.lineOfSightAvailability') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lineOfSightAvailability">Line of sight Availability</label>
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
                <input class="form-check-input" type="checkbox" name="civilWorks[trenchingRequired]" value="1" id="trenchingRequired" {{ old('civilWorks.trenchingRequired') ? 'checked' : '' }}>
                <label class="form-check-label" for="trenchingRequired">Trenching Required</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="civilWorks[breakingConcreteTar]" value="1" id="breakingConcreteTar" {{ old('civilWorks.breakingConcreteTar') ? 'checked' : '' }}>
                <label class="form-check-label" for="breakingConcreteTar">Breaking Concrete/Tar</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="civilWorks[polePlantingRequired]" value="1" id="polePlantingRequired" {{ old('civilWorks.polePlantingRequired') ? 'checked' : '' }}>
                <label class="form-check-label" for="polePlantingRequired">Pole Planting Required</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="civilWorks[constructionOfPlinth]" value="1" id="constructionOfPlinth" {{ old('civilWorks.constructionOfPlinth') ? 'checked' : '' }}>
                <label class="form-check-label" for="constructionOfPlinth">Construction of Plinth</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="civilWorks[newManholeRequired]" value="1" id="newManholeRequired" {{ old('civilWorks.newManholeRequired') ? 'checked' : '' }}>
                <label class="form-check-label" for="newManholeRequired">New Manhole Required</label>
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
                    <table class="table table-sm mb-0">
                      <thead>
                        <tr>
                          <th>Description</th>
                          <th style="width: 70px;">Unit</th>
                          <th style="width: 90px;">Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($materials['civils'] as $i => $row)
                          <tr>
                            <td>
                              <input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][description]" value="{{ old('materials.civils.' . $i . '.description', $row['description'] ?? '') }}">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][unit]" value="{{ old('materials.civils.' . $i . '.unit', $row['unit'] ?? '') }}">
                            </td>
                            <td>
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
                    <table class="table table-sm mb-0">
                      <thead>
                        <tr>
                          <th>Description</th>
                          <th style="width: 70px;">Unit</th>
                          <th style="width: 90px;">Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($materials['nte'] as $i => $row)
                          <tr>
                            <td>
                              <input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][description]" value="{{ old('materials.nte.' . $i . '.description', $row['description'] ?? '') }}">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][unit]" value="{{ old('materials.nte.' . $i . '.unit', $row['unit'] ?? '') }}">
                            </td>
                            <td>
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
                @foreach($photoLabels as $key => $label)
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

        <div class="d-flex align-items-center justify-content-between mt-3">
          <button type="button" class="btn btn-outline-secondary btn-sm" id="prevBtn">
            <i class="fas fa-chevron-left"></i> Back
          </button>
          <div class="d-flex align-items-center gap-2">
            <div id="navNextWrap">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="nextBtn">
                Next <i class="fas fa-chevron-right"></i>
              </button>
            </div>
            <div id="submitWrap" class="d-none">
              <button type="submit" name="status" value="draft" class="btn btn-warning btn-sm">
                <i class="fas fa-save"></i> Save Draft
              </button>
              <button type="submit" name="status" value="submitted" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane"></i> Submit
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function () {
    var steps = Array.prototype.slice.call(document.querySelectorAll('.survey-step'));
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
    var current = 0;

    var stepTitle = document.getElementById('stepTitle');
    var stepNo = document.getElementById('stepNo');
    var stepTotal = document.getElementById('stepTotal');
    var progressBar = document.getElementById('progressBar');
    var prevBtn = document.getElementById('prevBtn');
    var nextBtn = document.getElementById('nextBtn');
    var navNextWrap = document.getElementById('navNextWrap');
    var submitWrap = document.getElementById('submitWrap');
    var formEl = document.getElementById('surveyForm');

    var latEl = document.querySelector('input[name="general[latitude]"]');
    var lngEl = document.querySelector('input[name="general[longitude]"]');
    var coordsEl = document.querySelector('input[name="general[coordinates]"]');

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
      document.querySelectorAll('input[type="file"]').forEach(function (inp) {
        try { total += (inp.files ? inp.files.length : 0); } catch (e) {}
      });
      return total;
    }

    function fillOverview() {
      var get = function (sel) {
        var el = document.querySelector(sel);
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

      var perfSel = document.querySelector('select[name="meta[surveyPerformedByUserId]"]');
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

    stepTotal.textContent = String(steps.length);

    function render() {
      steps.forEach(function (el, i) {
        if (i === current) el.classList.remove('d-none');
        else el.classList.add('d-none');
      });
      stepTitle.textContent = titles[current] || ('Step ' + String(current + 1));
      stepNo.textContent = String(current + 1);
      var pct = Math.round(((current + 1) / steps.length) * 100);
      progressBar.style.width = String(pct) + '%';
      prevBtn.disabled = current === 0;
      nextBtn.disabled = current === steps.length - 1;
      var isLast = current === steps.length - 1;
      if (navNextWrap) navNextWrap.classList.toggle('d-none', isLast);
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

    function goTo(idx) {
      current = Math.max(0, Math.min(steps.length - 1, parseInt(idx, 10) || 0));
      render();
      window.scrollTo({ top: 0, behavior: 'smooth' });
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

    prevBtn.addEventListener('click', function () {
      goTo(current - 1);
    });

    nextBtn.addEventListener('click', function () {
      goTo(current + 1);
    });

    render();
  })();
</script>
@endsection
