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
.content-wrapper {
  display: flex;
  flex-direction: column;
  height: calc(100vh - var(--header-height));
  min-height: 0 !important;
  overflow-y: auto; /* make whole area scrollable */
  overflow-x: hidden;
  overscroll-behavior: contain;
  background: #f7f9fc;
}
.content-wrapper .content-header { flex: 0 0 auto; }
.content-wrapper .content { flex: 1 1 auto; overflow: visible; }
/* Create breathing room above the footer so lists/paginators don't crowd it */
.content-wrapper .content { padding-bottom: 12px; }
@media (min-width: 992px) { .sidebar-mini .content-wrapper { margin-left: 240px; } }
@media (max-width: 991.98px) { .content-wrapper { margin-left: 0; } }

/* Footer flows inside content-wrapper; no fixed positioning */
.main-footer { position: static !important; }

/* Faint scrollbars matching background for content area */
.content-wrapper { scrollbar-width: thin; scrollbar-color: #e9edf5 #f7f9fc; }
.content-wrapper::-webkit-scrollbar { width: 8px; height: 8px; }
.content-wrapper::-webkit-scrollbar-thumb { background: #e9edf5; border-radius: 8px; }
.content-wrapper::-webkit-scrollbar-track { background: #f7f9fc; }
/* Minimal footer nav layout to match example */
.main-footer { background: transparent; border-top: none; padding: 12px 0; }
.main-footer .footer-inner { width: 100%; margin: 0; padding: 0 16px; display: flex; flex-direction: row; justify-content: flex-end; align-items: center; }
.main-footer .footer-nav { list-style: none; display: flex; gap: 18px; padding: 0; margin: 0 0 8px 0; font-size: 12px; }
.main-footer .footer-nav a { color: #667085; text-decoration: none; padding: 4px 6px; border-radius: 6px; }
.main-footer .footer-nav a:hover { color: #111827; background: #f8fafc; }
.main-footer .footer-divider { width: 100%; max-width: 880px; border: 0; border-top: 1px solid #e6e9f0; margin: 6px auto 8px; }
.main-footer .footer-copy { font-size: 12px; color: #667085; }

/* Responsive footer tweaks */
@media (max-width: 575.98px) {
  .main-footer { padding: 10px 0; }
  .main-footer .footer-inner { padding: 0 12px; }
  .main-footer .footer-copy { font-size: 11px; }
}
@media (min-width: 576px) and (max-width: 991.98px) {
  .main-footer .footer-copy { font-size: 12px; }
}
@media (min-width: 992px) {
  .main-footer .footer-inner { max-width: 1200px; }
}
/* Polished footer styles */
.main-footer { background: transparent; border-top: none; box-shadow: none; padding: 10px 16px; }
.main-footer .footer-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: 8px; }
.main-footer .footer-content { display: inline-flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #e6e9f0; border-radius: 12px; padding: 8px 12px; box-shadow: 0 1px 2px rgba(16,24,40,.06); }
.main-footer .footer-content .separator { color: #98a2b3; }
.main-footer .footer-meta { display: inline-flex; align-items: center; gap: 10px; font-size: 11px; color: #667085; }
.main-footer .footer-meta .meta-item { display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e6e9f0; border-radius: 10px; padding: 6px 10px; }

@media (min-width: 992px) {
  .main-footer .footer-inner { justify-content: space-between; }
}
</style>
