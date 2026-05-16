<div class="modal custom-modal fade" id="lteSurveyEditModal-{{ $survey->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="lteSurveyEditModalLabel-{{ $survey->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0">
        <div class="d-flex align-items-center">
          <span class="badge bg-secondary me-2"><i class="fas fa-edit"></i></span>
          <div>
            <h5 class="modal-title mb-0" id="lteSurveyEditModalLabel-{{ $survey->id }}">Edit LTE Site Survey</h5>
            <small class="text-muted">#{{ $survey->id }} • {{ $survey->site_name ?: 'Untitled' }}</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0">
        @php
          $p = is_array($survey->payload) ? $survey->payload : (array) $survey->payload;
          $meta = $p['meta'] ?? [];
          $general = $p['general'] ?? [];
          $notes = $p['notes'] ?? [];
          $access = $p['accessSecurity'] ?? [];
          $tower = $p['tower'] ?? [];
          $tx = $p['transmission'] ?? [];
          $power = $p['power'] ?? [];
          $civil = $p['civilWorks'] ?? [];
          $mat = $p['materials'] ?? [];
          $civilsRows = is_array($mat['civils'] ?? null) ? $mat['civils'] : [];
          $nteRows = is_array($mat['nte'] ?? null) ? $mat['nte'] : [];
          $defaultCivils = is_array(data_get($materials, 'civils')) ? data_get($materials, 'civils') : [];
          $defaultNte = is_array(data_get($materials, 'nte')) ? data_get($materials, 'nte') : [];
          if (empty($civilsRows)) $civilsRows = $defaultCivils;
          if (empty($nteRows)) $nteRows = $defaultNte;
          $labels = is_array($photoLabels ?? null) ? $photoLabels : [];
          $photosByLabel = collect($survey->photos ?? [])->groupBy('label');
        @endphp

        <form method="POST" action="{{ route('lte-site-surveys.update', $survey->id) }}" enctype="multipart/form-data" class="lte-edit-form">
          @csrf
          @method('PUT')

          <ul class="nav nav-pills gap-2 mb-3" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#lteEditTabGeneral-{{ $survey->id }}" type="button" role="tab">General</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabCoords-{{ $survey->id }}" type="button" role="tab">Coordinates</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabAccess-{{ $survey->id }}" type="button" role="tab">Access/Tower</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabTx-{{ $survey->id }}" type="button" role="tab">Transmission</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabPower-{{ $survey->id }}" type="button" role="tab">Power</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabCivil-{{ $survey->id }}" type="button" role="tab">Civil</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabNotes-{{ $survey->id }}" type="button" role="tab">Notes</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabMat-{{ $survey->id }}" type="button" role="tab">Materials</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lteEditTabPhotos-{{ $survey->id }}" type="button" role="tab">Images</button></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane fade show active" id="lteEditTabGeneral-{{ $survey->id }}" role="tabpanel">
              <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="meta[date]" class="form-control form-control-sm" value="{{ data_get($meta, 'date') }}">
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="mb-3">
                    <label class="form-label">Survey Performed By</label>
                    <select name="meta[surveyPerformedByUserId]" class="form-select form-select-sm js-select2" required>
                      <option value=""></option>
                      @foreach(($users ?? []) as $u)
                        <option value="{{ $u->id }}" {{ (int)data_get($meta, 'surveyPerformedByUserId', 0) === (int)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="general[siteName]" class="form-control form-control-sm" value="{{ data_get($general, 'siteName', $survey->site_name) }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">JC Number</label>
                    <input type="text" name="general[jcNumber]" class="form-control form-control-sm" value="{{ data_get($general, 'jcNumber', $survey->jc_number) }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Province/Region</label>
                    <input type="text" name="general[provinceRegion]" class="form-control form-control-sm" value="{{ data_get($general, 'provinceRegion', $survey->province_region) }}">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Physical Address</label>
                <textarea name="general[physicalAddress]" class="form-control form-control-sm" rows="3">{{ data_get($general, 'physicalAddress', $survey->physical_address) }}</textarea>
              </div>

              <div class="mb-0">
                <label class="form-label">Contact Details</label>
                <textarea name="general[contactDetails]" class="form-control form-control-sm" rows="2">{{ data_get($general, 'contactDetails') }}</textarea>
              </div>
            </div>

            <div class="tab-pane fade" id="lteEditTabCoords-{{ $survey->id }}" role="tabpanel">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Site Latitude</label>
                    <input type="text" name="general[latitude]" class="form-control form-control-sm lte-lat" value="{{ data_get($general, 'latitude', $survey->latitude) }}" placeholder="-17.8292">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Site Longitude</label>
                    <input type="text" name="general[longitude]" class="form-control form-control-sm lte-lng" value="{{ data_get($general, 'longitude', $survey->longitude) }}" placeholder="31.0522">
                  </div>
                </div>
              </div>
              <input type="hidden" name="general[coordinates]" class="lte-coords" value="{{ data_get($general, 'coordinates', $survey->coordinates) }}">
              <div class="alert alert-info mb-0">Coordinates will be auto-combined from latitude and longitude.</div>
            </div>

            <div class="tab-pane fade" id="lteEditTabAccess-{{ $survey->id }}" role="tabpanel">
              <div class="row">
                <div class="col-md-6">
                  <div class="card mb-3">
                    <div class="card-header">Site Access and Security</div>
                    <div class="card-body">
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[securityFenceAvailable]" value="1" id="editFence-{{ $survey->id }}" {{ data_get($access, 'securityFenceAvailable') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editFence-{{ $survey->id }}">Security Fence Available</label>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Condition of Fence</label>
                        <select class="form-select form-select-sm" name="accessSecurity[conditionOfFence]">
                          <option value="">Select</option>
                          <option value="good" {{ data_get($access, 'conditionOfFence') === 'good' ? 'selected' : '' }}>Available (Good)</option>
                          <option value="bad" {{ data_get($access, 'conditionOfFence') === 'bad' ? 'selected' : '' }}>Available (Bad)</option>
                          <option value="not_available" {{ data_get($access, 'conditionOfFence') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>

                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[siteAccess24h]" value="1" id="edit24h-{{ $survey->id }}" {{ data_get($access, 'siteAccess24h') ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit24h-{{ $survey->id }}">24 Hour Site Access</label>
                      </div>

                      <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[guardAvailable]" value="1" id="editGuard-{{ $survey->id }}" {{ data_get($access, 'guardAvailable') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editGuard-{{ $survey->id }}">Guard Available</label>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessSecurity[lineOfSightAvailability]" value="1" id="editLos-{{ $survey->id }}" {{ data_get($access, 'lineOfSightAvailability') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editLos-{{ $survey->id }}">Line of sight Availability</label>
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
                          <option value="hilltop" {{ data_get($tower, 'terrainType') === 'hilltop' ? 'selected' : '' }}>Hilltop</option>
                          <option value="elevated_ground" {{ data_get($tower, 'terrainType') === 'elevated_ground' ? 'selected' : '' }}>Elevated Ground</option>
                          <option value="flat_terrain" {{ data_get($tower, 'terrainType') === 'flat_terrain' ? 'selected' : '' }}>Flat Terrain</option>
                          <option value="valley" {{ data_get($tower, 'terrainType') === 'valley' ? 'selected' : '' }}>Valley</option>
                          <option value="mountain_slope" {{ data_get($tower, 'terrainType') === 'mountain_slope' ? 'selected' : '' }}>Mountain Slope</option>
                          <option value="urban_rooftop" {{ data_get($tower, 'terrainType') === 'urban_rooftop' ? 'selected' : '' }}>Urban Rooftop</option>
                          <option value="other" {{ data_get($tower, 'terrainType') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Tower Owner</label>
                        <input type="text" name="tower[towerOwner]" class="form-control form-control-sm" value="{{ data_get($tower, 'towerOwner') }}">
                      </div>
                      <div class="mb-0">
                        <label class="form-label">Allocated Height</label>
                        <input type="text" name="tower[allocatedHeight]" class="form-control form-control-sm" value="{{ data_get($tower, 'allocatedHeight') }}" placeholder="e.g. 30m">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="lteEditTabTx-{{ $survey->id }}" role="tabpanel">
              <div class="card">
                <div class="card-header">Transmission Details</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Coordinates of nearest manhole</label>
                        <input type="text" name="transmission[nearestManholeCoordinates]" class="form-control form-control-sm" value="{{ data_get($tx, 'nearestManholeCoordinates') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Distance from existing fibre</label>
                        <input type="text" name="transmission[distanceFromExistingFibre]" class="form-control form-control-sm" value="{{ data_get($tx, 'distanceFromExistingFibre') }}">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Distance from nearest POP</label>
                        <input type="text" name="transmission[distanceFromNearestPop]" class="form-control form-control-sm" value="{{ data_get($tx, 'distanceFromNearestPop') }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Distance from nearest POP (alternative)</label>
                        <input type="text" name="transmission[distanceFromNearestPop2]" class="form-control form-control-sm" value="{{ data_get($tx, 'distanceFromNearestPop2') }}">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Allocated Port</label>
                        <input type="text" name="transmission[allocatedPort]" class="form-control form-control-sm" value="{{ data_get($tx, 'allocatedPort') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Required Backhaul Capacity</label>
                        <input type="text" name="transmission[requiredBackhaulCapacity]" class="form-control form-control-sm" value="{{ data_get($tx, 'requiredBackhaulCapacity') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-0">
                        <label class="form-label">Backhaul type</label>
                        <select class="form-select form-select-sm" name="transmission[backhaulType]">
                          <option value="">Select</option>
                          <option value="fibre" {{ data_get($tx, 'backhaulType') === 'fibre' ? 'selected' : '' }}>Fibre</option>
                          <option value="microwave" {{ data_get($tx, 'backhaulType') === 'microwave' ? 'selected' : '' }}>Microwave</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="lteEditTabPower-{{ $survey->id }}" role="tabpanel">
              <div class="card">
                <div class="card-header">Power Details</div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Power Source Type</label>
                        <select class="form-select form-select-sm" name="power[powerSourceType]">
                          <option value="">Select</option>
                          <option value="zesa" {{ data_get($power, 'powerSourceType') === 'zesa' ? 'selected' : '' }}>ZESA</option>
                          <option value="generator" {{ data_get($power, 'powerSourceType') === 'generator' ? 'selected' : '' }}>Generator</option>
                          <option value="solar" {{ data_get($power, 'powerSourceType') === 'solar' ? 'selected' : '' }}>Solar</option>
                          <option value="other" {{ data_get($power, 'powerSourceType') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Single/Three Phase</label>
                        <select class="form-select form-select-sm" name="power[phase]">
                          <option value="">Select</option>
                          <option value="single_phase" {{ data_get($power, 'phase') === 'single_phase' ? 'selected' : '' }}>Single Phase</option>
                          <option value="three_phase" {{ data_get($power, 'phase') === 'three_phase' ? 'selected' : '' }}>Three Phase</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Input Voltage</label>
                        <input type="text" name="power[inputVoltage]" class="form-control form-control-sm" value="{{ data_get($power, 'inputVoltage') }}">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Battery Capacity</label>
                        <input type="text" name="power[batteryCapacity]" class="form-control form-control-sm" value="{{ data_get($power, 'batteryCapacity') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Battery Autonomy (hrs)</label>
                        <input type="text" name="power[batteryAutonomyHrs]" class="form-control form-control-sm" value="{{ data_get($power, 'batteryAutonomyHrs') }}">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label class="form-label">Condition of DB</label>
                        <select class="form-select form-select-sm" name="power[conditionOfDb]">
                          <option value="">Select</option>
                          <option value="good" {{ data_get($power, 'conditionOfDb') === 'good' ? 'selected' : '' }}>Available (Good)</option>
                          <option value="bad" {{ data_get($power, 'conditionOfDb') === 'bad' ? 'selected' : '' }}>Available (Bad)</option>
                          <option value="not_available" {{ data_get($power, 'conditionOfDb') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Earthing System Installed</label>
                        <select class="form-select form-select-sm" name="power[earthingSystemInstalled]">
                          <option value="">Select</option>
                          <option value="available" {{ data_get($power, 'earthingSystemInstalled') === 'available' ? 'selected' : '' }}>Available</option>
                          <option value="not_available" {{ data_get($power, 'earthingSystemInstalled') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-0">
                        <label class="form-label">Cable from Utility Source to Site</label>
                        <select class="form-select form-select-sm" name="power[cableUtilitySourceToSite]">
                          <option value="">Select</option>
                          <option value="available" {{ data_get($power, 'cableUtilitySourceToSite') === 'available' ? 'selected' : '' }}>Available</option>
                          <option value="not_available" {{ data_get($power, 'cableUtilitySourceToSite') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="lteEditTabCivil-{{ $survey->id }}" role="tabpanel">
              <div class="card">
                <div class="card-header">Civil Works Requirement</div>
                <div class="card-body">
                  <div class="row g-2">
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="civilWorks[trenchingRequired]" value="1" id="editTrenching-{{ $survey->id }}" {{ data_get($civil, 'trenchingRequired') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editTrenching-{{ $survey->id }}">Trenching Required</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="civilWorks[breakingConcreteTar]" value="1" id="editBreaking-{{ $survey->id }}" {{ data_get($civil, 'breakingConcreteTar') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editBreaking-{{ $survey->id }}">Breaking Concrete/Tar</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="civilWorks[polePlantingRequired]" value="1" id="editPole-{{ $survey->id }}" {{ data_get($civil, 'polePlantingRequired') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editPole-{{ $survey->id }}">Pole Planting Required</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="civilWorks[constructionOfPlinth]" value="1" id="editPlinth-{{ $survey->id }}" {{ data_get($civil, 'constructionOfPlinth') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editPlinth-{{ $survey->id }}">Construction of Plinth</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="civilWorks[newManholeRequired]" value="1" id="editManhole-{{ $survey->id }}" {{ data_get($civil, 'newManholeRequired') ? 'checked' : '' }}>
                        <label class="form-check-label" for="editManhole-{{ $survey->id }}">New Manhole Required</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="lteEditTabNotes-{{ $survey->id }}" role="tabpanel">
              <div class="card">
                <div class="card-header">Notes (Optional)</div>
                <div class="card-body">
                  <div class="mb-0">
                    <label class="form-label">Notes</label>
                    <textarea name="notes[notes]" class="form-control form-control-sm" rows="6" placeholder="Any additional notes...">{{ data_get($notes, 'notes') }}</textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="lteEditTabMat-{{ $survey->id }}" role="tabpanel">
              <div class="row g-3">
                <div class="col-lg-6">
                  <div class="card mb-0">
                    <div class="card-header">Civils</div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                          <thead>
                            <tr><th>Description</th><th style="width:120px;">Unit</th><th style="width:120px;">Qty</th></tr>
                          </thead>
                          <tbody>
                            @foreach($civilsRows as $i => $row)
                              <tr>
                                <td><input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][description]" value="{{ $row['description'] ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][unit]" value="{{ $row['unit'] ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" name="materials[civils][{{ $i }}][qty]" value="{{ $row['qty'] ?? '' }}"></td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="card mb-0">
                    <div class="card-header">NTE</div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                          <thead>
                            <tr><th>Description</th><th style="width:120px;">Unit</th><th style="width:120px;">Qty</th></tr>
                          </thead>
                          <tbody>
                            @foreach($nteRows as $i => $row)
                              <tr>
                                <td><input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][description]" value="{{ $row['description'] ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][unit]" value="{{ $row['unit'] ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" name="materials[nte][{{ $i }}][qty]" value="{{ $row['qty'] ?? '' }}"></td>
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

            <div class="tab-pane fade" id="lteEditTabPhotos-{{ $survey->id }}" role="tabpanel">
              <div class="card">
                <div class="card-header">Upload More Photos</div>
                <div class="card-body">
                  <div class="row">
                    @foreach($labels as $key => $label)
                      <div class="col-md-6 mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="file" name="photos[{{ $key }}][]" class="form-control form-control-sm" accept="image/*,application/pdf" multiple>
                        @php $items = $photosByLabel->get($key, collect()); @endphp
                        @if($items->count())
                          <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($items as $ph)
                              @php $isImage = str_starts_with((string)($ph->mime_type ?? ''), 'image/'); @endphp
                              @if($isImage)
                                <a href="{{ asset('storage/' . $ph->file_path) }}" target="_blank" class="d-inline-block">
                                  <img src="{{ asset('storage/' . $ph->file_path) }}" alt="" style="width:90px;height:68px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">
                                </a>
                              @else
                                <a href="{{ asset('storage/' . $ph->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                  <i class="fas fa-paperclip me-1"></i> Open
                                </a>
                              @endif
                            @endforeach
                          </div>
                        @endif
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i> Cancel
            </button>
            <div class="d-flex align-items-center gap-2">
              <button type="submit" name="status" value="draft" class="btn btn-warning btn-sm">
                <i class="fas fa-save"></i> Save Draft
              </button>
              <button type="submit" name="status" value="submitted" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane"></i> Submit
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
