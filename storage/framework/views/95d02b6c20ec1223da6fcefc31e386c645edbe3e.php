

<?php $__env->startSection('title'); ?>
My Faults
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('content'); ?>

<section class="content workflow-faults-page">

<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">My Faults</h3>
            <div class="faults-panel-subtitle">Track your active workload, review updates, and take action from one workspace.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>

    <div class="faults-toolbar">
        <div class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="myFaultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="myFaultsSearch" class="form-control" placeholder="Search faults, customers, links, managers...">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="myFaultsSearchTrigger">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="myFaultsReset">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" data-page-size="20" data-page-size-control="#myFaultsPageSize" data-pager="#myFaultsPager" data-search="#myFaultsSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref. No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Switch</th>
                        <th>Port</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
                    ?>
                    <tr >
                    <td data-label="No."><?php echo e(++$i); ?></td>
                        <td data-label="Ref. No."><?php echo e($fault->fault_ref_number ?? 'N/A'); ?></td>
                        <td data-label="Customer"><?php echo e($fault->customer); ?></td>
                        <td data-label="Link Name"><?php echo e($fault->link); ?></td>
                        <td data-label="Switch"><?php echo e($latestRemark->switch_name ?? 'N/A'); ?></td>
                        <td data-label="Port"><?php echo e($latestRemark->port ?? 'N/A'); ?></td>
                        <td class="text-nowrap" data-label="Status">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['label' => $fault->description,'color' => \App\Models\Status::STATUS_COLOR[$fault->description] ?? '#64748B','soft' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fault->description),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Models\Status::STATUS_COLOR[$fault->description] ?? '#64748B'),'soft' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </td>
                        <td data-label="Fault Age">
                            <span class="faults-age-pill age-ticker" data-started-at="<?php echo e($fault->stage_started_at ?? ''); ?>"></span>
                        </td>
                        <td data-label="Action(s)">
                        <div class="faults-actions">
                        <?php if($fault->description==='Fault is under Rectification'): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('noc-clear-faults-clear')): ?>
                                <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#nocClearModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-save me-1"></i>Clear
                                </button>
                                <button class="btn btn-sm btn-outline-success"  data-bs-toggle="modal" data-bs-target="#inProgressModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-save me-1"></i>In Progress
                                </button>
                            <?php endif; ?>
                            <!-- <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('chief-tech-clear-faults-clear')): ?>
                                <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#chiefTechClearModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-save me-1"></i>Clear
                                </button>
                            <?php endif; ?> -->

                            <!--<a href="<?php echo e(route('faults.show',$fault->id)); ?>" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>-->
                            

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rectify-fault')): ?>
                                <button class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#rectifyEditModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-save me-1"></i>Rectify
                                </button>
                            <?php endif; ?>  
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-permit')): ?>
                                <button class="btn btn-outline-warning"  data-bs-toggle="modal" data-bs-target="#requestPermitEditModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-pencil me-1"></i>Request Permit
                                </button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-material')): ?>
                                <button class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#requestMaterialCreateModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-pencil me-1"></i>Request Material
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#escalateModal-<?php echo e($fault->id); ?>">
                                <i class="fas fa-level-up-alt me-1"></i>Escalate
                            </button>
                            
                        <?php endif; ?>
                            <button class="btn  btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-<?php echo e($fault->id); ?>">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($faults->isEmpty()): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">No faults assigned</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing your current assigned fault list</small>
            <div id="myFaultsPager"></div>
        </div>
    </div>
</div>

