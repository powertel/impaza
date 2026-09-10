<?php
  $latestRemarkForProgress = collect($remarks ?? [])->sortByDesc('created_at')->first();
?>
<div class="modal custom-modal fade" id="inProgressModal-<?php echo e($fault->id); ?>" tabindex="-1" aria-labelledby="inProgressModalLabel-<?php echo e($fault->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="inProgressModalLabel-<?php echo e($fault->id); ?>">In Progress Remark</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/faults/<?php echo e($fault->id); ?>/remarks" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
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

          <?php if(isset($remarks) && count($remarks)): ?>
          <div class="mt-4">
            <div class="d-flex align-items-center mb-2">
              <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
              <h6 class="mb-0 text-secondary">Conversation</h6>
            </div>
            <!-- Scrollable chat-style conversation -->
            <div id="inProgressRemarksScroller-<?php echo e($fault->id); ?>" class="js-remarks-list legacy-remarks-list" style="max-height: 420px;">
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
                        <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#inProgressPicModal-<?php echo e($remark->id); ?>">View</button>
                      </div>
                      <!-- Remark Attachment Modal -->
                      <div class="modal custom-modal fade" id="inProgressPicModal-<?php echo e($remark->id); ?>" data-bs-backdrop="false" data-bs-keyboard="true" tabindex="-1" aria-labelledby="inProgressPicModalLabel-<?php echo e($remark->id); ?>" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                          <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0">
                              <h5 class="modal-title" id="inProgressPicModalLabel-<?php echo e($remark->id); ?>"><i class="fas fa-paperclip me-2"></i>Attachment</h5>
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
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Switch</label>
                <input type="text" name="switch_name" class="form-control" value="<?php echo e(old('switch_name', $latestRemarkForProgress->switch_name ?? '')); ?>" placeholder="Enter switch name or identifier">
              </div>
              <div class="col-md-6">
                <label class="form-label">Port</label>
                <input type="text" name="port" class="form-control" value="<?php echo e(old('port', $latestRemarkForProgress->port ?? '')); ?>" placeholder="Enter port number or label">
              </div>
              <div class="col-md-12">
                <label class="form-label">Remark</label>
                <textarea name="remark" class="form-control" rows="3" placeholder="Enter remark..." required></textarea>
                <input type="hidden" name="activity" value="ON RECTIFICATION">
              </div>
              <div class="col-md-12">
                <label class="form-label">Attachments (optional)</label>
                <input type="file" name="attachments[]" multiple class="form-control" accept="image/png,image/jpg,image/jpeg">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save Remark
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('inProgressModal-<?php echo e($fault->id); ?>');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('inProgressRemarksScroller-<?php echo e($fault->id); ?>');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>
<?php /**PATH /var/www/html/resources/views/my_faults/in_progress_modal.blade.php ENDPATH**/ ?>