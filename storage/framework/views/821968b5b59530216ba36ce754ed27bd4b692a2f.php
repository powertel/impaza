<?php $__env->startSection('title'); ?>
links
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('styles'); ?>
<style>
  .links-page .links-status-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .links-page .links-status-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }

  .links-page .links-status-card {
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

  .links-page .links-status-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--status-color, var(--impaza-primary));
  }

  .links-page .linksStatusStat:hover .links-status-card,
  .links-page .linksStatusStat:focus-visible .links-status-card {
    transform: translateY(-2px);
    box-shadow: var(--impaza-shadow);
    border-color: color-mix(in srgb, var(--status-color, var(--impaza-primary)) 26%, var(--impaza-border));
  }

  .links-page .links-status-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .links-page .links-status-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .links-page .links-status-icon {
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

  .links-page .links-status-label {
    font-size: .72rem;
    color: var(--impaza-muted);
  }

  .links-page .links-status-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .links-page .links-status-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .links-page .links-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(180px, 220px) minmax(280px, 1fr) auto auto;
  }

  .links-page .links-toolbar .toolbar-search-form,
  .links-page .links-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .links-page .links-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .links-page .link-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .links-page .link-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  .links-page .configuration-banner {
    border-radius: 16px;
    border: 1px solid color-mix(in srgb, #F59E0B 32%, var(--impaza-border));
    background: color-mix(in srgb, #F59E0B 10%, var(--impaza-card));
    color: var(--impaza-text);
  }

  @media (max-width: 1199.98px) {
    .links-page .links-status-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .links-page .links-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .links-page .links-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .links-page .links-status-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .links-page .links-toolbar {
      grid-template-columns: 1fr;
    }

    .links-page .links-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="content workflow-faults-page links-page">
<div class="links-status-grid">
  <a href="#" class="links-status-link linksStatusStat" data-status-id="">
    <div class="links-status-card" style="--status-color:#64748B;">
      <div class="links-status-body">
        <div class="links-status-copy">
          <span class="links-status-icon"><i class="fas fa-list"></i></span>
          <div>
            <div class="links-status-label">All</div>
            <div class="links-status-title">Links</div>
          </div>
        </div>
        <div class="links-status-value"><?php echo e($totalLinks ?? 0); ?></div>
      </div>
    </div>
  </a>
  <?php $__currentLoopData = $linkStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      $icon = $st->link_status === 'Pending' ? 'fa-hourglass-half' : ($st->link_status === 'Connected' ? 'fa-plug' : ($st->link_status === 'Disconnected' ? 'fa-unlink' : 'fa-ban'));
      $bar = $st->link_status === 'Pending' ? '#EF4444' : ($st->link_status === 'Connected' ? '#10B981' : ($st->link_status === 'Disconnected' ? '#F59E0B' : '#64748B'));
    ?>
    <a href="#" class="links-status-link linksStatusStat" data-status-id="<?php echo e($st->id); ?>">
      <div class="links-status-card" style="--status-color: <?php echo e($bar); ?>">
        <div class="links-status-body">
          <div class="links-status-copy">
            <span class="links-status-icon"><i class="fas <?php echo e($icon); ?>"></i></span>
            <div>
              <div class="links-status-label"><?php echo e($st->link_status); ?></div>
              <div class="links-status-title">Status</div>
            </div>
          </div>
          <div class="links-status-value"><?php echo e((int)($statusCounts[$st->id] ?? 0)); ?></div>
        </div>
      </div>
    </a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <div>
          <h3 class="card-title mb-0">Manage Links</h3>
          <div class="page-lead">Search, filter, configure, edit, and review links from one responsive workspace with modern filters and dark-safe tables.</div>
        </div>
          <div class="card-tools">
            <a href="<?php echo e($needsConfiguration ? route('links.index', request()->except(['needs_configuration', 'page'])) : route('links.index', array_merge(request()->except('page'), ['needs_configuration' => 1]))); ?>"
               class="btn btn-warning btn-sm">
                <i class="fas fa-tools me-1"></i>
                <?php echo e($needsConfiguration ? 'View All Links' : 'Links To Configure'); ?>

                <?php if(!$needsConfiguration): ?>
                  <span class="badge bg-light text-dark ms-1"><?php echo e($linksNeedingConfigurationCount ?? 0); ?></span>
                <?php endif; ?>
            </a>
          
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-create')): ?>
                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" 
                        data-bs-target="#createLinkModal">
                    <i class="fas fa-plus-circle me-1"></i> Create Link
                </button>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-edit')): ?>
            <button type="button" class="btn btn-secondary btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editExistingLinksModal">
                <i class="fas fa-search me-1"></i> Edit Existing Links
            </button>
            <?php endif; ?>
          </div>
    </div>
    <div class="card-body">
        <?php if($needsConfiguration): ?>
          <div class="configuration-banner d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 mb-3">
            <div>
              <strong>Links Pending Configuration</strong>
              <span class="ms-2 text-muted">New customers: <?php echo e($newCustomerLinksToConfigure); ?> | Existing customers: <?php echo e($existingCustomerLinksToConfigure); ?></span>
            </div>
            <a href="<?php echo e(route('links.index', request()->except(['needs_configuration', 'page']))); ?>" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-list me-1"></i> Back To All Links
            </a>
          </div>
        <?php endif; ?>
        <div class="faults-toolbar">
            <div class="filter-toolbar links-toolbar">
                <div class="faults-toolbar-field">
                    <?php $perPage = request('per_page', 20); ?>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text"><i class="fas fa-list"></i></span>
                      <select id="linksPageSize" class="form-select">
                          <option value="10"  <?php echo e((int)$perPage===10 ? 'selected' : ''); ?>>Show 10</option>
                          <option value="20"  <?php echo e((int)$perPage===20 ? 'selected' : ''); ?>>Show 20</option>
                          <option value="50"  <?php echo e((int)$perPage===50 ? 'selected' : ''); ?>>Show 50</option>
                          <option value="100" <?php echo e((int)$perPage===100 ? 'selected' : ''); ?>>Show 100</option>
                      </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    <?php $statusSel = request('status'); ?>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text"><i class="fas fa-filter"></i></span>
                      <select id="linksStatusFilter" class="form-select">
                          <option value="" <?php echo e(empty($statusSel) ? 'selected' : ''); ?>>All Statuses</option>
                          <?php $__currentLoopData = $linkStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->id); ?>" <?php echo e((string)$statusSel === (string)$st->id ? 'selected' : ''); ?>><?php echo e($st->link_status); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>
                </div>
                <form id="linksSearchForm" method="GET" action="<?php echo e(route('links.index')); ?>" class="toolbar-search-form m-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="<?php echo e(request('q','')); ?>" class="form-control" placeholder="Search links, customers, cities, or locations">
                        <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                        <input type="hidden" name="status" value="<?php echo e($statusSel); ?>">
                        <input type="hidden" name="needs_configuration" value="<?php echo e($needsConfiguration ? 1 : ''); ?>">
                    </div>
                </form>
                <button type="submit" form="linksSearchForm" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Search</button>
                <a href="<?php echo e(route('links.index', array_filter(['per_page' => $perPage, 'needs_configuration' => $needsConfiguration ? 1 : null], fn ($value) => $value !== null && $value !== ''))); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
        <div class="faults-table-shell">
        <div class="table-responsive">
            <table id="linksTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>City/Town</th>
                        <th>Location</th>
                        <th>Pop</th>
                        <th>Link</th>
                        <th>Status</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $needsSetup = empty($link->city_id) || empty($link->suburb_id) || empty($link->pop_id);
                    ?>
                    <tr class="<?php echo e($needsSetup ? 'table-warning' : ''); ?>">
                        <td data-label="No."><span class="record-chip">#<?php echo e($links->firstItem() + $loop->index); ?></span></td>
                        <td data-label="Customer">
                          <div class="record-meta">
                          <div class="record-main"><?php echo e($link->customer); ?></div>
                          <?php if($needsSetup): ?>
                            <small class="record-chip mt-1"><i class="fas fa-tools"></i> <?php echo e($link->configuration_owner_type); ?></small>
                          <?php endif; ?>
                          </div>
                        </td>
                        <td data-label="City/Town"><?php echo e($link->city ?? 'Needs configuration'); ?></td>
                        <td data-label="Location"><?php echo e($link->suburb ?? 'Needs configuration'); ?></td>
                        <td data-label="Pop"><?php echo e($link->pop ?? 'Needs configuration'); ?></td>
                        <td data-label="Link">
                          <div class="record-meta">
                            <span class="link-name"><?php echo e($link->link); ?></span>
                            <span class="link-helper">Service link</span>
                          </div>
                        </td>
                        <td data-label="Status">
                          <?php $colors = \App\Models\LinkStatus::STATUS_COLOR; $color = $colors[$link->link_status ?? ''] ?? '#64748B'; ?>
                          <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['label' => $link->link_status ?? '—','color' => $color,'soft' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link->link_status ?? '—'),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'soft' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </td>

                        <td class="text-end" data-label="Action(s)">
                            <div class="workflow-actions">
                              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-list')): ?>
                              <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#linkViewModal<?php echo e($link->id); ?>" title="View">
                                <i class="fas fa-eye me-1"></i> View
                              </button>
                              <?php endif; ?>
                              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-edit')): ?>
                              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#linkEditModal<?php echo e($link->id); ?>" title="Edit">
                                <i class="fas fa-edit me-1"></i> Edit
                              </button>
                              <?php endif; ?>
                              <div class="dropdown">
                                <button
                                  type="button"
                                  class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                  data-bs-toggle="dropdown"
                                  data-bs-boundary="viewport"
                                  aria-expanded="false"
                                >
                                  <i class="fas fa-ellipsis-v me-1"></i> More
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-delete')): ?>
                                    <li>
                                      <form action="<?php echo e(route('links.destroy',$link->id)); ?>" method="POST" class="px-2 m-0">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 show_confirm" title="Delete">
                                          <i class="fas fa-trash text-danger"></i>
                                          <span class="text-danger">Delete</span>
                                        </button>
                                      </form>
                                    </li>
                                  <?php endif; ?>
                                  <li><hr class="dropdown-divider"></li>
                                  <?php if(($link->link_status ?? '') === 'Connected'): ?>
                                    <li>
                                      <form action="<?php echo e(route('disconnect',$link->id)); ?>" method="POST" class="px-2 m-0">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_disconnect" title="Disconnect">
                                          <i class="fas fa-unlink text-warning"></i>
                                          <span class="text-warning">Disconnect</span>
                                        </button>
                                      </form>
                                    </li>
                                  <?php endif; ?>
                                  <?php if(($link->link_status ?? '') === 'Disconnected' || ($link->link_status ?? '') === 'Decommissioned'): ?>
                                    <li>
                                      <form action="<?php echo e(route('reconnect',$link->id)); ?>" method="POST" class="px-2 m-0">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect">
                                          <i class="fas fa-plug text-success"></i>
                                          <span class="text-success">Reconnect</span>
                                        </button>
                                      </form>
                                    </li>
                                  <?php endif; ?>
                                    <li>
                                      <form action="<?php echo e(route('decommission',$link->id)); ?>" method="POST" class="px-2 m-0">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_decommission" title="Decommission">
                                          <i class="fas fa-ban text-danger"></i>
                                          <span class="text-danger">Decommission</span>
                                        </button>
                                      </form>
                                    </li>
                                </ul>
                              </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>  
                    <?php if($links->isEmpty()): ?>
                        <tr>
                            <td colspan="8" class="text-center empty-state">No Links to display</td>
                        </tr>
                    <?php endif; ?>
            </table>
            <div class="workflow-pagination">
              <small class="table-note">
                Showing <?php echo e($links->firstItem()); ?> to <?php echo e($links->lastItem()); ?> of <?php echo e($links->total()); ?> results
              </small>
              <?php echo e($links->appends(request()->except('page'))->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
        </div>
    </div>
    <!-- /.card-body -->
     <?php echo $__env->make('links.create_modal', [
        'customers' => $customers,
        'cities' => $cities,
        'suburbs' => $suburbs,
        'pops' => $pops,
        'linkTypes' => $linkTypes
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('links.search_modal', [
            'customers' => $customers,
            'cities' => $cities,
            'linkTypes' => $linkTypes
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lnk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('links.edit_modal', [
            'link' => $lnk,
            'customers' => $customers,
            'cities' => $cities,
            'suburbs' => $suburbs,
            'pops' => $pops,
            'linkTypes' => $linkTypes
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('links.view_modal', [ 'link' => $lnk ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
 
</section>
<?php $__env->startSection('scripts'); ?>
  <?php echo $__env->make('partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('links.partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <script>
    document.getElementById('linksPageSize')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      params.set('per_page', this.value);
      params.delete('page');
      window.location.search = params.toString();
    });
    document.getElementById('linksStatusFilter')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      const val = this.value;
      if (!val) params.delete('status'); else params.set('status', val);
      params.delete('page');
      window.location.search = params.toString();
    });
    document.querySelectorAll('.linksStatusStat').forEach(function(el){
      el.addEventListener('click', function(e){
        e.preventDefault();
        const id = this.getAttribute('data-status-id');
        const params = new URLSearchParams(window.location.search);
        if (!id) params.delete('status'); else params.set('status', id);
        params.delete('page');
        window.location.search = params.toString();
      });
    });
    (function(){
      var success = <?php echo json_encode(session('success'), 15, 512) ?>;
      var error = <?php echo json_encode(session('error'), 15, 512) ?>;
      var warning = <?php echo json_encode(session('warning'), 15, 512) ?>;
      var info = <?php echo json_encode(session('info'), 15, 512) ?>;
      function show(type, text){
        if (!text) return;
        if (window.toast) {
          window.toast.fire({ icon: type, title: String(text) });
        } else {
          alert(String(text));
        }
      }
      show('success', success);
      show('error', error);
      show('warning', warning);
      show('info', info);
    })();

    // Hide inline alert banners on Links page (use JS toast only)
    document.addEventListener('DOMContentLoaded', function(){
      try {
        document.querySelectorAll('.content .alert').forEach(function(el){ el.remove(); });
      } catch (e) {}
    });
  </script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>








                          

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/links/index.blade.php ENDPATH**/ ?>