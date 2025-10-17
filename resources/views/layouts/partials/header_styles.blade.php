<style>
/* Header (navbar) styles */
.main-header {
  position: sticky;
  top: 0;
  z-index: 1040;
  border-bottom: 1px solid #e6e9f0;
  box-shadow: 0 1px 2px rgba(16,24,40,.04);
}
.main-header .navbar-nav .nav-link { font-size: 12px; padding: 6px 10px; }
.main-header .navbar-nav .nav-link i { font-size: 0.9rem; }

@media (max-width: 991.98px) {
  .sidebar-mini .main-header { margin-left: 0; }
}

/* Independent content scroll area */
:root { --footer-height: 56px; }
.content-wrapper {
  display: flex;
  flex-direction: column;
  height: calc(100vh - var(--header-height) - var(--footer-height));
  min-height: 0 !important; /* override inline min-height to avoid page scroll */
  overflow: hidden;
  background: #f7f9fc;
}
.content-wrapper .content-header { flex: 0 0 auto; }
.content-wrapper .content { flex: 1 1 auto; overflow-y: auto; -webkit-overflow-scrolling: touch; overscroll-behavior: contain; }
@media (min-width: 992px) { .sidebar-mini .content-wrapper { margin-left: 240px; } }
@media (max-width: 991.98px) { .content-wrapper { margin-left: 0; } }

/* Footer restored to flow layout */
.main-footer { position: static !important; }
</style>
