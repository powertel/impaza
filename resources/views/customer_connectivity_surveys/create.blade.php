@extends('layouts.admin')

@section('title')
New Customer Connectivity Survey
@endsection

@section('content')
<section class="content ux-unified">
  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
            <i class="fas fa-wifi text-primary me-3"></i>
            New Customer Connectivity Survey
          </h3>
          <p class="text-sm text-gray-600 mb-0 mt-1 me-3">Fill in the survey details and submit</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('customer-connectivity-surveys.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back
          </a>
        </div>
      </div>
    </div>

    <div class="card-body">
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

      <form method="POST" action="{{ route('customer-connectivity-surveys.store') }}" enctype="multipart/form-data" id="ccSurveyForm">
        @csrf
        <input type="hidden" name="status" id="ccSurveyStatus" value="draft">

        <div class="card mb-3">
          <div class="card-header">Meta</div>
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
                  <select name="meta[surveyPerformedByUserId]" class="form-select form-select-sm js-select2 cc-performed-by-user" data-placeholder="Select user">
                    <option value=""></option>
                    @foreach(($users ?? []) as $u)
                      <option value="{{ $u->id }}" {{ (int)old('meta.surveyPerformedByUserId', optional(auth()->user())->id) === (int)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                  </select>
                  <input type="hidden" name="meta[surveyPerformedBy]" class="cc-performed-by-name" value="{{ old('meta.surveyPerformedBy', optional(auth()->user())->name) }}">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header">General</div>
          <div class="card-body">
            <div class="row">
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
                  <label class="form-label">Coordinates (optional)</label>
                  <input type="text" name="general[coordinates]" class="form-control form-control-sm" value="{{ old('general.coordinates') }}" placeholder="lat, lng">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label">Physical Address</label>
                  <textarea name="general[physicalAddress]" class="form-control form-control-sm" rows="2">{{ old('general.physicalAddress') }}</textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Latitude</label>
                  <input type="text" name="general[latitude]" class="form-control form-control-sm" value="{{ old('general.latitude') }}" placeholder="-17.8292">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Longitude</label>
                  <input type="text" name="general[longitude]" class="form-control form-control-sm" value="{{ old('general.longitude') }}" placeholder="31.0522">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Customer Contact (Name)</label>
                  <input type="text" name="general[customerContactName]" class="form-control form-control-sm" value="{{ old('general.customerContactName') }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input type="text" name="general[customerContactPhone]" class="form-control form-control-sm" value="{{ old('general.customerContactPhone') }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="text" name="general[customerContactEmail]" class="form-control form-control-sm" value="{{ old('general.customerContactEmail') }}">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
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
                <div class="mb-3">
                  <label class="form-label">VLAN / Routing Notes</label>
                  <textarea name="serviceRequirements[vlanNotes]" class="form-control form-control-sm" rows="2">{{ old('serviceRequirements.vlanNotes') }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
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
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="permissions[notes]" class="form-control form-control-sm" rows="2">{{ old('permissions.notes') }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
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
                <div class="mb-3">
                  <label class="form-label">New Proposed Manholes / Poles (Grid refs)</label>
                  <textarea name="outdoor[proposedRefs]" class="form-control form-control-sm" rows="2">{{ old('outdoor.proposedRefs') }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
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
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="indoor[notes]" class="form-control form-control-sm" rows="2">{{ old('indoor.notes') }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
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

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-outline-primary btn-sm" data-cc-status="draft">
            <i class="fas fa-save me-1"></i> Save Draft
          </button>
          <button type="button" class="btn btn-success btn-sm" data-cc-status="submitted">
            <i class="fas fa-check me-1"></i> Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function () {
    var form = document.getElementById('ccSurveyForm');
    var status = document.getElementById('ccSurveyStatus');
    if (!form || !status) return;

    function setSubmitRequired(on) {
      var req = [
        'input[name="general[customerName]"]',
        'input[name="general[siteName]"]'
      ];
      req.forEach(function (sel) {
        var el = form.querySelector(sel);
        if (!el) return;
        if (on) el.setAttribute('required', 'required');
        else el.removeAttribute('required');
      });
    }

    function syncPerformedByName() {
      var sel = form.querySelector('select[name="meta[surveyPerformedByUserId]"]');
      var hid = form.querySelector('input[name="meta[surveyPerformedBy]"]');
      if (!sel || !hid) return;
      var opt = sel.options && sel.selectedIndex >= 0 ? sel.options[sel.selectedIndex] : null;
      hid.value = opt ? (opt.text || '').trim() : '';
    }

    var perfSel = form.querySelector('select[name="meta[surveyPerformedByUserId]"]');
    if (perfSel) {
      perfSel.addEventListener('change', syncPerformedByName);
      syncPerformedByName();
    }

    document.querySelectorAll('[data-cc-status]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var val = btn.getAttribute('data-cc-status') || 'draft';
        status.value = val;
        setSubmitRequired(val === 'submitted');
        if (val === 'submitted' && typeof form.checkValidity === 'function' && !form.checkValidity()) {
          try {
            var invalid = form.querySelector(':invalid');
            if (invalid) {
              if (typeof invalid.reportValidity === 'function') invalid.reportValidity();
              else invalid.focus();
            }
          } catch (e) {
          }
          return;
        }
        form.submit();
      });
    });
  })();
</script>
@endsection
