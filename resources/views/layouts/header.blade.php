<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background:#fff;border-bottom:1px solid #eee;box-shadow:0 1px 2px rgba(16,24,40,.04);">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index3.html" class="nav-link">Home</a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a id="impazaNotifToggle" class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="position:relative;">
          <i class="fas fa-bell" style="color: rgb(35, 157, 233); font-size: 16px;"></i>
          <span id="impazaNotifBadge" class="badge bg-danger" style="display:none; position:absolute; top:6px; right:6px; font-size:10px; padding:2px 5px; border-radius:10px;"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu" style="min-width: 320px;">
          <div class="dropdown-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold">Notifications</span>
            <button type="button" class="btn btn-link btn-sm p-0" id="impazaNotifMarkAll" style="text-decoration:none;">Mark all read</button>
          </div>
          <div class="dropdown-divider"></div>
          <div id="impazaNotifList" style="max-height: 320px; overflow:auto;"></div>
        </div>
      </li>
      <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fa fa-user" style="color: rgb(35, 157, 233);"></i> &nbsp;
                <span class="align-middle d-none d-sm-inline-block" style="font-weight: 700; color: rgb(35, 157, 233);"> {{ Auth::user()->name }}</span> <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
            </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">

              <!-- item-->
              <a  class="dropdown-item notify-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                      document.getElementById('logout-form').submit();">
                          {{ __('Logout') }}
                  <i class="mdi mdi-logout me-1"></i>
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                          @csrf
              </form>
          </div>
      </li>
    </ul>
</nav>
<!-- /.navbar -->
