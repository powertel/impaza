<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rectify-fault')): ?>
<?php
  $latestRemarkForRectify = collect($remarks ?? [])->sortByDesc('created_at')->first();
?>
<div class="modal custom-modal fade" id="rectifyEditModal-<?php echo e($fault->id); ?>" tabindex="-1" aria-labelledby="rectifyEditModalLabel-<?php echo e($fault->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rectifyEditModalLabel-<?php echo e($fault->id); ?>">Fault Rectification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Customer</label>
            <input type="text" class="form-control" value="<?php echo e($fault->customer); ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Link</label>
            <input type="text" class="form-control" value="<?php echo e($fault->link); ?>" disabled>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Confirmed Reason For Outage</label>
          <select class="form-select <?php $__errorArgs = ['confirmedRfo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="confirmedRfo_id" form="rectify-form-<?php echo e($fault->id); ?>" id="confirmedRfoSelect-<?php echo e($fault->id); ?>" required>
            <option selected disabled>Select RFO</option>
            <?php $__currentLoopData = ($confirmedRFO ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $confirmed_rfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($confirmed_rfo->id); ?>"><?php echo e($confirmed_rfo->RFO); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <?php if(isset($remarks) && count($remarks)): ?>
        <div class="mt-4">
          <div class="d-flex align-items-center mb-2">
            <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
            <h6 class="mb-0 text-secondary">Conversation</h6>
          </div>
          <!-- Scrollable chat-style conversation -->
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
                  <?php if(!empty($remark->switch_name) || !empty($remark->port)): ?>
                    <div class="mt-2 d-flex flex-wrap gap-2">
                      <?php if(!empty($remark->switch_name)): ?>
                        <span class="badge rounded-pill bg-light text-dark border">Switch: <?php echo e($remark->switch_name); ?></span>
                      <?php endif; ?>
                      <?php if(!empty($remark->port)): ?>
                        <span class="badge rounded-pill bg-light text-dark border">Port: <?php echo e($remark->port); ?></span>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <?php if($remark->file_path): ?>
                    <div class="mt-2">
                      <img src="<?php echo e(asset('storage/'.$remark->file_path)); ?>" alt="Attachment" class="img-fluid rounded" style="max-height: 160px; object-fit: cover;">
                      <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#PicModal-<?php echo e($remark->id); ?>">View</button>
                    </div>
                    <!-- Remark Attachment Modal -->
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

        <div class="mt-3">
          <form action="/faults/<?php echo e($fault->id); ?>/remarks" method="POST" enctype="multipart/form-data" class="js-remark-form" data-remarks-target="#remarksScroller-<?php echo e($fault->id); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label">Switch</label>
                <input type="text" name="switch_name" class="form-control" value="<?php echo e(old('switch_name', $latestRemarkForRectify->switch_name ?? '')); ?>" placeholder="Enter switch name or identifier">
              </div>
              <div class="col-md-6">
                <label class="form-label">Port</label>
                <input type="text" name="port" class="form-control" value="<?php echo e(old('port', $latestRemarkForRectify->port ?? '')); ?>" placeholder="Enter port number or label">
              </div>
              <div class="col-md-12">
                <label class="form-label">Add Remark</label>
                <textarea name="remark" class="form-control <?php $__errorArgs = ['remark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="2" placeholder="Enter your message" id="rectifyRemark-<?php echo e($fault->id); ?>"></textarea>
                <input type="hidden" name="activity" value="ON RECTIFICATION">
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
              <button type="submit" class="btn btn-success btn-sm float-end">Add Remark</button>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <form action="<?php echo e(route('rectify.update', $fault->id )); ?>" method="POST" class="d-inline" id="rectify-form-<?php echo e($fault->id); ?>">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          <button type="submit" class="btn btn-outline-success btn-sm d-none" id="restoreBtn-<?php echo e($fault->id); ?>">
            <i class="fas fa-undo-alt me-1"></i> Restore
          </button>
        </form>
      </div>
  </div>
</div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('rectifyEditModal-<?php echo e($fault->id); ?>');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('remarksScroller-<?php echo e($fault->id); ?>');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
  var rfoSel = document.getElementById('confirmedRfoSelect-<?php echo e($fault->id); ?>');
  var remarkTa = document.getElementById('rectifyRemark-<?php echo e($fault->id); ?>');
  var restoreBtn = document.getElementById('restoreBtn-<?php echo e($fault->id); ?>');
  function updateRestoreVisibility(){
    var hasRfo = !!(rfoSel && rfoSel.value && rfoSel.value !== '' && rfoSel.value !== 'Select RFO');
    var hasRemark = !!(remarkTa && remarkTa.value && remarkTa.value.trim().length > 0);
    if (restoreBtn){
      if (hasRfo && hasRemark){
        restoreBtn.classList.remove('d-none');
        restoreBtn.disabled = false;
      } else {
        restoreBtn.classList.add('d-none');
        restoreBtn.disabled = true;
      }
    }
  }
  if (rfoSel){ rfoSel.addEventListener('change', updateRestoreVisibility); }
  if (remarkTa){ remarkTa.addEventListener('input', updateRestoreVisibility); }
  updateRestoreVisibility();
});
</script>
<?php /**PATH /var/www/html/resources/views/rectification/edit_modal.blade.php ENDPATH**/ ?>