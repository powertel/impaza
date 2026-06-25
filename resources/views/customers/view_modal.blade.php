@can('customer-list')
@foreach($customers as $customer)
<div class="modal custom-modal fade" id="customerViewModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerViewModalLabel{{ $customer->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="customerViewModalLabel{{ $customer->id }}"><i class="fas fa-eye me-2"></i>Customer Details</h5>
          <div class="text-muted small mt-1">Review customer profile data, linked services, and fault history from the updated business workspace modal.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-users"></i> {{ $customer->customer }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-hashtag"></i> {{ $customer->account_number }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
          <i class="fas fa-circle-info"></i>
          <div>This overview helps validate customer ownership, connected links, and fault history before taking lifecycle actions.</div>
        </div>
        <div class="fault-modal-section mb-3">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-id-card"></i></span>
            <div>
              <div class="fault-modal-section-title">Customer Profile</div>
              <div class="fault-modal-section-subtitle">Core customer identity, ownership, and contact details.</div>
            </div>
          </div>
          <div class="fault-modal-section-body">
            <dl class="row mb-0">
              <dt class="col-sm-5">Customer</dt>
              <dd class="col-sm-7">{{ $customer->customer }}</dd>
              <dt class="col-sm-5">Account Number</dt>
              <dd class="col-sm-7">{{ $customer->account_number }}</dd>
              <dt class="col-sm-5">Address</dt>
              <dd class="col-sm-7">{{ $customer->address ?? '' }}</dd>
              <dt class="col-sm-5">Contact Number</dt>
              <dd class="col-sm-7">{{ $customer->contact_number ?? '' }}</dd>
              <dt class="col-sm-5">POP Aggregator</dt>
              <dd class="col-sm-7">{{ !empty($customer->is_pop_aggregator) ? 'Yes' : 'No' }}</dd>
          @php
            $manager = DB::table('account_managers')
              ->leftJoin('users','account_managers.user_id','=','users.id')
              ->where('account_managers.id', $customer->account_manager_id)
              ->select('users.name as name','account_managers.accountManager as title')
              ->first();
          @endphp
              <dt class="col-sm-5">Account Manager</dt>
              <dd class="col-sm-7">
                @if($manager)
                  <span class="d-block">{{ $manager->name }}</span>
                @else
                  <span class="text-muted">Not set</span>
                @endif
              </dd>
            </dl>
          </div>
        </div>

        @php
          $links = DB::table('links')
            ->leftJoin('cities','links.city_id','=','cities.id')
            ->leftJoin('suburbs','links.suburb_id','=','suburbs.id')
            ->leftJoin('pops','links.pop_id','=','pops.id')
            ->where('links.customer_id', $customer->id)
            ->orderBy('links.link','asc')
            ->get(['links.id','links.link','cities.city','suburbs.suburb','pops.pop']);
          $faults = DB::table('faults')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
            ->where('faults.customer_id', $customer->id)
            ->orderBy('faults.created_at','desc')
            ->get(['faults.id','faults.fault_ref_number','links.link','faults.created_at','statuses.description','faults.status_id','assigned_users.name as assignedTo']);

          $ageByFault = [];
          $nocClearedId = (int) (DB::table('statuses')->where('status_code','CLN')->value('id') ?? 6);
          $faultIds = $faults->pluck('id')->all();
          if (!empty($faultIds)) {
            $clearedLogs = DB::table('fault_stage_logs')
              ->whereIn('fault_id', $faultIds)
              ->where('status_id', $nocClearedId)
              ->select('fault_id','started_at')
              ->get()
              ->keyBy('fault_id');
            foreach ($faults as $ff) {
              $start = \Carbon\Carbon::parse($ff->created_at);
              $end = (isset($clearedLogs[$ff->id]) && (int)$ff->status_id === $nocClearedId)
                ? \Carbon\Carbon::parse($clearedLogs[$ff->id]->started_at)
                : \Carbon\Carbon::now();
              $days = $start->diffInDays($end);
              $hours = $start->copy()->addDays($days)->diffInHours($end) % 24;
              $minutes = $start->copy()->addDays($days)->addHours($hours)->diffInMinutes($end) % 60;
              $ageByFault[$ff->id] = ($days > 0 ? ($days.'d ') : '') . ($hours.'h ') . ($minutes.'m');
            }
          }
        @endphp
        <ul class="nav nav-tabs mb-3" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#custLinksTab{{ $customer->id }}" role="tab">Links</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#custFaultsTab{{ $customer->id }}" role="tab">Faults</a>
          </li>
        </ul>
        <div class="tab-content pt-3">
          <div class="tab-pane fade show active" id="custLinksTab{{ $customer->id }}" role="tabpanel">
            @if($links->count())
              <div class="fault-modal-section mb-3">
                <div class="fault-modal-section-header">
                  <span class="fault-modal-section-icon"><i class="fas fa-link"></i></span>
                  <div>
                    <div class="fault-modal-section-title">Links</div>
                    <div class="fault-modal-section-subtitle">{{ $links->count() }} linked service {{ Str::plural('record', $links->count()) }} for this customer.</div>
                  </div>
                </div>
                <div class="fault-modal-section-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="linksSearch{{ $customer->id }}" class="form-control" placeholder="Search links">
                  </div>
                  <div class="input-group input-group-sm" style="width: 170px;">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="linksPageSize{{ $customer->id }}" class="form-select">
                      <option value="10">Show 10</option>
                      <option value="20" selected>Show 20</option>
                      <option value="50">Show 50</option>
                      <option value="100">Show 100</option>
                      <option value="all">Show All</option>
                    </select>
                  </div>
                  </div>
                  <div class="faults-table-shell">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 js-paginated-table" data-page-size="20" data-page-size-control="#linksPageSize{{ $customer->id }}" data-pager="#linksPager{{ $customer->id }}" data-search="#linksSearch{{ $customer->id }}">
                      <thead>
                        <tr>
                          <th>Link</th>
                          <th>City/Town</th>
                          <th>Location</th>
                          <th>Pop</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($links as $lnk)
                          <tr>
                            <td>{{ $lnk->link }}</td>
                            <td>{{ $lnk->city }}</td>
                            <td>{{ $lnk->suburb }}</td>
                            <td>{{ $lnk->pop }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div id="linksPager{{ $customer->id }}" class="mt-2"></div>
                  </div>
                </div>
              </div>
            @else
              <p class="text-muted mb-0">No links associated with this customer.</p>
            @endif
          </div>
          <div class="tab-pane fade" id="custFaultsTab{{ $customer->id }}" role="tabpanel">
            @if($faults->count())
              <div class="fault-modal-section mb-3">
                <div class="fault-modal-section-header">
                  <span class="fault-modal-section-icon"><i class="fas fa-triangle-exclamation"></i></span>
                  <div>
                    <div class="fault-modal-section-title">Faults</div>
                    <div class="fault-modal-section-subtitle">{{ $faults->count() }} fault {{ Str::plural('record', $faults->count()) }} logged for this customer.</div>
                  </div>
                </div>
                <div class="fault-modal-section-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="faultsSearch{{ $customer->id }}" class="form-control" placeholder="Search faults">
                  </div>
                  <div class="input-group input-group-sm" style="width: 170px;">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="faultsPageSize{{ $customer->id }}" class="form-select">
                      <option value="10">Show 10</option>
                      <option value="20" selected>Show 20</option>
                      <option value="50">Show 50</option>
                      <option value="100">Show 100</option>
                      <option value="all">Show All</option>
                    </select>
                  </div>
                  </div>
                  <div class="faults-table-shell">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 js-paginated-table" data-page-size="20" data-page-size-control="#faultsPageSize{{ $customer->id }}" data-pager="#faultsPager{{ $customer->id }}" data-search="#faultsSearch{{ $customer->id }}">
                      <thead>
                        <tr>
                          <th>Ref No</th>
                          <th>Link</th>
                          <th>Assigned To</th>
                          <th>Status</th>
                          <th>Age</th>
                          <th>Reported On</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($faults as $f)
                          <tr>
                            <td>{{ $f->fault_ref_number }}</td>
                            <td>{{ $f->link }}</td>
                            <td>{{ $f->assignedTo ?? '—' }}</td>
                            <td class="text-nowrap">
                                <x-status-badge :label="$f->description" :color="\App\Models\Status::STATUS_COLOR[$f->description] ?? '#64748B'" :soft="true" />
                            </td>
                            <td>{{ $ageByFault[$f->id] ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($f->created_at)->format('Y-m-d H:i') }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div id="faultsPager{{ $customer->id }}" class="mt-2"></div>
                  </div>
                </div>
              </div>
            @else
              <p class="text-muted mb-0">No faults logged for this customer.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan
