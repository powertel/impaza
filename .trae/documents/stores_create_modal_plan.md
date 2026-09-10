# Request Materials Modal — Select Dropdown + Add Row Fix Implementation Plan

## Repository Research

### Current State (broken as per user report)
**View**: [stores/create_modal.blade.php](file:///c:/Users/Freedom%20Jatakalula/impaza/resources/views/stores/create_modal.blade.php)

The modal has TWO columns the user circled:
1.  **"Or type custom (e.g. UTP Cable Cat6)" input below the select** — currently this is `input type=text` (row 62). The user wants it to be a **`<select>` option** dropdown, presumably showing all existing materials + a custom option combo, OR replacing the dual select+text with a single proper `<select>` dropdown.
2.  **"+ Add Row" button (row 39)** — currently has class `add-mat-row-btn` with `data-fault=` attribute.

### What actually exists currently (good news)
- **Select dropdown ALREADY EXISTS at line 49-61**: `<select class="mat-select" name="items[0][material_id]">`. It's populated via Blade `@foreach(($materials ?? collect()) as $m)` with each option carrying `data-name`, `data-unit`, `data-stock`.
- **A second text input `mat-name-input` exists at line 62**: `name="items[0][material_name]"` with placeholder "Or type custom...". This is the box the user actually circled.
- **The materials collection IS passed**:
  - From [MyFaultController@index line 109-111](file:///c:/Users/Freedom%20Jatakalula/impaza/app/Http/Controllers/MyFaultController.php#L109-L111): `Material::active()...->get(...)`
  - Into [my_faults/index.blade.php line 151](file:///c:/Users/Freedom%20Jatakalula/impaza/resources/views/my_faults/index.blade.php#L151): `@include('stores.create_modal', ... 'materials' => ($materials ?? collect()) ...)`
  - Also used inside [StoreController@createForFault](file:///c:/Users/Freedom%20Jatakalula/impaza/app/Http/Controllers/StoreController.php#L77-L86) for AJAX rendering.

### Root cause hypotheses for "Add Row should work"
H1. **Select + Add Row DOES work** in the static-template rows BUT the user's reported screenshot shows row 1 already filled (UTP Cable Cat5e selected) AND the Add Row button was never tested. Possible real issue: the dynamic `tpl.innerHTML` at lines 189-216 **duplicates the Blade literal `@foreach`** inside a JS template literal string which gets rendered to the browser verbatim — meaning every new dynamic row's `<select>` contains `<option>@foreach(($materials ?? collect()) as $m) ...` as LITERAL TEXT, not PHP-rendered options. After the first row, ALL subsequent dynamically-added rows have empty or broken selects. This is the bug.

H2. The "Or type custom" input *below* the select might be where the user actually types — they asked to convert this field into a proper select option instead of free text. Combined with H1, every row beyond row 1 has no populated select, so the user sees the "Or type custom" text box as the only material chooser, hence "this should be a select option".

## Files and Modules
- `resources/views/stores/create_modal.blade.php`: FULL REWRITE of JS template, row HTML structure, and select+custom input layout.
- (Optional, safety) `app/Http/Controllers/MyFaultController.php`: already passes `$materials`, no change needed.
- (Optional) `resources/views/faults/show.blade.php` (fault detail page): if it embeds stores.create_modal, ensure it passes materials too.

## Implementation Steps
1. **Step 1 — Fix the "Or type custom" field (make it a select):** Convert the dual "select + text input" layout into a single unified UX:
   - Keep the `<select class="mat-select">` with all existing material options (this IS the select dropdown that should be used).
   - Rename/repurpose the "Or type custom" row: change from a free-type `input[name=material_name]` text box into the SAME STYLE as the select visually, or merge it so that selecting a material from the dropdown AUTOFILLS both the `material_id` hidden AND `material_name` display field (the existing `change` handler at line 145 already does this → `nameInput.value = opt.dataset.name`).
   - Add `list attribute + datalist` fallback or custom free-entry `<option value="">Custom / Not in list → type name in box ↓</option>` as the default placeholder first option, with the text box now labeled clearly "Custom Material Name (fill in if not listed above)".

2. **Step 2 — Fix Add Row dynamic row rendering (the actual showstopper bug):**
   - The existing dynamic template literal at line 189 `tpl.innerHTML = \`... @foreach ... @endforeach ...\`` contains **Blade directives inside client JS**. This renders once on the server for row 0, but the `@foreach` is PHP — so when the browser renders the Add Row template, the literal string `@foreach(...)` becomes part of the HTML select, which is invalid. The dynamic rows get zero valid options.
   - Fix: In the Blade `@section('scripts')` (or inside the same script IIFE), render the options list **ONCE at page render time** into a JS string variable using `@json` Blade directive OR `JSON_encode(Material::active()...)`, then build each dynamic row's `<option>` by cloning from a hidden `<template id="matReqRowTpl">` element.
   - Cleanest approach: Use a native `<template>` HTML element at the top of the script with the `data-name/data-unit/data-stock` attributes properly rendered by Blade once, then `template.content.cloneNode(true)` in JS.

3. **Step 3 — Robustness & UX polish:**
   - On dynamic row `bindRow()`, re-apply the existing `mat-select change → set name/unit/stock hint` handlers correctly.
   - Ensure `renumber()` updates ALL form inputs including `items[N][material_id]`, `items[N][material_name]`, `items[N][unit]`, `items[N][quantity_requested]`, `items[N][remark]`.
   - Remove button on first row stays `disabled` until row count > 1 (existing logic; keep).
   - Add autofocus to qty input after a new row is inserted.

## Dependencies and Considerations
- `$materials` variable MUST be present — already injected by both MyFaultController@index (in 14 default SKUs + any user additions) and StoreController@createForFault. No DB change needed.
- StoreController@store validates using `items[*][material_name] + items[*][unit] + items[*][quantity_required]` — DO NOT rename these fields, keep the POST contract unchanged.
- Backward compatibility: existing valid submissions from the current row 0 flow must still work.
- Existing row must be visually consistent with the dynamic cloned rows (same markup, same classes) — keep card/card-body wrappers.
- The IIFE in create_modal.blade.php runs once PER FAULT MODAL (N times for N listed faults). All variables inside it (`faultId`, `rowIndex`, `container`, `addBtn`) are per-modal and scoped correctly because of the closure — so scoping is actually OK. Only the `innerHTML` of Add Row rows was broken, not the closure.

## Validation
- Visual check in browser at /my_faults: for any assigned fault, click "Request Material" → modal opens.
- Row 0: Select a material from dropdown → Material Name text box autofills with name, Unit autofills, Stock hint shows "In stock: X".
- Click **+ Add Row**: Verify (a) new row appears, (b) its `<select>` dropdown shows all 14 materials with SKUs and categories (not an empty list or `@foreach` literal), (c) selecting one of them autofills name/unit/stock on that NEW row, (d) Remove trash icon is now ENABLED on both rows, (e) click Add Row again → 3 rows OK, renumber visually.
- Submit a 2+ row request → check DB `material_request_items` count increased by N, each row has correct `material_id` FK (if chosen from list) or null (if custom name).
- Browser console: no JS errors during add/remove row operations.

## Risks
- **Risk: template content with Blade-escaped data** — `data-name` / `data-unit` attributes must be escaped via `{{ e($m->name) }}` (Laravel escapes by default in `{{ }}`). Mitigation: use standard `{{ }}` not `{!! !!}`.
- **Risk: Too many modals × too many DOM nodes** — the create_modal is included inside a `@foreach($faults)` loop so every fault row carries its own script IIFE. Move the reusable `<template id=matReqRowTpl-{{$fault->id}}>` to be fault-scoped (use fault ID suffix) OR global with fault-prefixed IDs.
- **Risk: form submit validation** — existing submit handler checks `name.value.trim() && qty > 0` per row. With the select, when user picks a material, `nameInput` gets auto-filled via JS change listener (existing code line 148). Risk if select changes but validation sees empty qty → prevent submit (correct behavior).
