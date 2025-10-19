
<div class="modal fade" id="createRfoModal" tabindex="-1" aria-labelledby="createRfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createRfoModalLabel">Create Reason For Outage</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('rfos.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="createRfoInput">Reason For Outage</label>
            <input type="text" id="createRfoInput" name="RFO" class="form-control @error('RFO') is-invalid @enderror" placeholder="Reason For Outage" value="{{ old('RFO') }}" required>
            @error('RFO')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
