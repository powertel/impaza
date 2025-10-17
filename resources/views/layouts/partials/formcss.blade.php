<style>
    
    /* ========== GLOBAL FORM STYLING ========== */
    
    /* Form Container */
    .form-container {
        background-color: #fcfcfc;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-bottom: 20px;
    }

    /* Form Sections - Fieldset Styling to match Assets */
    .form-section, fieldset {
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-bottom: 25px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 1.5rem 2rem 1rem 2rem;
    }

    .form-section-header, legend {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        padding: 0 0.75rem;
        font-weight: 600;
        font-size: 1.1rem;
        border-bottom: none;
        margin: 0 0 20px 0;
        width: auto;
        border: none;
    }

    .form-section-body {
        padding: 0;
        background-color: #ffffff;
    }

    /* Form Groups and Layout */
    .form-group, .mb-3 {
        margin-bottom: 20px;
    }

    .form-row, .row.g-2, .row.g-3 {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .form-col {
        flex: 1;
        padding: 0 10px;
        min-width: 250px;
    }

    .form-col-full, .col-md-12 {
        flex: 0 0 100%;
        padding: 0 10px;
    }

    .form-col-half, .col-md-6 {
        flex: 0 0 50%;
        padding: 0 10px;
    }

    .form-col-third, .col-md-4 {
        flex: 0 0 33.333%;
        padding: 0 10px;
    }

    .col-md-8 {
        flex: 0 0 66.666%;
        padding: 0 10px;
    }

    /* Bootstrap Grid Enhancements */
    .row.g-2 > [class*="col-"], .row.g-3 > [class*="col-"] {
        padding-right: 0.5rem;
        padding-left: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Form Labels */
    .form-label, label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .form-label.required::after, label.required::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }

    /* Form Controls - Enhanced Styling */
    .form-control, .form-select, input[type="text"], input[type="email"], 
    input[type="password"], input[type="number"], input[type="tel"], 
    input[type="url"], input[type="date"], input[type="datetime-local"], 
    input[type="time"], textarea, select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #374151;
        background-color: #ffffff;
        background-clip: padding-box;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        transition: all 0.15s ease-in-out;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .form-control:focus, .form-select:focus, input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 2px rgba(0, 0, 0, 0.05);
        background-color: #ffffff;
    }

    .form-control:hover, .form-select:hover, input:hover, textarea:hover, select:hover {
        border-color: #9ca3af;
    }

    /* Disabled State */
    .form-control:disabled, .form-select:disabled, input:disabled, textarea:disabled, select:disabled {
        background-color: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Invalid State */
    .form-control.is-invalid, .form-select.is-invalid, input.is-invalid, textarea.is-invalid, select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    /* Valid State */
    .form-control.is-valid, .form-select.is-valid, input.is-valid, textarea.is-valid, select.is-valid {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }



    /* Textarea Specific */
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    /* Select Specific */
    select.form-control, .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 8px center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        padding-right: 35px;
        cursor: pointer;
    }

    /* File Input */
    .form-control[type="file"] {
        padding: 8px 12px;
        cursor: pointer;
    }

    /* Checkbox and Radio */
    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        min-height: 20px;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0;
        margin-right: 8px;
        cursor: pointer;
        border: 2px solid #d1d5db;
        border-radius: 4px;
    }

    .form-check-input[type="radio"] {
        border-radius: 50%;
    }

    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    .form-check-label {
        cursor: pointer;
        font-weight: 400;
        margin-bottom: 0;
    }

    /* Input Groups */
    .input-group {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        width: 100%;
    }

    .input-group-text {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
        color: #6b7280;
        text-align: center;
        white-space: nowrap;
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 6px;
    }

    .input-group > .form-control,
    .input-group > .form-select {
        position: relative;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
    }

    .input-group > .input-group-text:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 0;
    }

    .input-group > .form-control:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .valid-feedback {
        display: block;
        width: 100%;
        margin-top: 4px;
        font-size: 12px;
        color: #16a34a;
    }

    /* Help Text */
    .form-text {
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
    }


    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .form-actions.text-center {
        justify-content: center;
    }

    .form-actions.text-start {
        justify-content: flex-start;
    }

    /* Modal Enhancements - Respecting Bootstrap sizes */
    .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.2);
        animation: fadeInModal 0.2s;
    }

    @keyframes fadeInModal {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        background: transparent;
        color: black;
        border-bottom: 1px solid #eee;
        border-radius: 8px 8px 0 0;
        padding: 1.25rem 1.5rem 0.75rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Responsive modal header padding */
    .modal-lg .modal-header,
    .modal-xl .modal-header {
        padding: 1.25rem 2rem 0.75rem 2rem;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }

    /* Larger title for larger modals */
    .modal-lg .modal-title,
    .modal-xl .modal-title {
        font-size: 1.35rem;
    }

    .modal-body {
        padding: 1.5rem;
        background-color: #fcfcfc;
        border-radius: 0;
        box-shadow: none;
        margin-bottom: 0;
    }

    /* Larger body padding for larger modals */
    .modal-lg .modal-body,
    .modal-xl .modal-body {
        padding: 1.5rem 2rem;
    }

    .modal-footer {
        border-top: 1px solid #eee;
        padding: 1rem 1.5rem 1.5rem 1.5rem;
        background-color: #fcfcfc;
        border-radius: 0 0 8px 8px;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Larger footer padding for larger modals */
    .modal-lg .modal-footer,
    .modal-xl .modal-footer {
        padding: 1rem 2rem 1.5rem 2rem;
    }

    /* Auto-Assign modal whitespace tweaks */
    #autoSettingsModal .modal-dialog { max-width: 680px; }
    #autoSettingsModal .modal-body { padding: 1rem 1.25rem 0.75rem 1.25rem; }
    #autoSettingsModal .form-label { margin-bottom: .25rem; }
    #autoSettingsModal .form-check { margin-top: .25rem; }

    /* Custom Modal for Assets Style - Only for .custom-modal-overlay */
    .custom-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        justify-content: center;
        align-items: center;
    }
    .custom-modal-overlay.active {
        display: flex;
    }
    .custom-modal-overlay .custom-modal {
        background: #fff;
        border-radius: 8px;
        max-width: 1200px;
        width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 2px 16px rgba(0,0,0,0.2);
        animation: fadeInModal 0.2s;
    }

    /* Responsive adjustments for custom modal size */
    @media (max-width: 1400px) {
        .custom-modal-overlay .custom-modal {
            max-width: 1000px;
        }
    }
    @media (max-width: 992px) {
        .custom-modal-overlay .custom-modal {
            max-width: 90vw;
        }
    }

    .custom-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 2rem 0.75rem 2rem;
        border-bottom: 1px solid #eee;
    }
    .custom-modal-title {
        font-size: 1.35rem;
        font-weight: 600;
    }
    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #888;
    }
    .custom-modal-body {
        padding: 1.5rem 2rem;
        background-color: #fcfcfc;
    }
    .custom-modal-footer {
        padding: 1rem 2rem 1.5rem 2rem;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Card Enhancements */
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 15px 20px;
        font-weight: 600;
        color: #495057;
    }

    .card-body {
        padding: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }
        
        .form-col, .form-col-half, .form-col-third {
            flex: 0 0 100%;
            margin-bottom: 15px;
        }
        
        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .form-section-header {
            padding: 12px 15px;
            font-size: 14px;
        }
        
        .form-section-body {
            padding: 15px;
        }
    }

    /* Loading States */
    .btn.loading {
        position: relative;
        color: transparent;
    }

    .btn.loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Bootstrap Form Switch Enhancement */
    .form-check.form-switch {
        padding-left: 2.5em;
        min-height: 1.5rem;
    }

    .form-check.form-switch .form-check-input {
        width: 2em;
        height: 1.2em;
        margin-left: -2.5em;
        background-image: none;
        background-color: #dee2e6;
        border: 1px solid #ced4da;
        border-radius: 2em;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        position: relative;
    }

    .form-check.form-switch .form-check-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        background-image: none;
    }

    .form-check.form-switch .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
        background-image: none;
    }

    .form-check.form-switch .form-check-input::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 2px;
        width: calc(1.2em - 4px);
        height: calc(1.2em - 4px);
        background-color: #fff;
        border-radius: 50%;
        transform: translateY(-50%);
        transition: all 0.15s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .form-check.form-switch .form-check-input:checked::before {
        transform: translateY(-50%) translateX(calc(2em - 1.2em));
    }

    .form-check.form-switch .form-check-label {
        cursor: pointer;
        margin-left: 0.5em;
    }

    /* Custom Switch for non-Bootstrap usage */
    .custom-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .custom-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
        border-radius: 24px;
    }

    .custom-switch .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .custom-switch input:checked + .slider {
        background-color: #3b82f6;
    }

    .custom-switch input:checked + .slider:before {
        transform: translateX(26px);
    }

    /* ========== TABLE DROPDOWN POSITIONING FIXES ========== */
    
    /* Force ALL dropdowns in tables to appear above to prevent overlap */
    .table td .dropdown-menu,
    .table td .dropdown.show .dropdown-menu {
        top: auto !important;
        bottom: 100% !important;
        margin-bottom: 5px !important;
        margin-top: 0 !important;
        transform: none !important;
        position: absolute !important;
        z-index: 1050 !important;
    }

    /* Prevent dropdown from overlapping table header */
    .table thead + tbody tr:first-child td .dropdown-menu,
    .table thead + tbody tr:first-child td .dropdown.show .dropdown-menu {
        bottom: 100% !important;
        margin-bottom: 5px !important;
        max-height: 200px !important;
    }

    /* Add extra margin for first row dropdowns to avoid header overlap */
    .table tbody tr:first-child td .dropdown-menu {
        margin-bottom: 10px !important;
    }

    /* Ensure table containers don't clip dropdowns */
    .table-responsive,
    .custom-datatable-filter,
    .dataTables_wrapper .table-responsive,
    .dataTables_wrapper,
    .card-body {
        overflow: visible !important;
    }

    /* Force dropdown positioning for single row tables */
    .table tbody tr:only-child td .dropdown-menu,
    .table tbody tr:only-child td .dropdown.show .dropdown-menu {
        top: auto !important;
        bottom: 100% !important;
        margin-bottom: 5px !important;
        margin-top: 0 !important;
        transform: none !important;
    }

    /* Override Bootstrap's default dropdown positioning */
    .table td .dropdown-menu.show {
        position: absolute !important;
        z-index: 1050 !important;
    }

    /* Ensure dropdowns are always visible and don't overlap */
    .table td .dropdown-menu {
        min-width: 160px;
        max-width: 180px;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        font-size: 0.9rem;
        padding: 0.2rem 0;
    }

    /* Make dropdown items smaller and more compact */
    .table td .dropdown-menu .dropdown-item {
        padding: 0.35rem 0.5rem;
        font-size: 0.9rem;
        line-height: 1.1;
        margin-bottom: 0.05rem;
    }

    /* Reduce icon size in dropdown items */
    .table td .dropdown-menu .dropdown-item i {
        width: 14px;
        font-size: 0.9rem;
        margin-right: 0.4rem;
    }

    /* Prevent dropdowns from going outside viewport */
    .table td .dropdown {
        position: relative;
    }

    /* Ensure dropdowns stay within table boundaries */
    .table td .dropdown-menu {
        max-width: calc(100vw - 40px);
        left: auto !important;
        right: 0 !important;
    }

    /* Force all table dropdowns to appear above */
    .table tbody tr td .dropdown-menu,
    .table tbody tr td .dropdown.show .dropdown-menu {
        top: auto !important;
        bottom: 100% !important;
        margin-bottom: 5px !important;
        margin-top: 0 !important;
        transform: none !important;
    }

    /* Mobile responsive dropdown positioning */
    @media (max-width: 768px) {
        .table td .dropdown-menu {
            position: fixed !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            top: 50% !important;
            margin-top: -100px !important;
            width: 90vw;
            max-width: 300px;
            z-index: 1050 !important;
        }
    }

    /* Form Check Input */
    .form-check-input {
            border: 2px solid #000; /* Bold outline */
            outline: none; /* Remove default outline */
        }
    .form-check-input:checked {
            background-color: #007bff; /* Change this to your desired checked color */
            border-color: #007bff; /* Change this to your desired border color */
    }

    input::placeholder,
    textarea::placeholder {
        color: #888; /* Placeholder text color */
        font-size: 12px; /* Placeholder text size */
        font-style: italic; /* Placeholder text style */
        opacity: 0.7; /* Placeholder transparency */
    }
</style>
