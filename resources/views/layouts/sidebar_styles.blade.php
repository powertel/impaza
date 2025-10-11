<style>
/* Sidebar styles */
:root { --header-height: 56px; }

.main-sidebar {
  background: #fff;
  border-right: 2px solid #e6e9f0;
  box-shadow: 2px 0 6px rgba(16,24,40,.06);
  position: fixed;
  top: var(--header-height);
  left: 0;
  height: calc(100vh - var(--header-height));
  overflow-y: auto !important;
  overflow-x: hidden !important;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
  z-index: 1030;
}
.main-sidebar .sidebar {
  height: auto;
  padding-top: 0;
  overflow: visible;
}

/* Sidebar nav */
.nav-sidebar .nav-header {
  font-size: clamp(10px, 1.1vw, 11px);
  color: rgb(6, 6, 6);
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 10px 16px 6px;
  margin-top: 6px;
}
.nav-sidebar .nav-header:after {
  content: '';
  display: block;
  height: 0;
  border-top: 1px solid #f0f0f0;
  margin: 6px 0 8px;
}

/* Ensure link text is dark */
.nav-sidebar .nav-link,
.nav-sidebar .nav-link p { color: #111827 !important; }

.nav-sidebar .nav-link {
  font-size: clamp(11px, 1.3vw, 13px);
  border-radius: 8px;
  padding: 7px 11px;
  margin: 2px 8px;
  transition: background-color .2s ease, color .2s ease, padding .2s ease;
}
.nav-sidebar .nav-link i.nav-icon {
  font-size: 0.9rem;
  color: #111827;
  margin-right: 8px;
}
.nav-sidebar .nav-link:hover {
  background-color: #f8f9fb;
  color: #111827;
}
.nav-sidebar .nav-link.active {
  background-color: #eef4ff;
  color: #1f5cff;
}
.nav-sidebar .nav-link.active i.nav-icon { color: #1f5cff; }

/* Tight spacing overrides for brand and nav */
.user-panel { margin: 0 !important; padding: 8px 14px !important; border-bottom: 1px solid #f0f0f0; }
.user-panel .info h3 { margin: 0 !important; font-size: 14px; line-height: 1.2; }
/* Remove nav extra margin; rely on theme defaults */
/* .sidebar .nav.mt-2 { margin-top: 4px !important; } */


/* Brand area */
.brand-link { padding: 16px 14px; }
.brand-link .brand-image { opacity: 0.9; }

/* General sidebar font sizing */
.main-sidebar, .sidebar { font-size: clamp(11px, 1.3vw, 13px); }

/* Layout adjustments at breakpoints */
@media (min-width: 992px) {
  .sidebar-mini .main-sidebar { width: 240px; }
  .sidebar-mini .content-wrapper, .sidebar-mini .main-footer, .sidebar-mini .main-header { margin-left: 240px; }
}
@media (min-width: 1280px) {
  .sidebar-mini .main-sidebar { width: 240px; }
  .sidebar-mini .content-wrapper, .sidebar-mini .main-footer, .sidebar-mini .main-header { margin-left: 240px; }
}
@media (max-width: 991.98px) {
  /* On small screens let the sidebar use full viewport when opened */
  .main-sidebar { top: 0; height: 100vh; overflow-y: auto !important; }
  .main-sidebar .sidebar { height: auto; padding-top: 0; overflow: initial; }
}
</style>