<?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('my_faults.in_progress_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('rectification.noc_clear_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('clear_faults.chief_tech_clear_modal', [ 'fault' => $fault ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('rectification.edit_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()), 'confirmedRFO' => ($confirmedRFO ?? collect()) ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('permits.requested-permits.edit_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('stores.create_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()), 'materials' => ($materials ?? collect()), 'pendingRequests' => collect() ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('my_faults.escalate_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('faults.show', [
        'fault' => $fault,
        'remarks' => ($remarksByFault[$fault->id] ?? collect()),
        'ageText' => ($faultAges[$fault->id] ?? ''),
        'ageStart' => ($faultAgeStart[$fault->id] ?? null),
        'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo $__env->make('partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
      window.currentUserName = <?php echo json_encode(optional(auth()->user())->name, 15, 512) ?>;
      document.getElementById('myFaultsSearchTrigger')?.addEventListener('click', function () {
        const input = document.getElementById('myFaultsSearch');
        if (!input) return;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
      });
      document.getElementById('myFaultsReset')?.addEventListener('click', function () {
        const input = document.getElementById('myFaultsSearch');
        const perPage = document.getElementById('myFaultsPageSize');
        if (input) {
          input.value = '';
          input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (perPage) {
          perPage.value = '20';
          perPage.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });

      window.initMaterialRequestModal = function (faultId) {
        const prefix = 'mat-req-items-' + faultId;
        const container = document.querySelector('.' + prefix);
        const tpl = document.getElementById('matReqRowTpl-' + faultId);
        if (!container) return { faultId, skipped: true, reason: 'no-container' };
        let rowIndex = 1;

        function updateRemoveButtons() {
          const rows = container.querySelectorAll('.mat-row');
          rows.forEach(r => {
            const rb = r.querySelector('.remove-mat-row');
            if (rb) rb.disabled = rows.length <= 1;
          });
        }

        function bindRow(row, idx) {
          const sel = row.querySelector('.mat-select');
          const nameInput = row.querySelector('.mat-name-input');
          const unitInput = row.querySelector('.mat-unit-input');
          const qtyInput = row.querySelector('.mat-qty-input');
          const stockHint = row.querySelector('.mat-stock-hint');
          const removeBtn = row.querySelector('.remove-mat-row');
          if (sel) {
            sel.addEventListener('change', function () {
              const opt = sel.selectedOptions[0];
              if (!opt) return;
              if (opt.value && opt.value !== '__custom__') {
                nameInput.value = opt.dataset.name || '';
                unitInput.value = opt.dataset.unit || unitInput.value || 'pcs';
                const stock = parseFloat(opt.dataset.stock || 0);
                if (stockHint) stockHint.textContent = 'In stock: ' + stock + ' ' + (opt.dataset.unit || '');
                nameInput.readOnly = true;
                nameInput.classList.add('bg-light');
              } else if (opt.value === '__custom__') {
                nameInput.value = '';
                nameInput.readOnly = false;
                nameInput.classList.remove('bg-light');
                nameInput.focus();
                if (stockHint) stockHint.textContent = '';
              } else {
                nameInput.readOnly = false;
                nameInput.classList.remove('bg-light');
                if (stockHint) stockHint.textContent = '';
              }
            });
          }
          if (removeBtn) {
            removeBtn.addEventListener('click', function () {
              const rows = container.querySelectorAll('.mat-row');
              if (rows.length <= 1) return;
              row.remove();
              renumber();
              updateRemoveButtons();
            });
          }
        }

        function renumber() {
          const rows = container.querySelectorAll('.mat-row');
          rows.forEach((r, i) => {
            r.querySelectorAll('[name^="items["]').forEach(el => {
              el.name = el.name.replace(/items\[\d+\]/, 'items[' + i + ']');
            });
            r.querySelectorAll('[data-row]').forEach(el => {
              el.setAttribute('data-row', i);
            });
          });
          rowIndex = rows.length;
        }

        container.querySelectorAll('.mat-row').forEach((r, i) => bindRow(r, i));
        updateRemoveButtons();

        const addBtn = document.getElementById('addRowBtn-' + faultId) || document.querySelector('.add-mat-row-btn[data-fault="' + faultId + '"]');
        let tplHTML = null;
        if (tpl) {
          if (tpl.tagName === 'SCRIPT') tplHTML = tpl.textContent;
          else if (tpl.content && tpl.content.cloneNode) tplHTML = tpl.innerHTML;
        }
        if (addBtn) {
          addBtn.addEventListener('click', function () {
            let row = null;
            if (tplHTML) {
              const wrap = document.createElement('div');
              wrap.innerHTML = tplHTML;
              row = wrap.querySelector('.mat-row');
            }
            if (!row) {
              const refRow = container.querySelector('.mat-row');
              if (!refRow) return;
              row = refRow.cloneNode(true);
              const sel = row.querySelector('.mat-select');
              if (sel) sel.value = '';
              row.querySelectorAll('.mat-name-input, .mat-unit-input, .mat-qty-input, .mat-stock-hint').forEach(el => {
                if (el.classList && el.classList.contains('mat-stock-hint')) { el.textContent = ''; return; }
                if (el.tagName === 'INPUT' && el.type === 'number') el.value = '';
                else if (el.classList && el.classList.contains('mat-name-input')) { el.value = ''; el.readOnly = false; el.classList.remove('bg-light'); }
                else if (el.classList && el.classList.contains('mat-unit-input')) el.value = 'pcs';
              });
            }
            const idx = rowIndex++;
            row.querySelectorAll('[name^="items["]').forEach(el => {
              el.name = el.name.replace(/items\[ROW_IDX\]/g, 'items[' + idx + ']');
              el.name = el.name.replace(/items\[\d+\]/, 'items[' + idx + ']');
            });
            row.querySelectorAll('[data-row]').forEach(el => {
              el.setAttribute('data-row', idx);
            });
            container.appendChild(row);
            const appended = container.querySelectorAll('.mat-row')[container.querySelectorAll('.mat-row').length - 1];
            bindRow(appended, idx);
            renumber();
            updateRemoveButtons();
            const qty = appended.querySelector('.mat-qty-input');
            if (qty) setTimeout(() => qty.focus(), 30);
          });
        }

        const form = document.getElementById('matReqForm-' + faultId);
        if (form) {
          form.addEventListener('submit', function (ev) {
            let valid = true;
            const rows = container.querySelectorAll('.mat-row');
            let anyFilled = false;
            rows.forEach(r => {
              const name = r.querySelector('.mat-name-input');
              const qty = r.querySelector('.mat-qty-input');
              const sel = r.querySelector('.mat-select');
              if (sel && sel.value === '__custom__') sel.value = '';
              if (name && name.value.trim() && qty && parseFloat(qty.value) > 0) {
                anyFilled = true;
                name.required = true;
                qty.required = true;
              } else {
                name.required = false;
                qty.required = false;
                if (name && !name.value.trim() && qty && !parseFloat(qty.value)) {
                  const u = r.querySelector('.mat-unit-input');
                  const rem = r.querySelector('[name$="[remark]"]');
                  if (u) u.required = false;
                  if (rem) rem.required = false;
                }
              }
            });
            if (!anyFilled) {
              ev.preventDefault();
              alert('Please add at least one material with quantity.');
              valid = false;
            }
            return valid;
          });
        }
        return { faultId, ok: true, initialRows: container.querySelectorAll('.mat-row').length };
      };

      (function autoInitMatReqModals() {
        const tpls = document.querySelectorAll('script[type="text/template"][id^="matReqRowTpl-"]');
        const results = [];
        tpls.forEach(function (t) {
          const m = t.id.match(/matReqRowTpl-(\d+)/);
          if (m && m[1]) results.push(window.initMaterialRequestModal(parseInt(m[1], 10)));
        });
        window.__matReqInitResults = results;
      })();
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/my_faults/index.blade.php ENDPATH**/ ?>