<style>
    .card { margin-right:4px; }
    .p-0 { padding: 10px !important; }
    .card-header { background-color: aliceblue; }
    .card-header .card-title { text-transform: uppercase; }
    .card-footer .btn { margin: 13px 12px 12px 10px;  float:right; }

    /* Modern modal UI refinements */
    .custom-modal .modal-dialog { max-width: 900px; }
    .custom-modal .modal-content { border-radius: 14px; border: 1px solid #e5e7eb; box-shadow: 0 10px 30px rgba(2, 6, 23, 0.15); overflow: hidden; }
    .modal-backdrop.show { backdrop-filter: blur(2px); }
    .custom-modal .modal-header { border-bottom: 1px solid #eef2f7; }
    .custom-modal .modal-title { font-weight: 600; letter-spacing: .02em; }
    .custom-modal .modal-body { padding-top: 1rem; }
    .custom-modal .modal-footer { border-top: 1px solid #eef2f7; }
    .custom-modal .form-label { font-weight: 500; color: #0f172a; }
    .custom-modal .form-control, .custom-modal .form-select { border-radius: .5rem; }
    .custom-modal .btn-primary { background-color: #1f5cff; border-color: #1f5cff; }
    .custom-modal .btn-primary:hover { filter: brightness(1.05); }
    .custom-modal .btn-outline-secondary { border-color: #cbd5e1; color: #334155; }
    .custom-modal .btn-outline-secondary:hover { background-color: #f1f5f9; }

    /* Smooth modal entrance */
    .custom-modal .modal.fade .modal-dialog { transition: transform .2s ease-out, opacity .2s ease-out; transform: translateY(10px); opacity: 0; }
    .custom-modal .modal.show .modal-dialog { transform: translateY(0); opacity: 1; }

    /* Responsive attachment image */
    .custom-modal .modal-body img { display: block; height: auto; max-width: 100%; border-radius: 8px; }

    /* Chat-style remarks */
    .chat-messages { display: flex; flex-direction: column; gap: .75rem; }
    .chat-msg { max-width: 75%; padding: .5rem .75rem; border-radius: .75rem; background: #f1f5f9; }
    .chat-msg-self { align-self: flex-end; background: #dbeafe; }
    .chat-msg-other { align-self: flex-start; }
    .chat-msg-meta { font-size: .75rem; color: #64748b; margin-bottom: .25rem; }
    .chat-msg-body { white-space: pre-wrap; }

/* Dashboard specific refinements */
  .dashboard-page .stat-card { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 2px rgba(16,24,40,.06); }
  .dashboard-page .stat-title { font-size: 12px; color: #6b7280; }
  .dashboard-page .stat-value { font-size: 1.1rem; font-weight: 700; color: #111827; }

  .dashboard-page .metric-icon { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
  .dashboard-page .metric-icon.icon-faults { background: #eef4ff; color: #1f5cff; }
  .dashboard-page .metric-icon.icon-customers { background: #e8fff2; color: #16a34a; }
  .dashboard-page .metric-icon.icon-links { background: #fff7ed; color: #f97316; }
  .dashboard-page .metric-icon.icon-open { background: #f1f5f9; color: #0ea5e9; }

  .dashboard-page .toolbar-card { border: 1px solid #e5e7eb; background:#fff; box-shadow: 0 1px 2px rgba(16,24,40,.06); }
  .dashboard-page .toolbar-card .btn { font-weight: 500; }
  .dashboard-page .toolbar-card .btn.btn-outline-secondary { border-color:#e5e7eb; color:#334155; }
  .dashboard-page .toolbar-card .btn.btn-outline-secondary:hover { background:#f8fafc; }
  .dashboard-page .toolbar-card .btn.btn-primary { background:#1f5cff; border-color:#1f5cff; }

  .dashboard-page .card .card-header { background:#f8fafc; border-bottom:1px solid #eef2f7; }
  .dashboard-page .card .card-title { font-size:13px; }

  .dashboard-page .card-tools .form-control-sm { border-radius:9999px; }
  .dashboard-page .card-tools select.form-control-sm { border-radius:9999px; padding-left:10px; padding-right:10px; }

  .dashboard-page .table { border-color:#eef2f7; }
  .dashboard-page .table thead th { color: #6b7280; font-size: 11px; letter-spacing: .02em; border-bottom:1px solid #eef2f7; }
  .dashboard-page .table tbody tr:hover { background-color: #f8fafc; }
  .dashboard-page .table tbody td { border-top: 1px solid #f1f5f9; }
  .dashboard-page .table .no-data td { text-align: center; padding: 16px; }
</style>
