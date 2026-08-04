<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-edit')): ?>
<div class="modal custom-modal fade" id="editExistingLinksModal" tabindex="-1" aria-labelledby="editExistingLinksLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl  modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="editExistingLinksLabel"><i class="fas fa-pen-to-square me-2"></i>Edit Existing Links</h5>
          <div class="text-muted small mt-1">Load customer links into the bulk edit workspace and update service records without leaving the page.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
          <i class="fas fa-circle-info"></i>
          <div>Select a customer to load all associated links into the editable bulk table below.</div>
        </div>
        <div class="fault-modal-section mb-3">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-users"></i></span>
            <div>
              <div class="fault-modal-section-title">Customer Selection</div>
              <div class="fault-modal-section-subtitle">Choose the customer whose links you want to edit in bulk.</div>
            </div>
          </div>
          <div class="fault-modal-section-body">
            <div class="mb-0">
              <label class="form-label">Customer</label>
              <select id="editLinksCustomer" class="form-select select2">
                <option selected disabled>Select Customer</option>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($cust->id); ?>"><?php echo e($cust->customer); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
        </div>

        <div class="fault-modal-section">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-table"></i></span>
            <div>
              <div class="fault-modal-section-title">Bulk Edit Table</div>
              <div class="fault-modal-section-subtitle">Loaded link records appear here for in-place updates.</div>
            </div>
          </div>
          <div class="fault-modal-section-body p-0">
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead>
                  <tr>
                    <th style="width: 24%">Link</th>
                    <th style="width: 14%">City/Town</th>
                    <th style="width: 14%">Location</th>
                    <th style="width: 14%">Pop</th>
                    <th style="width: 13%">Service Type</th>
                    <th style="width: 11%">Capacity</th>
                    <th style="width: 10%">Link Type</th>
                    <th style="width: 6%">#</th>
                  </tr>
                </thead>
                <tbody id="editExistingLinksBody">
                  <tr class="text-center text-muted"><td colspan="8">Select a customer to load links…</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Hidden templates for select options -->
        <div id="editLinksSelectTemplates" class="d-none">
          <select id="editLinksCitiesTpl">
            
            <?php $uniqueCities = collect($cities)->unique('id'); ?>
            <?php $__currentLoopData = $uniqueCities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($city->id); ?>"><?php echo e($city->city); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <select id="editLinksLinkTypesTpl">
            <?php $__currentLoopData = $linkTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($lt->id); ?>"><?php echo e($lt->linkType); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <select id="editLinksServiceTypesTpl">
            <option value="">Select Service</option>
            <option value="Internet">Internet</option>
            <option value="Metro VPN">Metro VPN</option>
            <option value="Intercity VPN">Intercity VPN</option>
            <option value="Carrier Services">Carrier Services</option>
            <option value="E-Vending">E-Vending</option>
            <option value="Dark-Fibre">Dark-Fibre</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" onclick="location.reload()">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/links/search_modal.blade.php ENDPATH**/ ?>