<div class="modal custom-modal fade" id="lteSurveyViewModal-{{ $survey->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="lteSurveyViewModalLabel-{{ $survey->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0" style="background:#eaf2ff; border-bottom: 1px solid #cfe1ff;">
        <div class="d-flex align-items-start gap-3">
          <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;border-radius:12px;">
            <i class="fas fa-broadcast-tower"></i>
          </span>
          <div>
            <div class="d-flex flex-wrap align-items-center gap-2">
              <h5 class="modal-title mb-0" id="lteSurveyViewModalLabel-{{ $survey->id }}">{{ $survey->site_name ?: 'Untitled Site' }}</h5>
              @if($survey->status === 'submitted')
                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Submitted</span>
              @else
                <span class="badge bg-warning text-dark"><i class="fas fa-pen me-1"></i>Draft</span>
              @endif
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2">
              <span class="badge rounded-pill bg-light text-dark border">
                <i class="fas fa-hashtag me-1"></i>{{ $survey->id }}
              </span>
              <span class="badge rounded-pill bg-light text-dark border">
                <i class="fas fa-id-card me-1"></i>JC: {{ $survey->jc_number ?: '-' }}
              </span>
              <span class="badge rounded-pill bg-light text-dark border">
                <i class="fas fa-map-marked-alt me-1"></i>{{ $survey->province_region ?: '-' }}
              </span>
              <span class="badge rounded-pill bg-light text-dark border">
                <i class="fas fa-user me-1"></i>{{ optional($survey->user)->name ?: '-' }}
              </span>
              <span class="badge rounded-pill bg-light text-dark border">
                <i class="fas fa-calendar-alt me-1"></i>{{ optional($survey->created_at)->format('Y-m-d H:i') ?: '-' }}
              </span>
            </div>
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
          $materials = $p['materials'] ?? [];
          $civils = is_array($materials['civils'] ?? null) ? $materials['civils'] : [];
          $nte = is_array($materials['nte'] ?? null) ? $materials['nte'] : [];
          $labels = is_array($photoLabels ?? null) ? $photoLabels : [];
          $photosByLabel = collect($survey->photos ?? [])->groupBy('label');
          $totalFiles = (int) ($survey->photos_count ?? collect($survey->photos ?? [])->count());

          $lat = $survey->latitude ?? data_get($general, 'latitude');
          $lng = $survey->longitude ?? data_get($general, 'longitude');
          $hasLatLng = $lat !== null && $lat !== '' && $lng !== null && $lng !== '';
          $mapsUrl = $hasLatLng ? ('https://www.google.com/maps?q=' . urlencode(trim((string) $lat) . ',' . trim((string) $lng))) : null;

          $performedBy = data_get($meta, 'surveyPerformedBy') ?: ($survey->survey_performed_by ?: null);
          $surveyDate = data_get($meta, 'date') ?: ($survey->survey_date ? \Illuminate\Support\Carbon::parse($survey->survey_date)->format('Y-m-d') : null);

          $ynBadge = function ($value) {
              return $value
                  ? '<span class="badge bg-success">Yes</span>'
                  : '<span class="badge bg-danger">No</span>';
          };
          $enumBadge = function ($value) {
              $v = trim((string) $value);
              if ($v === '') return '<span class="badge bg-light text-dark border">-</span>';
              $map = [
                  'good' => ['Good', 'success'],
                  'bad' => ['Bad', 'danger'],
                  'not_available' => ['Not Available', 'secondary'],
                  'available' => ['Available', 'success'],
                  'single_phase' => ['Single Phase', 'primary'],
                  'three_phase' => ['Three Phase', 'primary'],
                  'zesa' => ['ZESA', 'primary'],
                  'generator' => ['Generator', 'warning'],
                  'solar' => ['Solar', 'success'],
                  'fibre' => ['Fibre', 'primary'],
                  'microwave' => ['Microwave', 'info'],
              ];
              $label = $map[$v][0] ?? ucfirst(str_replace('_', ' ', $v));
              $color = $map[$v][1] ?? 'light text-dark border';
              return '<span class="badge bg-' . $color . '">' . e($label) . '</span>';
          };
          $info = function ($val) {
              $v = is_string($val) ? trim($val) : $val;
              return ($v === null || $v === '') ? '<span class="text-muted">—</span>' : '<span class="fw-semibold">' . e((string) $v) . '</span>';
          };
        @endphp
        <div class="p-3">
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div>
                    <div class="text-muted small">Survey Date</div>
                    <div class="fw-bold">{!! $info($surveyDate) !!}</div>
                  </div>
                  <span class="badge bg-light text-dark border"><i class="fas fa-calendar-day"></i></span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div>
                    <div class="text-muted small">Performed By</div>
                    <div class="fw-bold">{!! $info($performedBy) !!}</div>
                  </div>
                  <span class="badge bg-light text-dark border"><i class="fas fa-user"></i></span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div>
                    <div class="text-muted small">Backhaul</div>
                    <div class="fw-bold">{!! $enumBadge(data_get($tx, 'backhaulType')) !!}</div>
                  </div>
                  <span class="badge bg-light text-dark border"><i class="fas fa-network-wired"></i></span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div>
                    <div class="text-muted small">Files</div>
                    <div class="fs-5 fw-bold">{{ $totalFiles }}</div>
                    @if($mapsUrl)
                      <a class="small text-decoration-none" href="{{ $mapsUrl }}" target="_blank" rel="noopener">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>Open Map
                      </a>
                    @endif
                  </div>
                  <span class="badge bg-light text-dark border"><i class="fas fa-paperclip"></i></span>
                </div>
              </div>
            </div>
          </div>

          <div class="accordion" id="lteSurveyViewAcc-{{ $survey->id }}">
            <div class="accordion-item border-0 shadow-sm mb-3">
              <h2 class="accordion-header" id="lteSurveyAccHead1-{{ $survey->id }}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#lteSurveyAcc1-{{ $survey->id }}" aria-expanded="true" aria-controls="lteSurveyAcc1-{{ $survey->id }}">
                  <i class="fas fa-info-circle me-2 text-primary"></i>General & Contacts
                </button>
              </h2>
              <div id="lteSurveyAcc1-{{ $survey->id }}" class="accordion-collapse collapse show" aria-labelledby="lteSurveyAccHead1-{{ $survey->id }}" data-bs-parent="#lteSurveyViewAcc-{{ $survey->id }}">
                <div class="accordion-body">
                  <div class="row g-4">
                    <div class="col-lg-6">
                      <div class="card border-0 shadow-sm h-100 rounded-3">
                        <div class="card-header bg-transparent border-0">
                          <h6 class="mb-0 text-secondary"><i class="fas fa-info-circle me-2 text-primary"></i>General</h6>
                        </div>
                        <ul class="list-group list-group-flush">
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Date</span><span class="fw-semibold">{{ data_get($meta, 'date') ?: '-' }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Performed By</span><span class="fw-semibold">{{ data_get($meta, 'surveyPerformedBy') ?: ($survey->survey_performed_by ?: '-') }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Site Name</span><span class="fw-semibold">{{ data_get($general, 'siteName') ?: ($survey->site_name ?: '-') }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">JC Number</span><span class="fw-semibold">{{ data_get($general, 'jcNumber') ?: ($survey->jc_number ?: '-') }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Province/Region</span><span class="fw-semibold">{{ data_get($general, 'provinceRegion') ?: ($survey->province_region ?: '-') }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Coordinates</span><span class="fw-semibold">{{ $survey->coordinates ?: (data_get($general, 'coordinates') ?: '-') }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Latitude</span><span class="fw-semibold">{{ $survey->latitude ?: (data_get($general, 'latitude') ?: '-') }}</span></li>
                          <li class="list-group-item d-flex justify-content-between align-items-start"><span class="text-muted">Longitude</span><span class="fw-semibold">{{ $survey->longitude ?: (data_get($general, 'longitude') ?: '-') }}</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="card border-0 shadow-sm h-100 rounded-3">
                        <div class="card-header bg-transparent border-0">
                          <h6 class="mb-0 text-secondary"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Address & Contacts</h6>
                        </div>
                        <div class="card-body">
                          <div class="mb-3">
                            <div class="text-muted small mb-1">Physical Address</div>
                            <div class="fw-semibold">{{ data_get($general, 'physicalAddress') ?: ($survey->physical_address ?: '-') }}</div>
                          </div>
                          <div class="mb-0">
                            <div class="text-muted small mb-1">Contact Details</div>
                            <div class="fw-semibold">{{ data_get($general, 'contactDetails') ?: '-' }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3">
              <h2 class="accordion-header" id="lteSurveyAccHead2-{{ $survey->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lteSurveyAcc2-{{ $survey->id }}" aria-expanded="false" aria-controls="lteSurveyAcc2-{{ $survey->id }}">
                  <i class="fas fa-shield-alt me-2 text-primary"></i>Access, Tower, Transmission, Power
                </button>
              </h2>
              <div id="lteSurveyAcc2-{{ $survey->id }}" class="accordion-collapse collapse" aria-labelledby="lteSurveyAccHead2-{{ $survey->id }}" data-bs-parent="#lteSurveyViewAcc-{{ $survey->id }}">
                <div class="accordion-body">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-0">
                      <h6 class="mb-0 text-secondary"><i class="fas fa-shield-alt me-2 text-primary"></i>Access, Tower, Transmission, Power</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-lg-3">
                          <div class="text-muted small mb-2">Access & Security</div>
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Fence</span><span>{!! $ynBadge((bool) data_get($access, 'securityFenceAvailable')) !!}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Fence Condition</span><span>{!! $enumBadge(data_get($access, 'conditionOfFence')) !!}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>24h Access</span><span>{!! $ynBadge((bool) data_get($access, 'siteAccess24h')) !!}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Guard</span><span>{!! $ynBadge((bool) data_get($access, 'guardAvailable')) !!}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Line of Sight</span><span>{!! $ynBadge((bool) data_get($access, 'lineOfSightAvailability')) !!}</span></li>
                          </ul>
                        </div>
                        <div class="col-lg-3">
                          <div class="text-muted small mb-2">Tower</div>
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Terrain</span><span class="fw-semibold">{{ data_get($tower, 'terrainType') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Owner</span><span class="fw-semibold">{{ data_get($tower, 'towerOwner') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Height</span><span class="fw-semibold">{{ data_get($tower, 'allocatedHeight') ?: '-' }}</span></li>
                          </ul>
                        </div>
                        <div class="col-lg-3">
                          <div class="text-muted small mb-2">Transmission</div>
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Nearest Manhole</span><span class="fw-semibold">{{ data_get($tx, 'nearestManholeCoordinates') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Existing Fibre</span><span class="fw-semibold">{{ data_get($tx, 'distanceFromExistingFibre') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Nearest POP</span><span class="fw-semibold">{{ data_get($tx, 'distanceFromNearestPop') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>POP (Alt)</span><span class="fw-semibold">{{ data_get($tx, 'distanceFromNearestPop2') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Backhaul</span><span>{!! $enumBadge(data_get($tx, 'backhaulType')) !!}</span></li>
                          </ul>
                        </div>
                        <div class="col-lg-3">
                          <div class="text-muted small mb-2">Power</div>
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Source</span><span>{!! $enumBadge(data_get($power, 'powerSourceType')) !!}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Phase</span><span>{!! $enumBadge(data_get($power, 'phase')) !!}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Voltage</span><span class="fw-semibold">{{ data_get($power, 'inputVoltage') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Battery</span><span class="fw-semibold">{{ data_get($power, 'batteryCapacity') ?: '-' }}</span></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>DB</span><span>{!! $enumBadge(data_get($power, 'conditionOfDb')) !!}</span></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3">
              <h2 class="accordion-header" id="lteSurveyAccHead3-{{ $survey->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lteSurveyAcc3-{{ $survey->id }}" aria-expanded="false" aria-controls="lteSurveyAcc3-{{ $survey->id }}">
                  <i class="fas fa-hard-hat me-2 text-primary"></i>Civil Works
                </button>
              </h2>
              <div id="lteSurveyAcc3-{{ $survey->id }}" class="accordion-collapse collapse" aria-labelledby="lteSurveyAccHead3-{{ $survey->id }}" data-bs-parent="#lteSurveyViewAcc-{{ $survey->id }}">
                <div class="accordion-body">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-0">
                      <h6 class="mb-0 text-secondary"><i class="fas fa-hard-hat me-2 text-primary"></i>Civil Works</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-4 d-flex justify-content-between align-items-center"><span class="text-muted">Trenching</span><span>{!! $ynBadge((bool) data_get($civil, 'trenchingRequired')) !!}</span></div>
                        <div class="col-md-4 d-flex justify-content-between align-items-center"><span class="text-muted">Breaking Concrete/Tar</span><span>{!! $ynBadge((bool) data_get($civil, 'breakingConcreteTar')) !!}</span></div>
                        <div class="col-md-4 d-flex justify-content-between align-items-center"><span class="text-muted">Pole Planting</span><span>{!! $ynBadge((bool) data_get($civil, 'polePlantingRequired')) !!}</span></div>
                        <div class="col-md-4 d-flex justify-content-between align-items-center"><span class="text-muted">Plinth Construction</span><span>{!! $ynBadge((bool) data_get($civil, 'constructionOfPlinth')) !!}</span></div>
                        <div class="col-md-4 d-flex justify-content-between align-items-center"><span class="text-muted">New Manhole</span><span>{!! $ynBadge((bool) data_get($civil, 'newManholeRequired')) !!}</span></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3">
              <h2 class="accordion-header" id="lteSurveyAccHead4-{{ $survey->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lteSurveyAcc4-{{ $survey->id }}" aria-expanded="false" aria-controls="lteSurveyAcc4-{{ $survey->id }}">
                  <i class="fas fa-sticky-note me-2 text-primary"></i>Notes
                </button>
              </h2>
              <div id="lteSurveyAcc4-{{ $survey->id }}" class="accordion-collapse collapse" aria-labelledby="lteSurveyAccHead4-{{ $survey->id }}" data-bs-parent="#lteSurveyViewAcc-{{ $survey->id }}">
                <div class="accordion-body">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-0">
                      <h6 class="mb-0 text-secondary"><i class="fas fa-sticky-note me-2 text-primary"></i>Notes</h6>
                    </div>
                    <div class="card-body">
                      <div class="fw-semibold" style="white-space: pre-wrap;">{{ data_get($notes, 'notes') ?: '-' }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3">
              <h2 class="accordion-header" id="lteSurveyAccHead5-{{ $survey->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lteSurveyAcc5-{{ $survey->id }}" aria-expanded="false" aria-controls="lteSurveyAcc5-{{ $survey->id }}">
                  <i class="fas fa-boxes me-2 text-primary"></i>Materials
                </button>
              </h2>
              <div id="lteSurveyAcc5-{{ $survey->id }}" class="accordion-collapse collapse" aria-labelledby="lteSurveyAccHead5-{{ $survey->id }}" data-bs-parent="#lteSurveyViewAcc-{{ $survey->id }}">
                <div class="accordion-body">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-0">
                      <h6 class="mb-0 text-secondary"><i class="fas fa-boxes me-2 text-primary"></i>Materials</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-lg-6">
                          <div class="fw-semibold mb-2">Civils</div>
                          <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                              <thead><tr><th>Description</th><th>Unit</th><th class="text-end">Qty</th></tr></thead>
                              <tbody>
                                @forelse($civils as $row)
                                  <tr>
                                    <td>{{ $row['description'] ?? '-' }}</td>
                                    <td>{{ $row['unit'] ?? '-' }}</td>
                                    <td class="text-end">{{ $row['qty'] ?? '-' }}</td>
                                  </tr>
                                @empty
                                  <tr><td colspan="3" class="text-muted text-center">No items</td></tr>
                                @endforelse
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="fw-semibold mb-2">NTE</div>
                          <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                              <thead><tr><th>Description</th><th>Unit</th><th class="text-end">Qty</th></tr></thead>
                              <tbody>
                                @forelse($nte as $row)
                                  <tr>
                                    <td>{{ $row['description'] ?? '-' }}</td>
                                    <td>{{ $row['unit'] ?? '-' }}</td>
                                    <td class="text-end">{{ $row['qty'] ?? '-' }}</td>
                                  </tr>
                                @empty
                                  <tr><td colspan="3" class="text-muted text-center">No items</td></tr>
                                @endforelse
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

            <div class="accordion-item border-0 shadow-sm">
              <h2 class="accordion-header" id="lteSurveyAccHead6-{{ $survey->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lteSurveyAcc6-{{ $survey->id }}" aria-expanded="false" aria-controls="lteSurveyAcc6-{{ $survey->id }}">
                  <i class="fas fa-images me-2 text-primary"></i>Photos & Attachments
                </button>
              </h2>
              <div id="lteSurveyAcc6-{{ $survey->id }}" class="accordion-collapse collapse" aria-labelledby="lteSurveyAccHead6-{{ $survey->id }}" data-bs-parent="#lteSurveyViewAcc-{{ $survey->id }}">
                <div class="accordion-body">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-0">
                      <h6 class="mb-0 text-secondary"><i class="fas fa-images me-2 text-primary"></i>Photos</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        @foreach($labels as $key => $label)
                          @php $items = $photosByLabel->get($key, collect()); @endphp
                          <div class="col-lg-6">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                              <div class="fw-semibold">{{ $label }}</div>
                              <span class="badge bg-light text-dark border">{{ $items->count() }}</span>
                            </div>
                            @if($items->count())
                              <div class="d-flex flex-wrap gap-2">
                                @foreach($items as $ph)
                                  @php $isImage = str_starts_with((string)($ph->mime_type ?? ''), 'image/'); @endphp
                                  @if($isImage)
                                    <a href="{{ asset('storage/' . $ph->file_path) }}" target="_blank" class="d-inline-block">
                                      <img src="{{ asset('storage/' . $ph->file_path) }}" alt="" style="width:120px;height:90px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">
                                    </a>
                                  @else
                                    <a href="{{ asset('storage/' . $ph->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                      <i class="fas fa-paperclip me-1"></i> Open Attachment
                                    </a>
                                  @endif
                                @endforeach
                              </div>
                            @else
                              <div class="text-muted">No files</div>
                            @endif
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#lteSurveyEditModal-{{ $survey->id }}">
          <i class="fas fa-edit me-1"></i> Edit
        </button>
      </div>
    </div>
  </div>
</div>
