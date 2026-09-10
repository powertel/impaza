# My Faults Actions — 3-Dot Dropdown + MR-State Conditional Items Implementation Plan

## Repository Research

### Current state
- **View:** `resources/views/my_faults/index.blade.php` Action(s) column `L83-L126` renders **7 inline buttons** (Clear / In Progress / Rectify / Request Permit / Request Material / Escalate / View) when `$fault->description === 'Fault is under Rectification'`. On small/medium viewports this spills into 2 rows, the mess red-circled in the user's screenshot.
- **Controller:** `app/Http/Controllers/MyFaultController.php:index()` fetches `$faults` via raw `DB::table('faults')->...->get(...)` → returns **STDCLASS objects (not Fault Eloquent models)**. This means `$fault->materialRequests()` relation CANNOT be called in the Blade template. MR state for each fault MUST be pre-fetched separately in the controller as a keyed collection `$latestMrByFault[(int)$fault->id] = $mrModel | null`.
- **MR Lifecycle:** `app/Models/MaterialRequest.php:STATUS_*` constants + `scopePending/scopeIssued`:
  - STATUS_PENDING (`pending`), STATUS_PROCESSING (`processing`) → "Editable, not yet processed"
  - STATUS_PARTIAL (`partial`) → "Partially processed, still awaiting stock for some lines — still editable from tech side"
  - STATUS_ISSUED (`issued`) → "Fully processed by Stores — read-only, show what was requested & issued"
  - STATUS_CANCELLED (`cancelled`) → "Treat as NONE — ignore, allow creating a new request"
