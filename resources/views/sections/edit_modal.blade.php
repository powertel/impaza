@can('department-edit')
@foreach($sections as $section)
<div class="modal fade" id="sectionEditModal{{ $section->id }}" tabindex="-1" aria-labelledby="sectionEditModalLabel{{ $section->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sectionEditModalLabel{{ $section->id }}">Edit Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('sections.update', $section->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Section</label>
            <input type="text" name="section" class="form-control" value="{{ $section->section }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endcan