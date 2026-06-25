<style>
    .card {
        margin-right: 4px;
        border-color: var(--impaza-border);
        background: var(--impaza-card);
        box-shadow: var(--impaza-shadow-sm);
    }
    .p-0 { padding: 10px !important; }
    .card-header {
        background-color: color-mix(in srgb, var(--impaza-primary) 8%, var(--impaza-card));
        border-bottom: 1px solid color-mix(in srgb, var(--impaza-primary) 12%, var(--impaza-border));
        color: var(--impaza-text);
    }
    .card-header .card-title { text-transform: uppercase; }
    .card-footer .btn { margin: 13px 12px 12px 10px; float: right; }

    .modal-backdrop.show { backdrop-filter: blur(6px); background: rgba(15, 23, 42, .38); }

    /* Modern modal UI refinements */
    .custom-modal .modal-dialog { max-width: 960px; }
    .custom-modal .modal-content {
        border-radius: 22px;
        border: 1px solid var(--impaza-border);
        background: var(--impaza-card);
        color: var(--impaza-text);
        box-shadow: 0 20px 60px rgba(15, 23, 42, .18);
        overflow: hidden;
    }
    html[data-theme="dark"] .custom-modal .modal-content {
        box-shadow: 0 24px 70px rgba(2, 6, 23, .62);
    }
    .custom-modal .modal-header,
    .custom-modal .modal-footer {
        border-color: var(--impaza-border);
        background: color-mix(in srgb, var(--impaza-primary) 4%, var(--impaza-card));
    }
    .custom-modal .modal-header {
        padding: 18px 22px;
    }
    .custom-modal .modal-title {
        color: var(--impaza-text);
        font-weight: 700;
        letter-spacing: -.01em;
    }
    .custom-modal .modal-body {
        padding: 20px 22px;
        background: var(--impaza-card);
        color: var(--impaza-text);
    }
    .custom-modal .modal-footer {
        padding: 14px 22px;
        gap: 10px;
    }
    .custom-modal .form-label {
        font-weight: 600;
        color: var(--impaza-text);
        margin-bottom: 6px;
    }
    .custom-modal .form-control,
    .custom-modal .form-select,
    .custom-modal .select2-selection {
        border-radius: 12px;
        background: var(--impaza-card) !important;
        color: var(--impaza-text) !important;
        border-color: var(--impaza-border) !important;
    }
    .custom-modal .form-control:focus,
    .custom-modal .form-select:focus,
    .custom-modal .select2-container--default .select2-selection--single:focus,
    .custom-modal .select2-container--default.select2-container--focus .select2-selection--single,
    .custom-modal .select2-container--default.select2-container--focus .select2-selection--multiple {
        background: var(--impaza-card) !important;
        color: var(--impaza-text) !important;
        border-color: rgba(99, 102, 241, .55) !important;
    }
    .custom-modal .select2-container--default .select2-selection--single,
    .custom-modal .select2-container--default .select2-selection--multiple,
    .custom-modal .select2-dropdown,
    .custom-modal .select2-search__field {
        background: var(--impaza-card) !important;
        color: var(--impaza-text) !important;
        border-color: var(--impaza-border) !important;
    }
    .custom-modal .select2-container--default .select2-selection--single .select2-selection__rendered,
    .custom-modal .select2-container--default .select2-selection--multiple .select2-selection__rendered,
    .custom-modal .select2-container--default .select2-search--dropdown .select2-search__field {
        color: var(--impaza-text) !important;
    }
    .custom-modal .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .custom-modal .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: var(--impaza-muted) !important;
    }
    .custom-modal input:-webkit-autofill,
    .custom-modal input:-webkit-autofill:hover,
    .custom-modal input:-webkit-autofill:focus,
    .custom-modal textarea:-webkit-autofill,
    .custom-modal textarea:-webkit-autofill:hover,
    .custom-modal textarea:-webkit-autofill:focus,
    .custom-modal select:-webkit-autofill,
    .custom-modal select:-webkit-autofill:hover,
    .custom-modal select:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--impaza-text);
        -webkit-box-shadow: 0 0 0 1000px var(--impaza-card) inset;
        transition: background-color 9999s ease-out 0s;
    }
    .custom-modal .btn-close {
        filter: none;
        opacity: .85;
    }
    html[data-theme="dark"] .custom-modal .btn-close {
        filter: invert(1) grayscale(1);
    }
    .custom-modal .btn-primary { background-color: var(--impaza-primary); border-color: var(--impaza-primary); }
    .custom-modal .btn-primary:hover { filter: brightness(1.05); }
    .custom-modal .btn-outline-secondary {
        border-color: var(--impaza-border);
        color: var(--impaza-muted);
        background: transparent;
    }
    .custom-modal .btn-outline-secondary:hover {
        background: color-mix(in srgb, var(--impaza-primary) 6%, var(--impaza-card));
        color: var(--impaza-text);
    }
    .custom-modal .text-muted,
    .custom-modal .form-text,
    .custom-modal small {
        color: var(--impaza-muted) !important;
    }

    /* Fault modal panels */
    .custom-modal .fault-modal-section {
        border: 1px solid var(--impaza-border);
        border-radius: 18px;
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        overflow: hidden;
    }
    .custom-modal .fault-modal-section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--impaza-border);
        background: color-mix(in srgb, var(--impaza-primary) 5%, var(--impaza-card));
    }
    .custom-modal .fault-modal-section-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(99, 102, 241, .12);
        color: var(--impaza-primary);
        flex: 0 0 auto;
    }
    .custom-modal .fault-modal-section-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--impaza-text);
        line-height: 1.2;
    }
    .custom-modal .fault-modal-section-subtitle {
        font-size: .75rem;
        color: var(--impaza-muted);
        margin-top: 2px;
    }
    .custom-modal .fault-modal-section-body {
        padding: 18px;
    }
    .custom-modal .fault-modal-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
    }
    .custom-modal .fault-modal-kv {
        min-width: 0;
        padding: 12px 14px;
        border: 1px solid color-mix(in srgb, var(--impaza-border) 88%, transparent);
        border-radius: 14px;
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
    }
    .custom-modal .fault-modal-kv-label {
        display: block;
        font-size: .68rem;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: var(--impaza-muted);
        font-weight: 700;
        margin-bottom: 4px;
    }
    .custom-modal .fault-modal-kv-value {
        color: var(--impaza-text);
        font-weight: 600;
        line-height: 1.45;
        word-break: break-word;
    }
    .custom-modal .fault-modal-kv-value .badge {
        vertical-align: middle;
    }

    /* Smooth modal entrance */
    .custom-modal .modal.fade .modal-dialog {
        transition: transform .22s ease-out, opacity .22s ease-out;
        transform: translateY(12px) scale(.985);
        opacity: 0;
    }
    .custom-modal .modal.show .modal-dialog {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* Responsive attachment image */
    .custom-modal .modal-body img {
        display: block;
        height: auto;
        max-width: 100%;
        border-radius: 10px;
    }

    /* Chat-style remarks */
    .chat-messages { display: flex; flex-direction: column; gap: .75rem; }
    .chat-msg {
        max-width: 75%;
        padding: .75rem .9rem;
        border-radius: 1rem;
        background: color-mix(in srgb, var(--impaza-primary) 4%, var(--impaza-card));
        border: 1px solid var(--impaza-border);
    }
    .chat-msg-self {
        align-self: flex-end;
        background: rgba(99, 102, 241, .10);
    }
    .chat-msg-other { align-self: flex-start; }
    .chat-msg-meta { font-size: .75rem; color: var(--impaza-muted); margin-bottom: .25rem; }
    .chat-msg-body { white-space: pre-wrap; color: var(--impaza-text); }

    /* Dashboard specific refinements */
    .dashboard-page .stat-card { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 2px rgba(16,24,40,.06); }
    .dashboard-page .stat-title { font-size: 12px; color: #6b7280; }
    .dashboard-page .stat-value { font-size: 1.1rem; font-weight: 700; color: #111827; }
    .dashboard-page .stat-card .card-body { padding: .75rem 1rem; }

    .dashboard-page .metric-icon { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
    .dashboard-page .metric-icon.icon-faults { background: #eef4ff; color: #1f5cff; }
    .dashboard-page .metric-icon.icon-customers { background: #e8fff2; color: #16a34a; }
    .dashboard-page .metric-icon.icon-links { background: #fff7ed; color: #f97316; }
    .dashboard-page .metric-icon.icon-open { background: #f1f5f9; color: #0ea5e9; }

    .dashboard-page .stat-card-sm .card-body { padding: .5rem .75rem; }
    .dashboard-page .stat-card-sm .stat-title { font-size: 11px; }
    .dashboard-page .stat-card-sm .stat-value { font-size: 1rem; }
    .dashboard-page .stat-card-sm .metric-icon { width: 28px; height: 28px; }

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
    .age-ticker { color: #dc2626; font-weight: 700; }

    @media (max-width: 991.98px) {
        .custom-modal .modal-dialog {
            max-width: calc(100vw - 1rem);
            margin: .5rem auto;
        }
        .custom-modal .modal-header,
        .custom-modal .modal-body,
        .custom-modal .modal-footer {
            padding-left: 16px;
            padding-right: 16px;
        }
        .custom-modal .fault-modal-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
  /* Shared show/hide password toggle styles */
  .password-wrapper { position: relative; }
  .password-wrapper .form-control { padding-right: 40px; }
  .toggle-password {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    color: #7c8da1; /* muted */
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .toggle-password:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(76,111,255,0.18);
    border-radius: 8px;
  }
  .toggle-password svg { width: 18px; height: 18px; }

  /* Password Strength Meter */
  .password-strength-meter {
    height: 4px;
    background-color: #e2e8f0;
    margin-top: 8px;
    border-radius: 2px;
    overflow: hidden;
    display: none; /* Hidden until typing */
  }
  .password-strength-meter .strength-bar {
    height: 100%;
    width: 0;
    transition: width 0.3s ease, background-color 0.3s ease;
  }
  .password-strength-text {
    font-size: 0.75rem;
    margin-top: 4px;
    display: none;
    font-weight: 500;
  }
  
  /* Strength Colors */
  .strength-weak { background-color: #ef4444 !important; }   /* Red */
  .strength-fair { background-color: #f59e0b !important; }   /* Orange */
  .strength-good { background-color: #3b82f6 !important; }   /* Blue */
  .strength-strong { background-color: #10b981 !important; } /* Green */
  
  .text-weak { color: #ef4444; }
  .text-fair { color: #f59e0b; }
  .text-good { color: #3b82f6; }
  .text-strong { color: #10b981; }
</style>
