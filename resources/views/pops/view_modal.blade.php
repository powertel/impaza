@foreach($pops as $pop)
<div class="modal custom-modal fade" id="popViewModal{{ $pop->id }}" tabindex="-1" aria-labelledby="popViewModalLabel{{ $pop->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="popViewModalLabel{{ $pop->id }}"><i class="fas fa-eye me-2"></i>POP Details</h5>
          <div class="text-muted small mt-1">Review the POP hierarchy and the links currently associated with this point of presence.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> {{ $pop->city }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map-marker-alt"></i> {{ $pop->suburb }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-bullseye"></i> {{ $pop->pop }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>This overview helps confirm the POP hierarchy and connected services before you make updates elsewhere in the network modules.</div>
        </div>

        <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-bullseye"></i></span>
                <div>
                    <div class="fault-modal-section-title">POP Overview</div>
                    <div class="fault-modal-section-subtitle">Key location details for this POP record.</div>
                </div>
            </div>
            <div class="fault-modal-section-body">
                <div class="fault-modal-grid">
                    <div class="fault-modal-kv">
                        <span class="fault-modal-kv-label">City/Town</span>
                        <div class="fault-modal-kv-value">{{ $pop->city }}</div>
                    </div>
                    <div class="fault-modal-kv">
                        <span class="fault-modal-kv-label">Location</span>
                        <div class="fault-modal-kv-value">{{ $pop->suburb }}</div>
                    </div>
                    <div class="fault-modal-kv">
                        <span class="fault-modal-kv-label">POP</span>
                        <div class="fault-modal-kv-value">{{ $pop->pop }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        @php
            $linksForPop = DB::table('links')
                ->leftJoin('customers','links.customer_id','=','customers.id')
                ->leftJoin('link_statuses','links.link_status','=','link_statuses.id')
                ->where('links.pop_id', $pop->id)
                ->orderBy('customers.customer', 'asc')
                ->get(['links.id','links.link','customers.customer','link_statuses.link_status']);
        @endphp
        <div class="fault-modal-section">
            <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-link"></i></span>
                <div>
                    <div class="fault-modal-section-title">Links in this POP</div>
                    <div class="fault-modal-section-subtitle">{{ $linksForPop->count() }} linked service {{ Str::plural('record', $linksForPop->count()) }} currently mapped here.</div>
                </div>
            </div>
            <div class="fault-modal-section-body">
                @if($linksForPop->isEmpty())
                    <div class="fault-modal-empty">No links connected to this POP.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Link</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($linksForPop as $lnk)
                                    <tr>
                                        <td>{{ $lnk->link }}</td>
                                        <td>{{ $lnk->customer }}</td>
                                        <td class="text-nowrap">
                                            <x-status-badge :label="$lnk->link_status" :color="\App\Models\LinkStatus::STATUS_COLOR[$lnk->link_status] ?? '#64748B'" :soft="true" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
