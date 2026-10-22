

<?php $__env->startSection('title'); ?>
Assign Faults
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>

<section class="content workflow-faults-page">
    <div class="card faults-panel">
        <div class="faults-panel-header">
            <div class="faults-panel-copy">
                <h3 class="faults-panel-title">Assign Faults</h3>
                <div class="faults-panel-subtitle">Review new faults, search the queue, and assign them from the same workspace layout as the main faults log.</div>
            </div>
            <div class="faults-panel-actions"></div>
        </div>
        <div class="faults-toolbar">
            <div class="faults-toolbar-grid">
                    <div class="faults-toolbar-field">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-list"></i></span>
                            <select id="assignedfaultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
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
                        <input type="text" id="assignedfaultsSearch" class="form-control" placeholder="Search faults to assign...">
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="waitingAssignSearchTrigger">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="waitingAssignReset">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </button>
            </div>
        </div>
        <div class="faults-table-shell">
            <div class="table-responsive impaza-table-wrap faults-table-wrap">
                <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" id="assigned-faults-list" data-page-size="20" data-page-size-control="#assignedfaultsPageSize" data-pager="#assignedfaultsPager" data-search="#assignedfaultsSearch">
                    <thead class="theah-light">
                        <tr>
                            <th>Ref. No.</th>
                            <th>Customer</th>
                            <th>Link</th>
                            <th>Switch</th>
                            <th>Port</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Fault Age</th>
                            <th>Created</th>
                            <th>Action(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
                            ?>
                            <tr>
                                <td data-label="Ref. No."><?php echo e($fault->fault_ref_number); ?></td>
                                <td data-label="Customer"><?php echo e($fault->customer); ?></td>
                                <td data-label="Link"><?php echo e($fault->link); ?></td>
                                <td data-label="Switch"><?php echo e($latestRemark->switch_name ?? 'N/A'); ?></td>
                                <td data-label="Port"><?php echo e($latestRemark->port ?? 'N/A'); ?></td>
                                <td data-label="Priority">
                                    <?php
                                        $priorityColors = [
                                            'low'    => '#28a745',
                                            'medium' => '#ffc107',
                                            'high'   => '#dc3545'
                                        ];
                                        $priority = strtolower(trim($fault->priorityLevel ?? ''));
                                    ?>
                                    <span class="badge rounded-pill" style="background-color: <?php echo e($priorityColors[$priority] ?? '#6c757d'); ?>; color: white; padding: 0.5rem 0.75rem; font-weight: 600;">
                                        <?php echo e(ucfirst($fault->priorityLevel)); ?>

                                    </span>
                                </td>
                                <td data-label="Status">
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
                                <td data-label="Created"><?php echo e(\Carbon\Carbon::parse($fault->created_at)->format('Y-m-d H:i')); ?></td>
                                <td data-label="Action(s)">
                                    <div class="faults-actions">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign-fault')): ?>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal-<?php echo e($fault->id); ?>"><i class="fas fa-user-tag"></i> Assign</button>
                                    <?php endif; ?>
                                    <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-<?php echo e($fault->id); ?>">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    </div>
                                </td>
                            </tr>
                                <?php echo $__env->make('faults.show', [
                                    'fault' => $fault,
                                    'remarks' => ($remarksByFault[$fault->id] ?? collect()),
                                    'ageText' => ($faultAges[$fault->id] ?? ''),
                                    'ageStart' => ($faultAgeStart[$fault->id] ?? null),
                                    'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                               
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($faults->isEmpty()): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">No faults to assign at the moment</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="faults-table-footer">
                <small class="text-muted">Showing open faults waiting for assignment</small>
                <div id="assignedfaultsPager"></div>
            </div>
        </div>
    </div>
    <?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('assign.assign_modal', [
            'fault' => $fault,
            'technicians' => ($techniciansByFault[$fault->id] ?? $technicians),
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
      document.getElementById('waitingAssignSearchTrigger')?.addEventListener('click', function () {
        const input = document.getElementById('assignedfaultsSearch');
        if (!input) return;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
      });
      document.getElementById('waitingAssignReset')?.addEventListener('click', function () {
        const input = document.getElementById('assignedfaultsSearch');
        const perPage = document.getElementById('assignedfaultsPageSize');
        if (input) {
          input.value = '';
          input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (perPage) {
          perPage.value = '20';
          perPage.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    </script>
<?php $__env->stopSection(); ?>
 




<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/assign/waiting.blade.php ENDPATH**/ ?>