- **MR relation:** `Fault.php:101 materialRequests()` = `hasMany(MaterialRequest::class)`. A single fault can have many MRs historically, but only the latest non-cancelled MR matters for the tech action.
- **Existing dropdown pattern to reuse:** `resources/views/finance/index.blade.php:L123-L129` — `btn-group` + `data-bs-toggle="dropdown"` + `fa-ellipsis-v Actions` toggle + `<ul class="dropdown-menu dropdown-menu-end shadow p-2">` + items as `<li><a class="dropdown-item d-flex align-items-center gap-2"><i class="fas fa-..."></i>Label</a></li>` + `<li><hr class="dropdown-divider"></li>` separators between logical groups.
- **Existing MR view modal (read-only):** `resources/views/stores/show_modal.blade.php` — already fully built, expects a `$mr` MaterialRequest model instance; renders Fault info, timeline, Items table (requested vs issued qty, stock statuses, etc.). IDs `id="viewMRModal-{{ $mr->id }}"`.
- **Existing MR create modal (edit-mode-capable):** `resources/views/stores/create_modal.blade.php` — `@include('stores.create_modal', [ 'fault' => $fault, 'pendingRequests' => collect(), ... ])`. Currently `pendingRequests` always passed as empty `collect()`. This modal is idempotent — it has form rows, validates, POSTs to `stores.store`. We just need to:
  1. Pass the actual latest pending MR as `pendingRequest` (singular) when one exists
  2. Inside `create_modal.blade.php` detect edit-mode (when `isset($pendingRequest)` and `$pendingRequest` is a MR in pending/processing/partial state)
  3. In edit mode: fill `<form>` `action` to `POST` to `stores.update route instead of stores.store OR (simpler) just DELETE the existing MR + items BEFORE POSTing the new replacement (soft-save via single replace action; or better still, implement an update endpoint)

### Constraint
`StoreController@store` is the POST target for new material requests — there is currently no `PUT/PATCH stores/{id}/update` route/controller action. For the simplest safe edit-mode flow (no backend route changes): when submitting an edit-modal form, use the same `store` route but pass an extra hidden input `_replace_mr_id = $pendingRequest->id`. In `StoreController::store()`, detect this hidden id, then in a single DB txn: `MaterialRequest::find($id)->items()->delete(); MaterialRequest::find($id)->delete();` then proceed as normal to create a fresh MR on the same fault — effectively a replace. This keeps validation & MR numbering unchanged.

## Files and Modules

1. **`app/Http/Controllers/MyFaultController.php:index(L34-L137)`** — Add `$latestMrByFault` query.
2. **`resources/views/my_faults/index.blade.php:Action(s) column (L83-L126) + @foreach modal includes (L145-L160)`** — Replace 7 inline buttons with 3-dot dropdown; add MR-state-conditional actions; pass `pendingRequest` to create_modal; conditionally include `stores.show_modal` for issued MRs.
3. **`resources/views/stores/create_modal.blade.php`** — Add edit-mode detection: when `$editingMr ?? null` is set, pre-fill the form with existing rows/notes, add hidden `_replace_mr_id`, change modal submit button label from "Submit Request to Stores" → "Update Material Request", change modal header text accordingly, adjust ID if needed.
4. **`app/Http/Controllers/StoreController.php:store()`** — Add replace-MR handling: if `$request->filled('_replace_mr_id')`, find MR, verify ownership/same fault, in txn delete items + delete MR then continue normal insert.

## Implementation Steps

### Step 1 — MyFaultController::index inject latest MR per fault
In `app/Http/Controllers/MyFaultController.php:index()` after L111 (`$materials = ...`):
```php
// Latest non-cancelled MR keyed by fault_id for action-state logic + modals
$latestMrByFault = [];
if (!empty($faultIdsList)) {
    $mrs = \App\Models\MaterialRequest::query()
        ->whereIn('fault_id', $faultIdsList)
        ->where('status', '!=', \App\Models\MaterialRequest::STATUS_CANCELLED)
        ->with(['items'])
        ->orderByDesc('created_at')
        ->get();
    $seen = [];
    foreach ($mrs as $mr) {
        if (isset($seen[$mr->fault_id])) continue;
        $seen[$mr->fault_id] = true;
        $latestMrByFault[(int)$mr->fault_id] = $mr;
    }
}
```
Add `'latestMrByFault' => $latestMrByFault` to `view(... compact(...))` at L135.

### Step 2 — create_modal.blade.php Edit-mode support
In `resources/views/stores/create_modal.blade.php`:
- Top of file: `@php $editingMr = $editingMr ?? null; @endphp`
- `<form>` `action`: still `route('stores.store')`. Add hidden field `<input type="hidden" name="_replace_mr_id" value="{{ $editingMr?->id }}">` when editing.
- Modal title: `@if($editingMr) Update Material Request @else Request Materials @endif`
- Modal "Requesting for PWT... info banner": stays same; plus show inline status badge of MR being edited (Pending badge) if editing.
- Note (technician_note textarea): `old('technician_note', $editingMr?->technician_note ?? '')`
- Static row 0 + row template should render: if editing and `$editingMr->items` has N rows, first N static rows pre-fill values. But given our existing Add Row JS works well and row0 is always static, the simplest approach is: IF EDITING, write a @php block that generates an array N = max(1, count($editingMr->items)) and renders static rows 0..N-1 (instead of just row 0), each with data from $editingMr->items[i]. Material selected = selected attr on option matching items[i]->material_id. Name = items[i]->material_name. Unit = items[i]->unit. Qty = items[i]->quantity_requested. Remark = items[i]->remark. This preserves existing rows 1..N-1 without needing JS to add rows on load.
- Submit button label: `@if($editingMr) <i class="fas fa-save me-1"></i>Update Request @else ... original Submit Request to Stores @endif`
- Keep all IDs as-is (modal id still `requestMaterialCreateModal-{{ $fault->id }}`). OK for edit vs create since only one modal per fault and latest state is always the active one.

### Step 3 — StoreController::store Add _replace_mr_id clean-slate logic
In `app/Http/Controllers/StoreController.php` `store()` method at the top (after validation, before the MR create transaction):
```php
if ($request->filled('_replace_mr_id')) {
    $replaceId = (int) $request->input('_replace_mr_id');
    $replaceMr = \App\Models\MaterialRequest::with('items')->findOrFail($replaceId);
    // Security: only allow replacing if MR is still pending/processing/partial AND same fault
    abort_if(!$replaceMr->isPending() && $replaceMr->status !== \App\Models\MaterialRequest::STATUS_PARTIAL, 403);
    abort_if((int)$replaceMr->fault_id !== (int)$fault->id, 403);
    DB::beginTransaction();
    try {
        $replaceMr->items()->delete();
        $replaceMr->delete();
        DB::commit();
    } catch (\Throwable $e) { DB::rollBack(); throw $e; }
}
```
Then the same `store()` logic continues and creates a fresh MR — which auto-generates the auto MR number correctly. This guarantees atomic replace behavior without implementing a full update controller action with patch diff logic.

### Step 4 — My Faults index Action(s) column: 3-dot Dropdown
In `resources/views/my_faults/index.blade.php` L83-L126 (the entire Action(s) cell):

Replace the `.faults-actions` div (7 inline buttons + View) with:
```html
<td data-label="Action(s)" class="text-end">
  <div class="d-inline-flex flex-column align-items-end gap-2">
    {{-- Primary standalone View button (always visible) --}}
    <button class="btn btn-sm btn-outline-success rounded-pill" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
      <i class="fas fa-eye me-1"></i>View
    </button>
    @if ($fault->description==='Fault is under Rectification')
    <div class="btn-group dropstart">
      <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
        <i class="fas fa-ellipsis-v me-1"></i>Actions
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow p-2" style="min-width: 15rem;">
        {{-- Workflow group (Clear / InProgress / Rectify / Escalate) --}}
        @can('noc-clear-faults-clear')
        <li>
          <button class="dropdown-item d-flex align-items-center gap-2 text-primary" data-bs-toggle="modal" data-bs-target="#nocClearModal-{{ $fault->id }}">
            <i class="fas fa-check-circle"></i><span>Clear Fault</span>
          </button>
        </li>
        <li>
          <button class="dropdown-item d-flex align-items-center gap-2 text-success" data-bs-toggle="modal" data-bs-target="#inProgressModal-{{ $fault->id }}">
            <i class="fas fa-play-circle"></i><span>Mark In Progress</span>
          </button>
        </li>
        @endcan
        @can('rectify-fault')
        <li>
          <button class="dropdown-item d-flex align-items-center gap-2 text-indigo" data-bs-toggle="modal" data-bs-target="#rectifyEditModal-{{ $fault->id }}" style="color:#4F46E5;">
            <i class="fas fa-wrench"></i><span>Rectify</span>
          </button>
        </li>
        @endcan
        <li>
          <button class="dropdown-item d-flex align-items-center gap-2 text-danger" data-bs-toggle="modal" data-bs-target="#escalateModal-{{ $fault->id }}">
            <i class="fas fa-level-up-alt"></i><span>Escalate to Chief Tech</span>
          </button>
        </li>
        <li><hr class="dropdown-divider"></li>
        {{-- Permits group --}}
        @can('request-permit')
        <li>
          <button class="dropdown-item d-flex align-items-center gap-2 text-warning" data-bs-toggle="modal" data-bs-target="#requestPermitEditModal-{{ $fault->id }}" style="color:#D97706;">
            <i class="fas fa-file-signature"></i><span>Request Permit</span>
          </button>
        </li>
        <li><hr class="dropdown-divider"></li>
        @endcan
        {{-- Material Requests group — state-conditional --}}
        @php
            $mr = $latestMrByFault[(int)$fault->id] ?? null;
            $mrEditable = $mr && ($mr->isPending() || $mr->status === \App\Models\MaterialRequest::STATUS_PARTIAL);
            $mrIssued = $mr && $mr->isIssued();
        @endphp
        @can('request-material')
            @if($mrIssued)
            <li>
              <button class="dropdown-item d-flex align-items-center gap-2 text-muted" data-bs-toggle="modal" data-bs-target="#viewMRModal-{{ $mr->id }}">
                <i class="fas fa-box-circle-check"></i><span>Materials Requested (Issued)</span>
              </button>
            </li>
            @elseif($mrEditable)
            <li>
              <button class="dropdown-item d-flex align-items-center gap-2" style="color:#4F46E5;" data-bs-toggle="modal" data-bs-target="#requestMaterialCreateModal-{{ $fault->id }}">
                <i class="fas fa-pen-to-square"></i><span>Edit Material Request <span class="badge bg-light text-dark ms-1 border">{{ $mr->request_number }}</span></span>
              </button>
            </li>
            @else
            <li>
              <button class="dropdown-item d-flex align-items-center gap-2" style="color:#4F46E5;" data-bs-toggle="modal" data-bs-target="#requestMaterialCreateModal-{{ $fault->id }}">
                <i class="fas fa-box-plus"></i><span>Request Material</span>
              </button>
            </li>
            @endif
        @endcan
      </ul>
    </div>
    @endif
  </div>
