

<?php $__env->startSection('title'); ?>
Department Faults
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('content'); ?>
<section class="content workflow-faults-page">

<div class="faults-kpi-grid mb-4">
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#94A3B8;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-clipboard-list"></i></span>
          <div>
            <div class="faults-kpi-label">All Open Faults</div>
            <div class="faults-kpi-title">View all open faults</div>
          </div>
        </div>
        <div class="faults-kpi-value"><?php echo e((int) ($ageStats['open_total'] ?? 0)); ?></div>
      </div>
    </div>
  </a>
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="today" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#2563EB;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-calendar-day"></i></span>
          <div>
            <div class="faults-kpi-label">Logged Today</div>
            <div class="faults-kpi-title">View today's faults</div>
          </div>
        </div>
        <div class="faults-kpi-value"><?php echo e((int) ($ageStats['open_today'] ?? 0)); ?></div>
      </div>
    </div>
  </a>
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="lt72" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#10B981;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-hourglass-half"></i></span>
          <div>
            <div class="faults-kpi-label">Within 72 Hours</div>
            <div class="faults-kpi-title">View within 72 hours</div>
          </div>
        </div>
        <div class="faults-kpi-value"><?php echo e((int) ($ageStats['open_lt72'] ?? 0)); ?></div>
      </div>
    </div>
  </a>
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="gt72" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#F59E0B;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-hourglass-end"></i></span>
          <div>
            <div class="faults-kpi-label">Over 72 Hours</div>
            <div class="faults-kpi-title">View overdue faults</div>
          </div>
        </div>
        <div class="faults-kpi-value"><?php echo e((int) ($ageStats['open_gt72'] ?? 0)); ?></div>
      </div>
    </div>
  </a>
</div>

