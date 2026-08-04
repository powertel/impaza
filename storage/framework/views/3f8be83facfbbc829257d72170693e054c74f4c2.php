<?php $__env->startSection('title'); ?>
Customers
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('styles'); ?>
<style>
  .customers-page .customers-status-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .customers-page .customers-status-link {
    text-decoration: none;
    color: inherit;
    display: block;
    min-width: 0;
  }

  .customers-page .customers-status-card {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 104px;
    border-radius: 18px;
    border: 1px solid var(--impaza-border);
    background: var(--impaza-card);
    box-shadow: var(--impaza-shadow-sm);
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .customers-page .customers-status-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--status-color, var(--impaza-primary));
  }

  .customers-page .customersStatusStat:hover .customers-status-card,
  .customers-page .customersStatusStat:focus-visible .customers-status-card {
    transform: translateY(-2px);
    box-shadow: var(--impaza-shadow);
    border-color: color-mix(in srgb, var(--status-color, var(--impaza-primary)) 26%, var(--impaza-border));
  }

  .customers-page .customers-status-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .customers-page .customers-status-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .customers-page .customers-status-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--status-color, var(--impaza-primary));
    background: color-mix(in srgb, var(--status-color, var(--impaza-primary)) 12%, transparent);
    font-size: .95rem;
    flex: 0 0 auto;
  }

  .customers-page .customers-status-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .customers-page .customers-status-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .customers-page .customers-status-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .customers-page .customers-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(180px, 220px) minmax(280px, 1fr) auto auto;
  }

  .customers-page .customers-toolbar .toolbar-search-form,
  .customers-page .customers-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .customers-page .customers-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .customers-page .customer-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .customers-page .customer-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  .customers-page .customer-actions .dropdown-menu {
    min-width: 220px;
  }

  @media (max-width: 1199.98px) {
    .customers-page .customers-status-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .customers-page .customers-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .customers-page .customers-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .customers-page .customers-status-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .customers-page .customers-toolbar {
      grid-template-columns: 1fr;
    }

    .customers-page .customers-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<section class="content workflow-faults-page customers-page">
<div class="customers-status-grid">
  <a href="#" class="customers-status-link customersStatusStat" data-status-id="all">
    <div class="customers-status-card" style="--status-color:#64748B;">
      <div class="customers-status-body">
        <div class="customers-status-copy">
          <span class="customers-status-icon"><i class="fas fa-list"></i></span>
          <div>
            <div class="customers-status-label">All</div>
            <div class="customers-status-title">Customers</div>
          </div>
        </div>
        <div class="customers-status-value"><?php echo e($totalCustomers ?? 0); ?></div>
      </div>
    </div>
  </a>
  <?php
    $statusCards = [
      ['id'=>1,'label'=>'Pending','icon'=>'fa-hourglass-half','bar'=>'#EF4444'],
      ['id'=>2,'label'=>'Connected','icon'=>'fa-plug','bar'=>'#10B981'],
      ['id'=>3,'label'=>'Disconnected','icon'=>'fa-unlink','bar'=>'#F59E0B'],
      ['id'=>4,'label'=>'Decommissioned','icon'=>'fa-ban','bar'=>'#64748B'],
    ];
  ?>
  <?php $__currentLoopData = $statusCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="#" class="customers-status-link customersStatusStat" data-status-id="<?php echo e($st['id']); ?>">
      <div class="customers-status-card" style="--status-color: <?php echo e($st['bar']); ?>">
        <div class="customers-status-body">
          <div class="customers-status-copy">
            <span class="customers-status-icon"><i class="fas <?php echo e($st['icon']); ?>"></i></span>
            <div>
              <div class="customers-status-label"><?php echo e($st['label']); ?></div>
              <div class="customers-status-title">Status</div>
            </div>
          </div>
          <div class="customers-status-value"><?php echo e((int)($customerStatusCounts[$st['id']] ?? 0)); ?></div>
        </div>
      </div>
    </a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Manage Customers</h3>
            <div class="page-lead">Search, filter, review, and manage customer lifecycle actions from one responsive workspace with modern table filters.</div>
        </div>
        <div class="card-tools">
            <span class="record-chip"><i class="fas fa-layer-group"></i> <?php echo e($customers->total()); ?> total records</span>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-create')): ?>
            <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#customerCreateModal"><i class="fas fa-plus-circle me-1"></i> Create Customer </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="faults-toolbar">
        <div class="filter-toolbar customers-toolbar">
            <div class="faults-toolbar-field">
                <?php $perPage = request('per_page', 20); ?>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="customersPageSize" class="form-select">
                        <option value="10"  <?php echo e((int)$perPage===10 ? 'selected' : ''); ?>>Show 10</option>
                        <option value="20"  <?php echo e((int)$perPage===20 ? 'selected' : ''); ?>>Show 20</option>
                        <option value="50"  <?php echo e((int)$perPage===50 ? 'selected' : ''); ?>>Show 50</option>
                        <option value="100" <?php echo e((int)$perPage===100 ? 'selected' : ''); ?>>Show 100</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    <?php $statusSel = request('status'); ?>
                    <select id="customersStatusFilter" class="form-select">
                        <option value="all" <?php echo e(empty($statusSel) || $statusSel==='all' ? 'selected' : ''); ?>>All Statuses</option>
                        <option value="1" <?php echo e((string)$statusSel === '1' ? 'selected' : ''); ?>>Pending</option>
                        <option value="2" <?php echo e((string)$statusSel === '2' ? 'selected' : ''); ?>>Connected</option>
                        <option value="3" <?php echo e((string)$statusSel === '3' ? 'selected' : ''); ?>>Disconnected</option>
                        <option value="4" <?php echo e((string)$statusSel === '4' ? 'selected' : ''); ?>>Decommissioned</option>
                    </select>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('customers.index')); ?>" class="toolbar-search-form m-0">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" value="<?php echo e(request('q','')); ?>" class="form-control" placeholder="Search customers, account numbers, or managers">
                    <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                    <input type="hidden" name="status" value="<?php echo e($statusSel); ?>">
                </div>
            </form>
            <button type="button" class="btn btn-primary btn-sm" id="customersApplyFilters"><i class="fas fa-search me-1"></i>Search</button>
            <a href="<?php echo e(route('customers.index', ['per_page' => $perPage])); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Reset</a>
        </div>
    </div>
    <div class="card-body">
        <div class="faults-table-shell">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Account Manager</th>
                        <th>Account Number</th>
                        <th>Status</th>
                        <!-- <th>Address</th>
                        <th>Contact Number</th> -->
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td data-label="No."><span class="record-chip">#<?php echo e($customers->firstItem() + $loop->index); ?></span></td>
                        <td data-label="Customer">
                            <div class="record-meta">
                                <span class="customer-name"><?php echo e($customer->customer); ?></span>
                                <span class="customer-helper">Customer record</span>
                            </div>
                        </td>
                        <td data-label="Account Manager">
                            <div class="record-meta">
                                <span class="record-main"><?php echo e($customer->accountManager ?: 'Unassigned'); ?></span>
                                <span class="record-sub">Ownership</span>
                            </div>
                        </td>
                        <td data-label="Account Number">
                            <span class="record-chip"><i class="fas fa-hashtag"></i> <?php echo e($customer->account_number); ?></span>
                        </td>
                        <td data-label="Status">
                            <?php
                                $statusMap = [1=>'Pending',2=>'Connected',3=>'Disconnected',4=>'Decommissioned'];
                                $statusColors = ['Pending'=>'#EF4444','Connected'=>'#10B981','Disconnected'=>'#F59E0B','Decommissioned'=>'#64748B'];
                                $label = $statusMap[(int)($customer->customer_status ?? 2)] ?? 'Connected';
                                $color = $statusColors[$label] ?? '#6c757d';
                            ?>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['label' => $label,'color' => $color,'soft' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'soft' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </td>
                        <td class="text-end" data-label="Action(s)">
                            <div class="workflow-actions customer-actions">
                              <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerViewModal<?php echo e($customer->id); ?>" title="View">
                                <i class="fas fa-eye me-1"></i> View
                              </button>
                              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-edit')): ?>
                              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customerEditModal<?php echo e($customer->id); ?>" title="Edit">
                                <i class="fas fa-edit me-1"></i> Edit
                              </button>
                              <?php endif; ?>
                              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-delete')): ?>
                              <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#customerDeleteModal<?php echo e($customer->id); ?>" title="Delete">
                                <i class="fas fa-trash me-1"></i> Delete
                              </button>
                              <?php endif; ?>
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v me-1"></i> More
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('finance-link-update')): ?>
                                  <?php $custStatus = (int)($customer->customer_status ?? 2); ?>
                                  <?php if($custStatus === 2): ?>
                                  <li>
                                    <form action="<?php echo e(route('customers.disconnect',$customer->id)); ?>" method="POST" class="px-2 m-0">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('PUT'); ?>
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_disconnect" title="Disconnect">
                                        <i class="fas fa-unlink text-warning"></i>
                                        <span class="text-warning">Disconnect</span>
                                      </button>
                                    </form>
                                  </li>
                                  <?php endif; ?>
                                  <?php if($custStatus === 3): ?>
                                  <li>
                                    <form action="<?php echo e(route('customers.reconnect',$customer->id)); ?>" method="POST" class="px-2 m-0">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('PUT'); ?>
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect Disconnected Links">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect (Disconnected)</span>
                                      </button>
                                    </form>
                                  </li>
                                  <?php endif; ?>
                                  <?php if($custStatus === 4): ?>
                                  <li>
                                    <form action="<?php echo e(route('customers.reconnect',$customer->id)); ?>" method="POST" class="px-2 m-0">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('PUT'); ?>
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect Disconnected Links">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect (Disconnected)</span>
                                      </button>
                                    </form>
                                  </li>
                                  <li>
                                    <form action="<?php echo e(route('customers.reconnect_decommissioned',$customer->id)); ?>" method="POST" class="px-2 m-0">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('PUT'); ?>
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect Decommissioned Links">
                                        <i class="fas fa-plug text-primary"></i>
                                        <span class="text-primary">Reconnect (Decommissioned)</span>
                                      </button>
                                    </form>
                                  </li>
                                  <?php endif; ?>
                                  <li>
                                    <form action="<?php echo e(route('customers.decommission',$customer->id)); ?>" method="POST" class="px-2 m-0">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('PUT'); ?>
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_decommission" title="Decommission">
                                        <i class="fas fa-ban text-danger"></i>
                                        <span class="text-danger">Decommission</span>
                                      </button>
                                    </form>
                                  </li>
                                <?php endif; ?>
                              </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody> 
            </table>
            <div class="workflow-pagination">
              <small class="text-muted">
                Showing <?php echo e($customers->firstItem()); ?> to <?php echo e($customers->lastItem()); ?> of <?php echo e($customers->total()); ?> results
              </small>
              <?php echo e($customers->appends(request()->except('page'))->links('pagination::bootstrap-5')); ?>

            </div>

            <?php echo $__env->make('customers.create_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('customers.edit_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('customers.view_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('customers.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>
 
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
  document.getElementById('customersApplyFilters')?.addEventListener('click', function(){
    const form = document.querySelector('.customers-toolbar .toolbar-search-form');
    if (form) form.submit();
  });
  document.getElementById('customersPageSize')?.addEventListener('change', function(){
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', this.value);
    params.delete('page');
    window.location.search = params.toString();
  });
  document.getElementById('customersStatusFilter')?.addEventListener('change', function(){
    const params = new URLSearchParams(window.location.search);
    params.set('status', this.value);
    params.delete('page');
    window.location.search = params.toString();
  });
  document.querySelectorAll('.customersStatusStat').forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault();
      const statusId = this.getAttribute('data-status-id');
      const params = new URLSearchParams(window.location.search);
      params.set('status', statusId);
      params.delete('page');
      window.location.search = params.toString();
    });
  });
</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/customers/index.blade.php ENDPATH**/ ?>