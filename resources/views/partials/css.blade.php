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
    .custom-modal .fault-modal-header-copy {
        min-width: 0;
    }
    .custom-modal .fault-modal-header-copy .modal-title {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .custom-modal .fault-modal-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .custom-modal .fault-modal-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 28px;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--impaza-border) 86%, transparent);
        background: color-mix(in srgb, var(--impaza-primary) 5%, var(--impaza-card));
        color: var(--impaza-muted);
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
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
    .custom-modal .fault-modal-section-header > div:last-child {
        min-width: 0;
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
    .custom-modal .fault-modal-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid color-mix(in srgb, var(--impaza-primary) 20%, var(--impaza-border));
        background: color-mix(in srgb, var(--impaza-primary) 5%, var(--impaza-card));
        color: var(--impaza-muted);
        font-size: .78rem;
        line-height: 1.45;
    }
    .custom-modal .fault-modal-note i {
        color: var(--impaza-primary);
        margin-top: 2px;
    }
    .custom-modal .fault-modal-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
    }
    .custom-modal .fault-modal-toggle .form-check {
        margin-bottom: 0;
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid color-mix(in srgb, var(--impaza-border) 86%, transparent);
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
    }
    .custom-modal .fault-modal-toggle .form-check-label {
        color: var(--impaza-text);
    }
    .custom-modal .fault-modal-stream {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 420px;
        overflow-y: auto;
        padding-right: 6px;
    }
    .custom-modal .fault-modal-empty {
        padding: 18px;
        border: 1px dashed var(--impaza-border);
        border-radius: 14px;
        text-align: center;
        color: var(--impaza-muted);
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
    }
    .custom-modal .fault-modal-attachment {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }
    .custom-modal .fault-modal-attachment-thumb {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 220px;
        padding: 6px;
        border-radius: 16px;
        border: 1px solid color-mix(in srgb, var(--impaza-border) 88%, transparent);
        background: color-mix(in srgb, var(--impaza-primary) 3%, var(--impaza-card));
        text-decoration: none;
    }
    .custom-modal .fault-modal-attachment-thumb img {
        max-height: 160px;
        object-fit: cover;
    }
    .custom-modal .fault-modal-attachment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .custom-modal .fault-modal-attachment-actions .btn {
        border-radius: 999px;
        min-height: 30px;
        padding-inline: 12px;
    }
    .custom-modal .fault-modal-attachment-missing {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px dashed var(--impaza-border);
        color: var(--impaza-muted);
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
        font-size: .78rem;
    }
    .custom-modal .fault-modal-footer .btn,
    .custom-modal .modal-footer .btn {
        border-radius: 999px;
        min-height: 34px;
        padding-inline: 14px;
        font-weight: 600;
    }
    .custom-modal .card.border-0.shadow-sm {
        border: 1px solid var(--impaza-border) !important;
        border-radius: 18px !important;
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card)) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
        overflow: hidden;
    }
    .custom-modal .card.border-0.shadow-sm .card-header {
        padding: 16px 18px;
        border-bottom: 1px solid var(--impaza-border) !important;
        background: color-mix(in srgb, var(--impaza-primary) 5%, var(--impaza-card)) !important;
    }
    .custom-modal .card.border-0.shadow-sm .card-body {
        padding: 18px;
        background: transparent;
    }
    .custom-modal .card.border-0.shadow-sm .list-group-item {
        border-color: color-mix(in srgb, var(--impaza-border) 86%, transparent);
        background: transparent;
        color: var(--impaza-text);
    }
    .custom-modal .card.border-0.shadow-sm .badge.bg-secondary {
        background: color-mix(in srgb, var(--impaza-muted) 14%, transparent) !important;
        color: var(--impaza-muted) !important;
        border: 1px solid color-mix(in srgb, var(--impaza-muted) 22%, transparent);
    }

    /* Shared fault workflow table/page system */
    .workflow-faults-page .content,
    .content.workflow-faults-page {
        padding-inline: 6px;
    }
    .faults-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    .faults-kpi-link {
        text-decoration: none;
        color: inherit;
        display: block;
        min-width: 0;
    }
    .faults-kpi-card {
        position: relative;
        display: flex;
        align-items: stretch;
        min-height: 108px;
        border-radius: 18px;
        border: 1px solid var(--impaza-border);
        background: var(--impaza-card);
        box-shadow: var(--impaza-shadow-sm);
        overflow: hidden;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .faults-kpi-card::before {
        content: "";
        width: 4px;
        flex: 0 0 4px;
        background: var(--faults-kpi-color, var(--impaza-primary));
    }
    .faults-kpi-link:hover .faults-kpi-card,
    .faults-kpi-link:focus-visible .faults-kpi-card {
        transform: translateY(-2px);
        box-shadow: var(--impaza-shadow);
        border-color: color-mix(in srgb, var(--faults-kpi-color, var(--impaza-primary)) 26%, var(--impaza-border));
    }
    .faults-kpi-body {
        flex: 1 1 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 18px;
        min-width: 0;
    }
    .faults-kpi-copy {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .faults-kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .92rem;
        color: var(--faults-kpi-color, var(--impaza-primary));
        background: color-mix(in srgb, var(--faults-kpi-color, var(--impaza-primary)) 12%, transparent);
        flex: 0 0 auto;
    }
    .faults-kpi-label {
        font-size: .72rem;
        color: var(--impaza-muted);
        line-height: 1.25;
    }
    .faults-kpi-title {
        font-size: .86rem;
        font-weight: 700;
        color: var(--impaza-text);
        line-height: 1.2;
    }
    .faults-kpi-value {
        font-size: 1.65rem;
        font-weight: 700;
        line-height: 1;
        color: var(--impaza-text);
        flex: 0 0 auto;
    }
    .faults-panel {
        border: 1px solid var(--impaza-border);
        border-radius: 22px;
        background: var(--impaza-card);
        box-shadow: var(--impaza-shadow-sm);
        overflow: visible;
    }
    .faults-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--impaza-border);
        background: color-mix(in srgb, var(--impaza-primary) 4%, var(--impaza-card));
    }
    .faults-panel-copy {
        min-width: 0;
    }
    .faults-panel-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--impaza-text);
        letter-spacing: -.01em;
    }
    .faults-panel-subtitle {
        margin-top: 4px;
        color: var(--impaza-muted);
        font-size: .74rem;
        line-height: 1.4;
    }
    .faults-panel-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
    }
    .faults-toolbar {
        padding: 18px 20px;
        border-bottom: 1px solid var(--impaza-border);
        background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
    }
    .faults-toolbar-grid {
        display: grid;
        grid-template-columns: 116px minmax(160px, 1fr) minmax(150px, 1fr) minmax(240px, 1.35fr) auto auto;
        gap: 12px;
        align-items: center;
    }
    .faults-toolbar-field {
        min-width: 0;
    }
    .faults-toolbar-field .input-group-text,
    .faults-toolbar-search .input-group-text {
        min-width: 42px;
        justify-content: center;
    }
    .faults-table-shell {
        padding: 18px 20px 14px;
    }
    .faults-table-wrap {
        border-radius: 18px;
        overflow: auto;
    }
    .faults-table thead th {
        white-space: nowrap;
        padding: 11px 12px;
        font-size: .7rem;
    }
    .faults-table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
    }
    .faults-table .faults-ref {
        display: inline-flex;
        flex-direction: column;
        gap: 3px;
    }
    .faults-table .faults-ref a {
        font-weight: 700;
    }
    .faults-table .faults-cell-main {
        font-weight: 600;
        color: var(--impaza-text);
        line-height: 1.28;
    }
    .faults-table .faults-cell-sub {
        margin-top: 3px;
        font-size: .72rem;
        line-height: 1.3;
        color: var(--impaza-muted);
    }
    .faults-table .faults-status-link {
        text-decoration: none;
    }
    .faults-table .faults-status-link .impaza-badge {
        min-height: 24px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .01em;
        box-shadow: none;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .faults-table .faults-status-link:hover .impaza-badge,
    .faults-table .faults-status-link:focus-visible .impaza-badge {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
    }
    .faults-age-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(99, 102, 241, .08);
        color: var(--impaza-primary);
        border: 1px solid rgba(99, 102, 241, .14);
        font-weight: 700;
        font-size: .68rem;
        white-space: nowrap;
    }
    .faults-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 6px;
    }
    .faults-actions .btn {
        min-height: 30px;
        border-radius: 999px;
        padding-inline: 10px;
        font-weight: 600;
    }
    .faults-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 2px 2px;
    }
    .faults-table-footer .pagination {
        margin-bottom: 0;
    }
    html[data-theme="dark"] .faults-kpi-link:hover .faults-kpi-card,
    html[data-theme="dark"] .faults-kpi-link:focus-visible .faults-kpi-card {
        box-shadow: var(--impaza-shadow);
    }
    .workflow-faults-page .card,
    .content.workflow-faults-page .card {
        border: 1px solid var(--impaza-border);
        border-radius: 22px;
        background: var(--impaza-card);
        box-shadow: var(--impaza-shadow-sm);
        overflow: visible;
    }
    .workflow-faults-page .card-header,
    .content.workflow-faults-page .card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--impaza-border);
        background: color-mix(in srgb, var(--impaza-primary) 4%, var(--impaza-card));
    }
    .workflow-faults-page .card-title,
    .content.workflow-faults-page .card-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--impaza-text);
        letter-spacing: -.01em;
        text-transform: none;
    }
    .workflow-faults-page .card-tools,
    .content.workflow-faults-page .card-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
    }
    .workflow-faults-page .card-body,
    .content.workflow-faults-page .card-body {
        padding: 18px 20px 14px;
    }
    .workflow-faults-page .filter-toolbar,
    .content.workflow-faults-page .filter-toolbar {
        display: grid !important;
        grid-template-columns: minmax(110px, 140px) minmax(180px, 1fr) auto auto;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px !important;
    }
    .workflow-faults-page .filter-toolbar form,
    .content.workflow-faults-page .filter-toolbar form {
        width: 100%;
        min-width: 0;
    }
    .workflow-faults-page .filter-toolbar .input-group,
    .workflow-faults-page .filter-toolbar .form-select,
    .workflow-faults-page .filter-toolbar .form-control,
    .content.workflow-faults-page .filter-toolbar .input-group,
    .content.workflow-faults-page .filter-toolbar .form-select,
    .content.workflow-faults-page .filter-toolbar .form-control {
        min-height: 36px;
    }
    .workflow-faults-page .table-responsive,
    .content.workflow-faults-page .table-responsive {
        border-radius: 18px;
        overflow: auto;
    }
    .workflow-faults-page .table,
    .content.workflow-faults-page .table {
        margin-bottom: 0;
        color: var(--impaza-text);
    }
    .workflow-faults-page .table thead th,
    .content.workflow-faults-page .table thead th {
        white-space: nowrap;
        padding: 11px 12px;
        font-size: .7rem;
        color: var(--impaza-muted);
        font-weight: 700;
        letter-spacing: .03em;
        border-bottom: 1px solid var(--impaza-border);
        background: var(--impaza-card);
        text-transform: uppercase;
    }
    .workflow-faults-page .table tbody td,
    .content.workflow-faults-page .table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
        border-color: color-mix(in srgb, var(--impaza-border) 86%, transparent);
    }
    .workflow-faults-page .table tbody tr:hover,
    .content.workflow-faults-page .table tbody tr:hover {
        background: color-mix(in srgb, var(--impaza-primary) 3%, var(--impaza-card));
    }
    .workflow-faults-page .badge.rounded-pill,
    .content.workflow-faults-page .badge.rounded-pill {
        min-height: 24px;
        padding: 4px 10px;
        border-radius: 999px !important;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .01em;
    }
    .workflow-faults-page .age-ticker,
    .content.workflow-faults-page .age-ticker {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 4px 10px;
        border-radius: 999px !important;
        background: rgba(99, 102, 241, .08) !important;
        color: var(--impaza-primary) !important;
        border: 1px solid rgba(99, 102, 241, .14);
        font-weight: 700;
        font-size: .68rem !important;
    }
    .workflow-faults-page .btn,
    .content.workflow-faults-page .btn {
        border-radius: 999px;
    }
    .workflow-faults-page .table .btn,
    .content.workflow-faults-page .table .btn {
        min-height: 30px;
        padding-inline: 10px;
        font-weight: 600;
    }
    .workflow-faults-page .workflow-actions,
    .content.workflow-faults-page .workflow-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 6px;
    }
    .workflow-faults-page .workflow-pagination,
    .content.workflow-faults-page .workflow-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
    }
    .workflow-faults-page .workflow-kpis,
    .content.workflow-faults-page .workflow-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 1rem;
    }
    .workflow-faults-page .workflow-kpis .col,
    .content.workflow-faults-page .workflow-kpis .col {
        width: auto;
        flex: initial;
    }
    .workflow-faults-page .workflow-kpis .card-body,
    .content.workflow-faults-page .workflow-kpis .card-body {
        padding: 16px 18px;
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
        width: min(100%, 760px);
        max-width: 82%;
        padding: .85rem .95rem;
        border-radius: 18px;
        background: color-mix(in srgb, var(--impaza-primary) 4%, var(--impaza-card));
        border: 1px solid var(--impaza-border);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .chat-msg-self {
        align-self: flex-end;
        background: rgba(99, 102, 241, .10);
    }
    .chat-msg-other { align-self: flex-start; }
    .chat-msg-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        font-size: .75rem;
        color: var(--impaza-muted);
        margin-bottom: .35rem;
    }
    .chat-msg-body {
        white-space: pre-wrap;
        color: var(--impaza-text);
        line-height: 1.5;
    }

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
        .workflow-faults-page .card-header,
        .content.workflow-faults-page .card-header {
            flex-direction: column;
            align-items: stretch;
        }
        .faults-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .faults-panel-header {
            flex-direction: column;
            align-items: stretch;
        }
        .workflow-faults-page .card-tools,
        .content.workflow-faults-page .card-tools {
            justify-content: flex-start;
        }
        .faults-panel-actions {
            justify-content: flex-start;
        }
        .faults-table-shell,
        .faults-toolbar {
            padding-inline: 16px;
        }
        .faults-toolbar-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .faults-toolbar-search,
        .faults-toolbar-grid .faults-toolbar-submit,
        .faults-toolbar-grid .faults-toolbar-reset {
            grid-column: span 2;
        }
        .workflow-faults-page .filter-toolbar,
        .content.workflow-faults-page .filter-toolbar {
            grid-template-columns: 1fr 1fr;
        }
        .workflow-faults-page .filter-toolbar form,
        .content.workflow-faults-page .filter-toolbar form {
            grid-column: span 2;
        }
        .workflow-faults-page .workflow-kpis,
        .content.workflow-faults-page .workflow-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .workflow-faults-page .card-body,
        .content.workflow-faults-page .card-body {
            padding: 14px 14px 12px;
        }
        .faults-kpi-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .faults-kpi-body {
            padding: 14px 16px;
        }
        .faults-kpi-value {
            font-size: 1.45rem;
        }
        .faults-table-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        .faults-table-shell {
            padding: 14px 14px 12px;
        }
        .faults-toolbar-grid {
            grid-template-columns: 1fr;
        }
        .faults-toolbar-search,
        .faults-toolbar-grid .faults-toolbar-submit,
        .faults-toolbar-grid .faults-toolbar-reset {
            grid-column: auto;
        }
        .faults-table-wrap {
            overflow: visible;
            background: transparent;
            border-radius: 0;
        }
        .faults-table {
            min-width: 0 !important;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .faults-table thead {
            display: none;
        }
        .faults-table tbody {
            display: block;
        }
        .faults-table tbody tr {
            display: block;
            border: 1px solid var(--impaza-border);
            border-radius: 16px;
            background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
            box-shadow: var(--impaza-shadow-sm);
            overflow: hidden;
        }
        .faults-table tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 9px 14px;
            border: 0;
            text-align: right;
        }
        .faults-table tbody td + td {
            border-top: 1px solid color-mix(in srgb, var(--impaza-border) 85%, transparent);
        }
        .faults-table tbody td::before {
            content: attr(data-label);
            flex: 0 0 42%;
            text-align: left;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--impaza-muted);
        }
        .faults-table .faults-ref,
        .faults-table .faults-cell-main,
        .faults-table .faults-cell-sub {
            text-align: right;
        }
        .faults-table .faults-ref {
            align-items: flex-end;
        }
        .faults-table .faults-status-link,
        .faults-table .faults-age-pill {
            margin-left: auto;
        }
        .faults-actions {
            width: 100%;
            justify-content: flex-end;
        }
        .faults-table td.text-end {
            text-align: right !important;
        }
        .workflow-faults-page .filter-toolbar,
        .content.workflow-faults-page .filter-toolbar {
            grid-template-columns: 1fr;
        }
        .workflow-faults-page .filter-toolbar form,
        .content.workflow-faults-page .filter-toolbar form {
            grid-column: auto;
        }
        .workflow-faults-page .workflow-pagination,
        .content.workflow-faults-page .workflow-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
        .workflow-faults-page .workflow-kpis,
        .content.workflow-faults-page .workflow-kpis {
            grid-template-columns: 1fr;
        }
        .workflow-faults-page .table-responsive,
        .content.workflow-faults-page .table-responsive {
            overflow: visible;
            background: transparent;
            border-radius: 0;
        }
        .workflow-faults-page .table,
        .content.workflow-faults-page .table {
            min-width: 0 !important;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .workflow-faults-page .table thead,
        .content.workflow-faults-page .table thead {
            display: none;
        }
        .workflow-faults-page .table tbody,
        .content.workflow-faults-page .table tbody {
            display: block;
        }
        .workflow-faults-page .table tbody tr,
        .content.workflow-faults-page .table tbody tr {
            display: block;
            border: 1px solid var(--impaza-border);
            border-radius: 16px;
            background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
            box-shadow: var(--impaza-shadow-sm);
            overflow: hidden;
        }
        .workflow-faults-page .table tbody td,
        .content.workflow-faults-page .table tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 9px 14px;
            border: 0;
            text-align: right;
        }
        .workflow-faults-page .table tbody td + td,
        .content.workflow-faults-page .table tbody td + td {
            border-top: 1px solid color-mix(in srgb, var(--impaza-border) 85%, transparent);
        }
        .workflow-faults-page .table tbody td::before,
        .content.workflow-faults-page .table tbody td::before {
            content: attr(data-label);
            flex: 0 0 42%;
            text-align: left;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--impaza-muted);
        }
        .workflow-faults-page .table td.text-end,
        .content.workflow-faults-page .table td.text-end {
            text-align: right !important;
        }
        .workflow-faults-page .workflow-actions,
        .content.workflow-faults-page .workflow-actions {
            width: 100%;
            justify-content: flex-end;
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
