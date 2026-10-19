<?php
  $latestRemarkForEscalation = collect($remarks ?? [])->sortByDesc('created_at')->first();
?>
<div class="modal custom-modal fade" id="escalateModal-<?php echo e($fault->id); ?>" tabindex="-1" aria-labelledby="escalateModalLabel-<?php echo e($fault->id); ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="escalateModalLabel-<?php echo e($fault->id); ?>">Escalate Fault to Chief Technician</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('my_faults.escalate', $fault->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Switch</label>
              <input type="text" name="switch_name" class="form-control" value="<?php echo e(old('switch_name', $latestRemarkForEscalation->switch_name ?? '')); ?>" placeholder="Enter switch name or identifier">
            </div>
            <div class="col-md-6">
              <label class="form-label">Port</label>
              <input type="text" name="port" class="form-control" value="<?php echo e(old('port', $latestRemarkForEscalation->port ?? '')); ?>" placeholder="Enter port number or label">
            </div>
            <div class="col-md-12">
              <label class="form-label">Reason / Context</label>
              <textarea name="remark" class="form-control" rows="3" placeholder="Provide context for escalation" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="fas fa-level-up-alt me-1"></i>Escalate
          </button>
        </div>
      </form>

      <?php if(isset($remarks) && count($remarks)): ?>
      <div class="px-3 pb-2">
        <div class="d-flex align-items-center mb-2">
          <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
          <h6 class="mb-0 text-secondary">Conversation</h6>
        </div>
        <div id="escalateRemarksScroller-<?php echo e($fault->id); ?>" class="js-remarks-list legacy-remarks-list" style="max-height: 420px;">
          <?php $__currentLoopData = ($remarks ?? collect())->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                <?php if(!empty($remark->file_path)): ?>
                  <div class="mt-2">
                    <img src="<?php echo e(asset('storage/'.$remark->file_path)); ?>" alt="Attachment" class="img-fluid rounded" style="max-height: 160px; object-fit: cover;">
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('escalateModal-<?php echo e($fault->id); ?>');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('escalateRemarksScroller-<?php echo e($fault->id); ?>');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>

<?php /**PATH /var/www/html/resources/views/my_faults/escalate_modal.blade.php ENDPATH**/ ?>