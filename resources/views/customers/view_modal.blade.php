@can('customer-list')
@foreach($customers as $customer)
<div class="modal fade" id="customerViewModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerViewModalLabel{{ $customer->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerViewModalLabel{{ $customer->id }}">Customer Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Customer</dt>
          <dd class="col-sm-7">{{ $customer->customer }}</dd>
          <dt class="col-sm-5">Account Number</dt>
          <dd class="col-sm-7">{{ $customer->account_number }}</dd>
        </dl>

        <hr class="my-3">
        <h6 class="mb-2">Links</h6>
        @php
          $links = DB::table('links')
            ->leftjoin('cities','links.city_id','=','cities.id')
            ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
            ->leftjoin('pops','links.pop_id','=','pops.id')
            ->where('links.customer_id', $customer->id)
            ->orderBy('cities.city', 'asc')
            ->get(['links.id','links.link','cities.city','suburbs.suburb','pops.pop']);
        @endphp
        @if($links->count())
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light">
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
        @else
          <p class="text-muted mb-0">No links associated with this customer.</p>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan