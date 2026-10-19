<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-permit')): ?>
<div class="modal custom-modal fade" id="requestPermitEditModal-<?php echo e($fault->id); ?>" tabindex="-1" aria-labelledby="requestPermitEditModalLabel-<?php echo e($fault->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="requestPermitEditModalLabel-<?php echo e($fault->id); ?>">Request Permit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Keeping as UI-only form to match existing edit view behavior -->
      <form>
        <div class="modal-body">
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Requested By</label>
              <input type="text" class="form-control" value="<?php echo e($fault->customer); ?>" disabled>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Fault Number</label>
              <input type="text" class="form-control" placeholder="e.g. F-2025-001">
            </div>
            <div class="mb-3 col">
              <label class="form-label">PTW Number</label>
              <input type="text" class="form-control" placeholder="e.g. PTW-12345">
            </div>
            <div class="mb-3 col">
              <label class="form-label">CR Number</label>
              <input type="text" class="form-control" placeholder="e.g. CR-9876">
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Date of Issue</label>
              <input type="text" class="form-control" placeholder="YYYY-MM-DD">
            </div>
            <div class="mb-3 col">
              <label class="form-label">Start Time</label>
              <input type="text" class="form-control" placeholder="HH:MM">
            </div>
            <div class="mb-3 col">
              <label class="form-label">End Time</label>
              <input type="text" class="form-control" placeholder="HH:MM">
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Priority</label>
              <select class="form-select">
                <option>Select</option>
                <option>Low</option>
                <option>Medium</option>
                <option>Normal</option>
                <option>High</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Description</label>
              <textarea class="form-control" rows="3" placeholder="Work description"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success btn-sm"><i class="fas fa-save me-1"></i>Request</button>
        </div>
      </form>

      <!-- Conversation (Remarks) -->
      <?php if(isset($remarks) && count($remarks)): ?>
      <div class="px-3 pb-2">
        <div class="d-flex align-items-center mb-2">
          <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
          <h6 class="mb-0 text-secondary">Conversation</h6>
        </div>
        <div id="remarksScroller-<?php echo e($fault->id); ?>" class="js-remarks-list legacy-remarks-list" style="max-height: 420px;">
          <?php $__currentLoopData = $remarks->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $currentName = optional(auth()->user())->name;
              $isOwn = $currentName && (strtolower(trim($remark->name)) === strtolower(trim($currentName)));
            ?>
            <div class="legacy-remark-row <?php echo e($isOwn ? 'legacy-remark-row-self' : 'legacy-remark-row-other'); ?>">
              <div class="legacy-remark-bubble <?php echo e($isOwn ? 'legacy-remark-bubble-self' : 'legacy-remark-bubble-other'); ?>">
                <div class="legacy-remark-meta">
                  <span class="badge <?php echo e($isOwn ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($remark->name ?? 'User'); ?></span>
                  <small class="text-muted"><?php echo e(Carbon\Carbon::parse($remark->created_at)->diffForHumans()); ?></small>
                  <?php if(!empty($remark->activity)): ?>
                    <small class="text-muted">• <?php echo e($remark->activity); ?></small>
                  <?php endif; ?>
                </div>
                <div class="legacy-remark-body"><?php echo e($remark->remark); ?></div>
                <?php if($remark->file_path): ?>
                  <div class="mt-2">
                    <img src="<?php echo e(asset('storage/'.$remark->file_path)); ?>" alt="Attachment" class="img-fluid rounded" style="max-height: 160px; object-fit: cover;">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#PicModal-<?php echo e($remark->id); ?>">View</button>
                  </div>
                  <div class="modal custom-modal fade" id="PicModal-<?php echo e($remark->id); ?>" data-bs-backdrop="false" data-bs-keyboard="true" tabindex="-1" aria-labelledby="PicModalLabel-<?php echo e($remark->id); ?>" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                      <div class="modal-content rounded-4 border-0 shadow-lg">
                        <div class="modal-header border-0">
                          <h5 class="modal-title" id="PicModalLabel-<?php echo e($remark->id); ?>"><i class="fas fa-paperclip me-2"></i>Attachment</h5>
                          <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <img src="<?php echo e(asset('storage/'.$remark->file_path)); ?>" alt="Attachment" class="img-fluid rounded">
                        </div>
                        <div class="modal-footer border-0">
                          <a href="<?php echo e(asset('storage/'.$remark->file_path)); ?>" class="btn btn-outline-primary" download><i class="fas fa-download me-1"></i>Download</a>
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Add Remark Form -->
      <div class="px-3 pb-3">
        <form action="/faults/<?php echo e($fault->id); ?>/remarks" method="POST" enctype="multipart/form-data" class="js-remark-form" data-remarks-target="#remarksScroller-<?php echo e($fault->id); ?>">
          <?php echo e(csrf_field()); ?>

          <div class="row g-2 align-items-end">
            <div class="col-md-8">
              <label class="form-label">Add Remark</label>
              <textarea name="remark" class="form-control <?php $__errorArgs = ['remark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="2" placeholder="Enter your message"></textarea>
              <input type="hidden" name="activity" value="ON REQUEST PERMIT">
              <input type="hidden" name="url" value="<?php echo e(url()->current()); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Attachments (optional)</label>
              <input type="file" name="attachments[]" multiple class="form-control <?php $__errorArgs = ['attachments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/png,image/jpg,image/jpeg">
            </div>
          </div>
          <div class="mt-2">
            <button type="submit" class="btn btn-success btn-sm float-end">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('requestPermitEditModal-<?php echo e($fault->id); ?>');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('remarksScroller-<?php echo e($fault->id); ?>');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>
<?php /**PATH /var/www/html/resources/views/permits/requested-permits/edit_modal.blade.php ENDPATH**/ ?>