</td>
```

Then in the `@foreach ($faults as $fault) @include ...` chain (L145-L160):
- Replace current `stores.create_modal` include line so `editingMr` is passed conditionally:
```php
@include('stores.create_modal', [
    'fault' => $fault,
    'materials' => ($materials ?? collect()),
    'editingMr' => (function($f) use($latestMrByFault){
        $mr = $latestMrByFault[(int)$f->id] ?? null;
        return $mr && ($mr->isPending() || $mr->status === \App\Models\MaterialRequest::STATUS_PARTIAL) ? $mr : null;
    })($fault),
])
```
- Add, after the existing create_modal include — conditionally include `stores.show_modal` IF issued MR exists (because viewMRModal-id needs to exist for the menu item target):
```php
@php
    $mrIssued = $latestMrByFault[(int)$fault->id] ?? null;
    if ($mrIssued && $mrIssued->isIssued()) {
        echo \Illuminate\Support\Facades\Blade::render(view('stores.show_modal', ['mr' => $mrIssued]));
    }
@endphp
```
Alternatively a clean `@includeIf(...)` or simpler: if we wrap it in `@if(condition) @include @endif`.

### Step 5 — `StoreController.php store()` update replace logic
Apply the `_replace_mr_id` delete-on-edit logic (described in Step 3).

### Step 6 — View:clear & cache:clear + view verify
Run `artisan view:clear cache:clear` inside container. Open `/my_faults` & test all 3 MR states:
- **No MR on fault**: dropdown → "Request Material" (opens empty create modal)
- **Pending MR (MR-0001 / created but not processed)**: dropdown → "Edit Material Request (MR-20260910-0001)" (opens modal pre-filled with existing 3 rows + note; submits replace transaction → new MR created, old destroyed)
- **Issued MR (MR fully processed)**: dropdown → "Materials Requested (Issued)" read-only view, opens stores.show_modal

## Dependencies and Considerations
- **NOT an Eloquent $faults collection:** Controller's `$faults` is built from DB::table() — stdClass objects — so any `$fault->method()` call would fail; we correctly pre-compute `$latestMrByFault` in controller using actual MR Eloquent, and pass the id-keyed array into blade. Correct.
- **`_replace_mr_id` hidden field + delete-then-create strategy:** Simpler than full PUT/PATCH; no new routes/controllers. MR auto-number resets naturally because the new MR will get a fresh number for today. Old MR hard-deleted so history is wiped only for pending (not issued) edits. If we wanted preservation, soft-deletes on MaterialRequest would be better but that is a separate bigger change not in scope. Since Pending/Partial MRs are not yet actioned by stores, hard delete is acceptable.
- **Dropstart vs Dropdown-end:** The actions cell is right-aligned; `btn-group dropstart` + `dropdown-menu dropdown-menu-end` prevents the menu from overflowing the right edge of the viewport (Finance index uses dropdown-toggle left side with end; my_faults row is right-most column so dropstart is cleaner).
- **Font-awesome icons used:** `fa-ellipsis-v` toggle, `fa-eye`, `fa-check-circle`, `fa-play-circle`, `fa-wrench`, `fa-level-up-alt`, `fa-file-signature`, `fa-box-plus`, `fa-pen-to-square`, `fa-box-circle-check`. All are solid free icons in FontAwesome 6.x.
- **Bootstrap 5 dropdowns:** Requires `popper.js` + `bootstrap.bundle.js` which AdminLTE already ships.

## Validation
1. **Visual & HTML output check:** Open /my_faults after view:clear; inspect action cells — confirm 3-dot `Actions` dropdown menu renders with grouped items, dividers.
2. **State test with fault #2 (PWT260414001) — currently has PENDING MR-20260910-0001:** Dropdown → "Edit Material Request (MR-20260910-0001)". Click → modal opens, row pre-filled with UTP/RJ45/Heatshrink 3 items; verify title is "Update Material Request", btn is "Update Request". Submit → old MR deleted, new MR created (MR-20260910-0002), back to same page.
3. **Create brand-new fault (with technician permission only — no MR):** Dropdown → "Request Material" (opens blank modal with 1 row).
4. **Issued MR state test:** Go to MR-20260909-0001 (fault #2's original issued MR, set it to linked fault #3 OR create new MR through stores-process flow and mark full issued). Dropdown → "Materials Requested (Issued)" → opens showMRModal with issued items, qty issued correct.
5. **Functional regression check:** Clear / In Progress / Rectify / Request Permit / Escalate menu items all still open their modals (same behavior as old inline buttons).
6. **Run diagnostics:** `artisan route:clear`, `artisan view:clear` — ensure everything compiles and no undefined key errors.

## Risks
- **Risk:** Empty $faultIdsList edge case — all `@foreach $faults empty`. Mitigation: wrap `latestMrByFault` computation with `if (!empty($faultIdsList))` (already in plan).
- **Risk:** Escalate / Permit modal IDs missing. Mitigation: Modal IDs are based on existing `-{{$fault->id}}` suffix pattern — NOT changed — so all existing modal includes still match.
- **Risk:** Replace-MR transaction could fail mid-flight (delete old MR but fail to create new MR). Mitigation: Wrap the ENTIRE store() method's existing MR-insert + delete-old logic inside a SINGLE outer `DB::beginTransaction()` before any writes, `DB::commit()` only after the new MR is fully created. Current store method probably has its own txn but wrapping the whole method ensures atomicity.
- **Risk:** Edit-mode static row render quantity mismatches items count (items empty array or null). Mitigation: Default to `max(1, count($items))` rows — never less than 1 row because Add-Row/Remove-Button logic already disables delete on single row.
