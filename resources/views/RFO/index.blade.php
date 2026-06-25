@extends('layouts.admin')

@section('title')
RFO
@endsection

@include('partials.css')

@section('content')

<section class="content workflow-faults-page" >
<div class="card faults-panel" >
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Reasons For Outage</h3>
            <div class="faults-panel-subtitle">Maintain the outage reason library with the same modern table and modal workspace used across the faults module.</div>
        </div>
        <div class="faults-panel-actions">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createRfoModal">
                <i class="fas fa-plus-circle me-1"></i>Create RFO
            </button>
        </div>
    </div>
    <div class="faults-toolbar">
        <div class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="rfosPageSize" class="form-select form-select-sm" aria-label="Rows per page">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="rfosSearch" class="form-control" placeholder="Search outage reasons...">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="rfosSearchTrigger">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="rfosReset">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>
    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" data-page-size="20" data-page-size-control="#rfosPageSize" data-pager="#rfosPager" data-search="#rfosSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Reason For Outage</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rfos as $rfo)
                    <tr>
                        <td data-label="No.">{{ ++$i }}</td>
                        <td data-label="Reason For Outage">
                            <div class="faults-cell-main">{{ $rfo->RFO }}</div>
                        </td>
                        <td data-label="Action(s)">
                            <div class="faults-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRfoModal{{ $rfo->id }}">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                    @include('RFO.edit', ['rfo' => $rfo])
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing the current outage reason library</small>
            <div id="rfosPager"></div>
        </div>
        @include('RFO.create')
    </div>
</div>


</section>

@endsection

@section('scripts')
    @include('partials.scripts')
    <script>
        document.getElementById('rfosSearchTrigger')?.addEventListener('click', function () {
            const input = document.getElementById('rfosSearch');
            if (!input) return;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.focus();
        });
        document.getElementById('rfosReset')?.addEventListener('click', function () {
            const input = document.getElementById('rfosSearch');
            const perPage = document.getElementById('rfosPageSize');
            if (input) {
                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (perPage) {
                perPage.value = '20';
                perPage.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    </script>
@endsection
