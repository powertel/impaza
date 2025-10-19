@foreach($pops as $pop)
<div class="modal fade" id="popViewModal{{ $pop->id }}" tabindex="-1" aria-labelledby="popViewModalLabel{{ $pop->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="popViewModalLabel{{ $pop->id }}">POP Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-2">
            <div class="col">
                <strong>City/Town</strong>
                <p class="text-muted mb-0">{{ $pop->city }}</p>
            </div>
            <div class="col">
                <strong>Location</strong>
                <p class="text-muted mb-0">{{ $pop->suburb }}</p>
            </div>
            <div class="col">
                <strong>POP</strong>
                <p class="text-muted mb-0">{{ $pop->pop }}</p>
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
        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <strong>Links in this POP</strong>
                <small class="text-muted">{{ $linksForPop->count() }} total</small>
            </div>
            @if($linksForPop->isEmpty())
                <p class="text-muted mb-0">No links connected to this POP.</p>
            @else
                <div class="table-responsive mt-2">
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
                                        <span class="badge rounded-pill" style="background-color: {{ App\Models\LinkStatus::STATUS_COLOR[ $lnk->link_status ] ?? '#6c757d' }}; color: #0d0c0cff; padding: 0.35rem 0.6rem; font-weight: 600;">
                                            {{ $lnk->link_status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
