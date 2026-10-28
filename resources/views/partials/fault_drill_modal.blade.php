{{-- Shared fault drill-down panel. Needs public/css/fault-drill.css, js/faults-pdf.js and js/reports-drill.js --}}
<div class="modal fade drill-modal" id="drillModal" tabindex="-1" aria-labelledby="drillTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex-grow-1 min-w-0">
                    <div class="drill-crumbs" id="drillCrumbs"></div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                        <button type="button" class="btn btn-sm btn-light drill-back d-none" id="drillBack" title="Back"><i class="fas fa-arrow-left"></i></button>
                        <h5 class="modal-title mb-0" id="drillTitle">Faults</h5>
                        <span class="drill-period" id="drillPeriod"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="drillLoading" class="text-center text-muted py-5">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading faults…
                </div>
                <div id="drillError" class="alert alert-danger d-none"></div>
                <div id="drillContent" class="d-none">
                    <div class="drill-stats" id="drillStats"></div>
                    <div class="drill-bds" id="drillBreakdowns"></div>
                    <div class="drill-table-head">
                        <div class="drill-count" id="drillCount"></div>
                        <div class="drill-search">
                            <i class="fas fa-search"></i>
                            <input type="search" id="drillSearch" class="form-control form-control-sm" placeholder="Filter these faults…">
                        </div>
                    </div>
                    <div class="table-responsive drill-table-wrap">
                        <table class="table table-sm align-middle drill-table mb-0">
                            <thead>
                                <tr>
                                    <th>Ref</th><th>Customer</th><th>Link / location</th><th>Root cause</th><th>Status</th><th>Reported</th><th>Duration</th>
                                </tr>
                            </thead>
                            <tbody id="drillRows"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Click a root cause, customer or region to drill further. Click a reference to open it in Faults.</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="drillCsv"><i class="fas fa-file-csv me-1"></i>Download CSV</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="drillPdf"><i class="fas fa-file-pdf me-1"></i>Download PDF</button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
