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
</style>