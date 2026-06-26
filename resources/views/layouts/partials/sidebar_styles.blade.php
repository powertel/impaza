<style>
/* Sidebar styles */
:root { --header-height: 56px; }

.main-sidebar {
   background: linear-gradient(180deg, #0B1220 0%, #0F172A 100%);
   border-right: 1px solid rgba(30,41,59,.85);
   box-shadow: 0 10px 30px rgba(2,6,23,.35);
   position: fixed;
   top: var(--header-height);
   bottom: 0;
   left: 0;
   height: auto;
   overflow-y: hidden !important; /* make inner .sidebar the scroll container */
   overflow-x: hidden !important;
   overscroll-behavior: contain;
   -webkit-overflow-scrolling: touch;
   /* Ensure sidebar sits above AdminLTE's #sidebar-overlay (z-index:1037) but below header (z-index:1040) */
   z-index: 1039;
 }

@media (min-width: 992px) {
  .main-sidebar { top: 0; height: 100vh; }
}
.main-sidebar .sidebar {
   height: 100%;
   padding-top: 0; /* keep brand close to header */
   display: flex;
   flex-direction: column;
   overflow: hidden; /* contain scroll to nav */
   min-height: 0; /* allow flex children to shrink and scroll */
 }
.main-sidebar .sidebar .user-panel { flex: 0 0 auto; }
.main-sidebar .sidebar { height: 100%; padding-top: 0; display: flex; flex-direction: column; min-height: 0; overflow-y: auto; overflow-x: hidden; -webkit-overflow-scrolling: touch; overscroll-behavior: contain; touch-action: pan-y; scrollbar-width: none; }
    .main-sidebar .sidebar::-webkit-scrollbar { width: 0; height: 0; }
    .main-sidebar .sidebar nav { flex: 0 0 auto; height: auto; overflow: visible; -webkit-overflow-scrolling: auto; overscroll-behavior: auto; touch-action: auto; }
    .main-sidebar .sidebar nav::-webkit-scrollbar { width: 0; height: 0; }

/* Sidebar nav */
.nav-sidebar .nav-header {
  font-size: clamp(10px, 1.1vw, 11px);
  color: rgba(148,163,184,.95);
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
  border-top: 1px solid rgba(148,163,184,.14);
  margin: 6px 0 8px;
}

.nav-sidebar .nav-link,
.nav-sidebar .nav-link p { color: rgba(226,232,240,.96) !important; }

.nav-sidebar .nav-link {
  font-size: clamp(11px, 1.3vw, 13px);
  border-radius: 999px;
  padding: 8px 12px;
  margin: 2px 10px;
  transition: background-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
}
.nav-sidebar .nav-link i.nav-icon {
  font-size: 0.9rem;
  color: rgba(226,232,240,.9);
  margin-right: 8px;
}
.nav-sidebar .nav-link:hover {
  background-color: #1E293B;
  color: #FFFFFF;
}
.nav-sidebar .nav-link.active {
  background-color: #6366F1;
  color: #FFFFFF;
  box-shadow: 0 12px 22px rgba(99,102,241,.26), 0 0 0 1px rgba(99,102,241,.24) inset;
}
.nav-sidebar .nav-link.active i.nav-icon { color: #FFFFFF; }
.nav-sidebar .nav-link:hover i.nav-icon { color: #FFFFFF; }
.nav-sidebar .nav-link:active { transform: translateY(1px); }

/* Tight spacing overrides for brand and nav */
.user-panel { margin: 0 !important; padding: 0 14px !important; border-bottom: 1px solid rgba(148,163,184,.14); height: var(--header-height); display: flex; align-items: center; }
.user-panel .image { display: flex; align-items: center; height: 100%; }
.user-panel .image { padding: 6px 10px; border-radius: 12px; }
.user-panel .image { background: transparent; border: 1px solid transparent; }
.sidebar-mini:not(.sidebar-collapse) .user-panel .image { max-width: 100%; }
.user-panel .image img.impaza-brand-logo { width: auto !important; height: 30px !important; max-width: 100% !important; opacity: 1; }
.user-panel .info h3 { margin: 0 !important; font-size: 14px; line-height: 1.2; }
.impaza-brand-logo { display: block; filter: none; }
.dark-mode-logo { display: none !important; }
html[data-theme="dark"] .light-mode-logo { display: none !important; }
html[data-theme="dark"] .dark-mode-logo { display: inline-block !important; }

html[data-theme="dark"] .main-sidebar .user-panel .image {
  background: transparent;
  border-color: transparent;
}
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
  .sidebar-mini .content-wrapper { margin-left: 240px; }
}
@media (min-width: 1280px) {
  .sidebar-mini .main-sidebar { width: 240px; }
  .sidebar-mini .content-wrapper { margin-left: 240px; }
}

@media (min-width: 992px) {
  .sidebar-mini.sidebar-collapse .main-sidebar { width: 76px; }
  .sidebar-mini.sidebar-collapse .content-wrapper { margin-left: 76px; }

  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-header { display: none; }
  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link { justify-content: center; padding-left: 0; padding-right: 0; border-radius: 14px; margin-left: 12px; margin-right: 12px; }
  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link p { display: none; }
  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link i.nav-icon { margin-right: 0; font-size: 1rem; }
  .sidebar-mini.sidebar-collapse .impaza-brand-logo { width: auto !important; height: 26px !important; }
  .sidebar-mini.sidebar-collapse .main-sidebar { overflow: visible !important; }
  .sidebar-mini.sidebar-collapse .main-sidebar .sidebar { overflow: visible !important; }
  .sidebar-mini.sidebar-collapse .main-sidebar .sidebar nav { overflow-x: visible !important; }
}
@media (max-width: 991.98px) {
  /* Keep consistent scrolling and header offset on small screens */
  .main-sidebar { top: var(--header-height); bottom: 0; height: auto; overflow-y: hidden !important; }
  .main-sidebar .sidebar {
     height: 100%;
     padding-top: 0; /* keep brand close to header */
     display: flex;
     flex-direction: column;
     overflow: hidden; /* contain scroll to nav */
     min-height: 0; /* allow flex children to shrink and scroll */
   }
  .main-sidebar .sidebar nav { flex: 0 0 auto; overflow: visible; }
  .main-sidebar .sidebar nav::-webkit-scrollbar { width: 0; height: 0; }
}

/* Active/selected menu item: force text and icons to white */
.nav-sidebar .nav-link.active,
.nav-sidebar > .nav-item > .nav-link.active { color: #fff !important; }
.nav-sidebar .nav-link.active p { color: #fff !important; }
.nav-sidebar .nav-link.active i.nav-icon,
.nav-sidebar > .nav-item > .nav-link.active i.nav-icon { color: #fff !important; }
/* Treeview nested items */
.nav-treeview .nav-link.active,
.nav-treeview .nav-link.active p,
.nav-treeview .nav-link.active i { color: #fff !important; }

/* Ensure the UL inside the nav can scroll within the available height */
.nav.nav-sidebar { overflow: visible; width: 100%; }
.nav-sidebar .nav-link { display: flex; align-items: center; }
.nav-sidebar .nav-link p { white-space: normal; word-break: break-word; overflow-wrap: anywhere; }

/* Contain scroll within the sidebar and make it smooth */
.main-sidebar .sidebar nav { overscroll-behavior: contain; scroll-behavior: smooth; }
</style>

<!-- Custom scroll/touch interception removed to keep links tappable on mobile -->

<style>
/* Override: make only the nav scroll, keep logo/user panel pinned */
.main-sidebar .sidebar { overflow: hidden !important; }
.main-sidebar .sidebar nav { flex: 1 1 auto !important; overflow-y: auto !important; overflow-x: hidden; -webkit-overflow-scrolling: touch; overscroll-behavior: contain; scroll-behavior: auto !important; scrollbar-width: thin; scrollbar-color: rgba(148,163,184,.35) transparent; }
.main-sidebar .sidebar nav::-webkit-scrollbar { width: 8px; height: 8px; }
.main-sidebar .sidebar nav::-webkit-scrollbar-thumb { background: rgba(148,163,184,.25); border-radius: 8px; }
.main-sidebar .sidebar nav::-webkit-scrollbar-track { background: transparent; }
.main-sidebar .sidebar nav::-webkit-scrollbar-thumb { background: rgba(148,163,184,.28); border-radius: 8px; }
.main-sidebar .sidebar nav::-webkit-scrollbar-track { background: transparent; }

.impaza-sidebar-footer {
  flex: 0 0 auto;
  padding: 12px 12px 14px;
  border-top: 1px solid rgba(148,163,184,.14);
  background: rgba(2,6,23,.15);
}
.impaza-sidebar-user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 10px;
  border: 1px solid rgba(148,163,184,.16);
  border-radius: 16px;
  background: rgba(15,23,42,.35);
}
.impaza-sidebar-user .avatar {
  width: 34px;
  height: 34px;
  border-radius: 999px;
  background: rgba(99,102,241,.20);
  color: rgba(226,232,240,.95);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  border: 1px solid rgba(99,102,241,.28);
}
.impaza-sidebar-user .meta { min-width: 0; }
.impaza-sidebar-user .name { font-weight: 700; color: #FFFFFF; line-height: 1.2; font-size: 12px; }
.impaza-sidebar-user .role { color: rgba(148,163,184,.95); font-size: 11px; line-height: 1.2; }
.impaza-sidebar-user .actions { margin-left: auto; display: inline-flex; gap: 6px; }
.impaza-sidebar-user .actions a {
  width: 30px;
  height: 30px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: rgba(226,232,240,.9);
  background: rgba(148,163,184,.08);
  border: 1px solid rgba(148,163,184,.12);
  text-decoration: none;
  transition: background .15s ease, border-color .15s ease, transform .15s ease;
}
.impaza-sidebar-user .actions a:hover { background: rgba(99,102,241,.18); border-color: rgba(99,102,241,.22); transform: translateY(-1px); }
.impaza-sidebar-user .actions a:active { transform: translateY(0); }
</style>

<style>
/* ============================================================
   Collapsed sidebar — clean icon rail (matches reference)
   ============================================================ */

/* Smooth width/offset transition for sidebar, content and topbar */
.main-sidebar { transition: width .22s ease; }
.content-wrapper { transition: margin-left .22s ease; }

@media (min-width: 992px) {
  /* Brand: center + shrink the logo when collapsed */
  .sidebar-mini.sidebar-collapse .user-panel { padding: 0 6px !important; height: var(--header-height); }
  .sidebar-mini.sidebar-collapse .user-panel .image { width: 100% !important; display: flex; justify-content: center; }
  .sidebar-mini.sidebar-collapse .impaza-brand-logo { width: auto !important; height: 26px !important; margin: 0 auto; }

  /* Footer user card: collapse to a centered avatar only */
  .sidebar-mini.sidebar-collapse .impaza-sidebar-footer { padding: 10px 8px; }
  .sidebar-mini.sidebar-collapse .impaza-sidebar-user { justify-content: center; gap: 0; padding: 8px; }
  .sidebar-mini.sidebar-collapse .impaza-sidebar-user .meta,
  .sidebar-mini.sidebar-collapse .impaza-sidebar-user .actions { display: none; }

  /* Tooltip-style label on hover of a collapsed icon */
  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-item { position: relative; }
  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link > p {
    display: none;
    position: absolute;
    left: calc(100% + 6px);
    top: 50%;
    transform: translateY(-50%);
    background: #1E293B;
    color: #fff;
    padding: 6px 10px;
    border-radius: 8px;
    white-space: nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.45);
    z-index: 1041;
    pointer-events: none;
  }
  .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link:hover > p { display: block; }
}
</style>
