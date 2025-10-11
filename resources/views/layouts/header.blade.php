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