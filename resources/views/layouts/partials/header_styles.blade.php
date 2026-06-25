<style>
/* Header (navbar) styles */
.main-header {
  position: sticky;
  top: 0;
  z-index: 1040;
  border-bottom: 1px solid var(--impaza-border);
  background: rgba(248,250,252,.88);
  backdrop-filter: blur(10px);
  box-shadow: var(--impaza-shadow-sm);
}
.main-header .navbar-nav .nav-link { font-size: 12px; padding: 6px 10px; }
.main-header .navbar-nav .nav-link i { font-size: 0.9rem; }
.main-header .navbar-nav .nav-link { color: var(--impaza-muted); }
.main-header .navbar-nav .nav-link:hover { color: var(--impaza-text); }

html[data-theme="dark"] .main-header {
  background: rgba(2,6,23,.75);
  border-bottom-color: var(--impaza-border);
}

.impaza-topbar { padding-left: 10px; padding-right: 10px; }
.impaza-topbar-left { display: inline-flex; align-items: center; gap: 10px; min-width: 240px; }
.impaza-topbar-center { flex: 1 1 auto; display: flex; justify-content: center; padding: 0 12px; }
.impaza-topbar-right { display: inline-flex; align-items: center; gap: 8px; }

.impaza-topbar-breadcrumb {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: var(--impaza-muted);
  white-space: nowrap;
}
.impaza-topbar-breadcrumb a { color: var(--impaza-muted); text-decoration: none; }
.impaza-topbar-breadcrumb a:hover { color: var(--impaza-text); text-decoration: none; }
.impaza-topbar-breadcrumb .sep { opacity: .65; }
.impaza-topbar-breadcrumb .current { color: var(--impaza-text); font-weight: 600; }

.impaza-topbar-search {
  width: min(680px, 100%);
  height: 36px;
  border-radius: 999px;
  border: 1px solid var(--impaza-border);
  background: rgba(255,255,255,.75);
  color: var(--impaza-muted);
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 0 12px 0 14px;
  box-shadow: 0 1px 0 rgba(15,23,42,.02);
  transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
}
.impaza-topbar-search:hover { border-color: rgba(99,102,241,.45); }
.impaza-topbar-search:focus { outline: none; border-color: rgba(99,102,241,.55); box-shadow: 0 0 0 .2rem rgba(99,102,241,.18); }
.impaza-topbar-search .left { display: inline-flex; align-items: center; gap: 10px; }
.impaza-topbar-search .left i { color: var(--impaza-muted); }
.impaza-topbar-search .placeholder { color: var(--impaza-muted); font-size: 12px; }
.impaza-topbar-search .kbd {
  font-size: 10px;
  color: var(--impaza-muted);
  border: 1px solid var(--impaza-border);
  background: rgba(248,250,252,.9);
  padding: 3px 8px;
  border-radius: 999px;
}
html[data-theme="dark"] .impaza-topbar-search {
  background: rgba(15,23,42,.55);
  border-color: var(--impaza-border);
}
html[data-theme="dark"] .impaza-topbar-search .kbd { background: rgba(2,6,23,.55); }

.impaza-topbar-icon {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: 1px solid transparent;
  background: transparent;
  color: var(--impaza-muted);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background .15s ease, border-color .15s ease, color .15s ease, transform .15s ease;
}
.impaza-topbar-icon:hover { background: rgba(99,102,241,.08); border-color: rgba(99,102,241,.20); color: var(--impaza-text); }
.impaza-topbar-icon:active { transform: translateY(1px); }

.impaza-topbar-dropdown-menu {
  border-radius: 14px;
  border: 1px solid var(--impaza-border);
  box-shadow: var(--impaza-shadow);
  overflow: hidden;
}

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
  background: var(--impaza-bg);
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
.content-wrapper { scrollbar-width: thin; scrollbar-color: rgba(148,163,184,.45) var(--impaza-bg); }
.content-wrapper::-webkit-scrollbar { width: 8px; height: 8px; }
.content-wrapper::-webkit-scrollbar-thumb { background: rgba(148,163,184,.35); border-radius: 8px; }
.content-wrapper::-webkit-scrollbar-track { background: var(--impaza-bg); }
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
.main-footer .footer-content { display: inline-flex; align-items: center; gap: 6px; background: var(--impaza-card); border: 1px solid var(--impaza-border); border-radius: 12px; padding: 8px 12px; box-shadow: var(--impaza-shadow-sm); }
.main-footer .footer-content .separator { color: #98a2b3; }
.main-footer .footer-meta { display: inline-flex; align-items: center; gap: 10px; font-size: 11px; color: #667085; }
.main-footer .footer-meta .meta-item { display: inline-flex; align-items: center; gap: 6px; background: rgba(248,250,252,.9); border: 1px solid var(--impaza-border); border-radius: 10px; padding: 6px 10px; }
html[data-theme="dark"] .main-footer .footer-meta .meta-item { background: rgba(2,6,23,.35); }

@media (min-width: 992px) {
  .main-footer .footer-inner { justify-content: space-between; }
}
</style>