<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Department Faults</h3>
            <div class="faults-panel-subtitle">Search, filter, and review faults assigned to your department from one responsive workspace.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>
    <div class="faults-toolbar">
        <form method="GET" action="<?php echo e(route('department_faults.index')); ?>" class="m-0">
            <div class="faults-toolbar-grid">
                <div class="faults-toolbar-field">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <select id="departmentFaultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
                            <option value="10"  <?php echo e((int)$perPage===10 ? 'selected' : ''); ?>>10</option>
                            <option value="20"  <?php echo e((int)$perPage===20 ? 'selected' : ''); ?>>20</option>
                            <option value="50"  <?php echo e((int)$perPage===50 ? 'selected' : ''); ?>>50</option>
                            <option value="100" <?php echo e((int)$perPage===100 ? 'selected' : ''); ?>>100</option>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    <?php $statusFilter = request('status', 'all'); ?>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-filter"></i></span>
                        <select name="status" id="deptFaultsStatusFilter" class="form-select form-select-sm" aria-label="Status filter">
                            <option value="all"   <?php echo e($statusFilter === 'all' ? 'selected' : ''); ?>>All Statuses</option>
                            <option value="lt4"   <?php echo e($statusFilter === 'lt4' ? 'selected' : ''); ?>>Open Faults</option>
                            <?php $__currentLoopData = ($openStatuses ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($st->id); ?>" <?php echo e($statusFilter == (string)$st->id ? 'selected' : ''); ?>><?php echo e($st->description); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    <?php $ageFilter = request('age', 'all'); ?>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                        <select name="age" id="deptFaultsAgeFilter" class="form-select form-select-sm" aria-label="Age filter">
                            <option value="all"    <?php echo e($ageFilter === 'all' ? 'selected' : ''); ?>>All Ages</option>
                            <option value="today"  <?php echo e($ageFilter === 'today' ? 'selected' : ''); ?>>Today</option>
                            <option value="lt72"   <?php echo e($ageFilter === 'lt72' ? 'selected' : ''); ?>>Within 72 Hours</option>
                            <option value="gt72"   <?php echo e($ageFilter === 'gt72' ? 'selected' : ''); ?>>Over 72 Hours</option>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    <?php $regionFilter = request('region', 'all'); ?>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                        <select name="region" id="deptFaultsRegionFilter" class="form-select form-select-sm" aria-label="Region filter">
                            <option value="all" <?php echo e($regionFilter === 'all' ? 'selected' : ''); ?>>All Regions</option>
                            <?php $__currentLoopData = ($regions ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($r); ?>" <?php echo e($regionFilter === (string) $r ? 'selected' : ''); ?>><?php echo e($r); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field faults-toolbar-search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="<?php echo e(request('q','')); ?>" class="form-control" placeholder="Search faults, customers, links, managers...">
                        <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit"><i class="fas fa-search me-1"></i> Search</button>
                <a href="<?php echo e(route('department_faults.index', ['per_page' => $perPage])); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset"><i class="fas fa-rotate-left me-1"></i> Reset</a>
            </div>
        </form>
    </div>
    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Switch</th>
                        <th>Port</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
                    ?>
                    <tr>
                        <td data-label="No."><?php echo e($faults->firstItem() + $loop->index); ?></td>
                        <td data-label="Ref No."><?php echo e($fault->fault_ref_number); ?></td>
                        <td data-label="Customer"><?php echo e($fault->customer); ?></td>
                        <td data-label="Link Name"><?php echo e($fault->link); ?></td>
                        <td data-label="Switch"><?php echo e($latestRemark->switch_name ?? 'N/A'); ?></td>
                        <td data-label="Port"><?php echo e($latestRemark->port ?? 'N/A'); ?></td>
                        <td class="<?php echo e($fault->name ? 'fw-bold' : 'text-muted'); ?>" data-label="Assigned To"><?php echo e($fault->name ?: 'Not yet assigned'); ?></td>
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
                        <td data-label="Action(s)">
                            <div class="faults-actions">
                                <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-<?php echo e($fault->id); ?>">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($faults->count() === 0): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No Department faults</td>
                        </tr>
                    <?php endif; ?>
                </tbody> 
            </table>
            <div class="d-flex justify-content-between align-items-center mt-2 faults-table-footer">
                <div class="text-muted">
                    Showing <?php echo e($faults->firstItem() ?? 0); ?> to <?php echo e($faults->lastItem() ?? 0); ?> of <?php echo e($faults->total()); ?> results
                    <?php if(request('q')): ?>
                        for "<?php echo e(request('q')); ?>"
                    <?php endif; ?>
                </div>
                <div>
                    <?php echo e($faults->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
    </div>
</div>
<?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('faults.show', [
        'fault' => $fault,
        'remarks' => ($remarksByFault[$fault->id] ?? collect()),
        'ageText' => ($faultAges[$fault->id] ?? ''),
        'ageStart' => ($faultAgeStart[$fault->id] ?? null),
        'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php if(!empty($fault->referral_id)): ?>
    <?php echo $__env->make('department_faults.complete_referral_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 

</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo $__env->make('partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
      window.currentUserName = <?php echo json_encode(optional(auth()->user())->name, 15, 512) ?>;
      (function(){
        var perSelect = document.getElementById('departmentFaultsPageSize');
        if (perSelect) {
          perSelect.addEventListener('change', function(){
            var params = new URLSearchParams(window.location.search);
            params.set('per_page', String(perSelect.value));
            params.delete('page');
            window.location.search = params.toString();
          });
        }
      })();
      document.querySelectorAll('.deptFaultsAgeStat').forEach(function(el){
        el.addEventListener('click', function(e){
          e.preventDefault();
          var params = new URLSearchParams(window.location.search);
          params.set('status', this.getAttribute('data-status'));
          var age = this.getAttribute('data-age');
          if (!age) params.delete('age'); else params.set('age', age);
          params.delete('page');
          window.location.search = params.toString();
        });
      });
      document.getElementById('deptFaultsStatusFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
      document.getElementById('deptFaultsAgeFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
      document.getElementById('deptFaultsRegionFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/department_faults/index.blade.php ENDPATH**/ ?>