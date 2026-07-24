<!-- Navbar -->
<?php
  $impazaPageName = trim($__env->yieldContent('pageName'));
  $impazaTitle = trim($__env->yieldContent('title'));
  $impazaCurrent = $impazaPageName ?: ($impazaTitle ?: 'Dashboard');
?>
<nav class="main-header navbar navbar-expand impaza-topbar">
  <div class="impaza-topbar-left">
    <a class="nav-link impaza-topbar-icon" id="impazaSidebarToggle" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar" aria-controls="impazaSidebar" aria-expanded="true"><i class="fas fa-bars"></i></a>
    <div class="d-none d-md-flex impaza-topbar-breadcrumb">
      <a href="<?php echo e(route('home')); ?>">Home</a>
      <span class="sep">/</span>
      <span class="current"><?php echo e($impazaCurrent); ?></span>
    </div>
  </div>

  <div class="d-none d-xl-flex impaza-topbar-center">
    <button type="button" class="impaza-topbar-search" data-impaza-global-search="1" aria-label="Open search">
      <span class="left">
        <i class="fas fa-search"></i>
        <span class="placeholder">Search faults, customers, sites, surveys...</span>
      </span>
      <span class="kbd">Ctrl + K</span>
    </button>
  </div>

  <ul class="navbar-nav ml-auto impaza-topbar-right">
    <li class="nav-item d-md-flex d-xl-none">
      <button type="button" class="btn impaza-topbar-icon" data-impaza-global-search="1" aria-label="Open search">
        <i class="fas fa-search"></i>
      </button>
    </li>
    <li class="nav-item">
      <button type="button" class="btn impaza-topbar-icon" id="impazaThemeToggle" aria-label="Toggle theme" aria-pressed="false">
        <i class="fas fa-moon"></i>
      </button>
    </li>

    <li class="nav-item dropdown">
      <a id="impazaNotifToggle" class="nav-link impaza-topbar-icon" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="position:relative;">
        <i class="fas fa-bell"></i>
        <span id="impazaNotifBadge" class="badge bg-danger" style="display:none; position:absolute; top:4px; right:4px; font-size:10px; padding:2px 5px; border-radius:10px;"></span>
      </a>
      <div class="dropdown-menu dropdown-menu-end impaza-topbar-dropdown-menu" style="min-width: 340px;">
        <div class="dropdown-header d-flex align-items-center justify-content-between">
          <span class="fw-semibold">Notifications</span>
          <button type="button" class="btn btn-link btn-sm p-0" id="impazaNotifMarkAll" style="text-decoration:none;">Mark all read</button>
        </div>
        <div class="dropdown-divider"></div>
        <div id="impazaNotifList" style="max-height: 340px; overflow:auto;"></div>
      </div>
    </li>

    <li class="nav-item dropdown">
      <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" style="gap:8px;">
        <span class="d-inline-flex align-items-center justify-content-center" style="width:30px; height:30px; border-radius:999px; background: rgba(99,102,241,.16); color: var(--impaza-text); font-weight:800; border:1px solid rgba(99,102,241,.22);">
          <?php echo e(strtoupper(mb_substr((string)Auth::user()->name, 0, 1))); ?>

        </span>
        <span class="align-middle d-none d-sm-inline-block" style="font-weight: 700; color: var(--impaza-text);"><?php echo e(Auth::user()->name); ?></span>
        <i class="fas fa-chevron-down d-none d-sm-inline-block" style="color: var(--impaza-muted); font-size: 12px;"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end impaza-topbar-dropdown-menu profile-dropdown">
        <a class="dropdown-item" href="<?php echo e(route('user.profile')); ?>"><i class="fas fa-user me-2"></i>Profile</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-power-off me-2"></i><?php echo e(__('Logout')); ?>

        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"><?php echo csrf_field(); ?></form>
      </div>
    </li>
  </ul>
</nav>

<div class="modal fade" id="impazaGlobalSearchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
    <div class="modal-content" style="border-radius: 18px; border: 1px solid var(--impaza-border); box-shadow: var(--impaza-shadow); background: var(--impaza-card);">
      <div class="modal-body" style="padding: 14px;">
        <div class="d-flex align-items-center" style="gap: 10px;">
          <div class="d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 12px; background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.18); color: var(--impaza-primary);">
            <i class="fas fa-search"></i>
          </div>
          <div style="flex: 1 1 auto;">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
              <form method="GET" action="<?php echo e(route('faults.index')); ?>" class="m-0">
                <input id="impazaGlobalSearchInput" type="text" name="q" class="form-control" placeholder="Search faults, customers, sites, surveys..." autocomplete="off">
              </form>
            <?php else: ?>
              <input id="impazaGlobalSearchInput" type="text" class="form-control" placeholder="Search faults, customers, sites, surveys..." autocomplete="off" disabled>
            <?php endif; ?>
          </div>
          <button type="button" class="btn btn-light border btn-icon" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>

        <div class="mt-3 d-flex flex-wrap" style="gap: 8px;">
          <span class="badge rounded-pill" style="background: rgba(148,163,184,.12); border: 1px solid rgba(148,163,184,.18); color: var(--impaza-muted); padding: 6px 10px;">CTRL + K</span>
          <span class="badge rounded-pill" style="background: rgba(148,163,184,.12); border: 1px solid rgba(148,163,184,.18); color: var(--impaza-muted); padding: 6px 10px;">ESC</span>
          <span class="badge rounded-pill" style="background: rgba(148,163,184,.12); border: 1px solid rgba(148,163,184,.18); color: var(--impaza-muted); padding: 6px 10px;">Enter</span>
        </div>

        <div class="mt-3">
          <div class="text-muted small" style="letter-spacing: .02em;">Quick links</div>
          <div class="mt-2 d-flex flex-wrap" style="gap: 8px;">
            <a class="btn btn-outline-secondary btn-sm rounded-pill" href="<?php echo e(route('home')); ?>"><i class="fas fa-chart-pie me-1"></i>Dashboard</a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
              <a class="btn btn-outline-secondary btn-sm rounded-pill" href="<?php echo e(route('faults.index')); ?>"><i class="fas fa-exclamation-triangle me-1"></i>Faults Log</a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('surveys-list')): ?>
              <a class="btn btn-outline-secondary btn-sm rounded-pill" href="<?php echo e(route('lte-site-surveys.index')); ?>"><i class="fas fa-clipboard-list me-1"></i>LTE Site Surveys</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.navbar -->
<?php /**PATH /var/www/html/resources/views/layouts/header.blade.php ENDPATH**/ ?>