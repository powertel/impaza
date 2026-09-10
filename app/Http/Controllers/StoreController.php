<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Store;
use App\Models\Fault;
use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use Carbon\Carbon;

class StoreController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:stores-list|stores-process|materials', ['only' => ['index', 'show', 'requests']]);
        $this->middleware('permission:stores-process', ['only' => ['process', 'issue', 'updateItem']]);
    }

    public function index(Request $request)
    {
        return $this->requests($request);
    }

    public function requests(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        $q = trim((string) $request->input('q', ''));
        $statusFilter = trim((string) $request->input('status', 'pending'));

        $query = MaterialRequest::with([
            'fault',
            'fault.customer',
            'requestedBy',
            'processedBy',
            'items',
        ]);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('request_number', 'like', "%{$q}%")
                    ->orWhereHas('fault', function ($fq) use ($q) {
                        $fq->where('fault_ref_number', 'like', "%{$q}%");
                    })
                    ->orWhereHas('requestedBy', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($statusFilter === 'pending') {
            $query->pending();
        } elseif ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $materialRequests = $query->orderByDesc('created_at')->paginate($perPage);

        $stats = [
            'pending' => MaterialRequest::pending()->count(),
            'issued' => MaterialRequest::where('status', MaterialRequest::STATUS_ISSUED)->count(),
            'partial' => MaterialRequest::where('status', MaterialRequest::STATUS_PARTIAL)->count(),
            'total' => MaterialRequest::count(),
        ];

        return view('stores.requests_index', compact('materialRequests', 'stats', 'q', 'statusFilter', 'perPage'));
    }

    public function create()
    {
        return redirect()->route('materials.index');
    }

    public function createForFault(Fault $fault)
    {
        $materials = Material::active()->orderBy('name')->get([
            'id', 'name', 'sku', 'category', 'unit', 'quantity_on_hand',
        ]);

        $pendingRequests = $fault->materialRequests()->with('items')->pending()->get();

        return view('stores.create_modal', compact('fault', 'materials', 'pendingRequests'))->render();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fault_id' => 'required|exists:faults,id',
            'technician_note' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'nullable|exists:materials,id',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity_requested' => 'required|numeric|min:0.01',
            'items.*.remark' => 'nullable|string|max:500',
        ]);

        $fault = Fault::findOrFail($validated['fault_id']);

        DB::transaction(function () use ($validated, $fault, $request) {
            $mr = MaterialRequest::create([
                'request_number' => MaterialRequest::generateRequestNumber(),
                'fault_id' => $fault->id,
                'requested_by' => auth()->id(),
                'technician_note' => $validated['technician_note'],
                'status' => MaterialRequest::STATUS_PENDING,
                'submitted_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                if (empty($item['material_name']) || empty($item['quantity_requested'])) {
                    continue;
                }
                $mr->items()->create([
                    'material_id' => $item['material_id'] ?? null,
                    'material_name' => $item['material_name'],
                    'unit' => $item['unit'] ?? 'pcs',
                    'quantity_requested' => (float) $item['quantity_requested'],
                    'remark' => $item['remark'] ?? null,
                ]);
            }
        });

        $back = url()->previous();
        if (str_contains($back, 'my_faults')) {
            return redirect()->route('my_faults.index')
                ->with('success', 'Material request submitted successfully. It has been sent to Stores.');
        }
        return redirect()->route('faults.show', $fault->id)
            ->with('success', 'Material request submitted successfully. It has been sent to Stores.');
    }

    public function show($id)
    {
        $mr = MaterialRequest::with([
            'fault',
            'fault.customer',
            'fault.city',
            'fault.suburb',
            'fault.link',
            'fault.pop',
            'requestedBy',
            'processedBy',
            'items',
            'items.material',
        ])->findOrFail($id);

        return view('stores.show_modal', compact('mr'))->render();
    }

    public function issue($id)
    {
        $mr = MaterialRequest::with([
            'fault',
            'fault.customer',
            'fault.city',
            'fault.suburb',
            'fault.link',
            'fault.pop',
            'requestedBy',
            'items',
            'items.material',
        ])->findOrFail($id);

        return view('stores.issue', compact('mr'));
    }

    public function process(Request $request, $id)
    {
        $mr = MaterialRequest::findOrFail($id);

        $validated = $request->validate([
            'stores_note' => 'nullable|string|max:2000',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:material_request_items,id',
            'items.*.issued' => 'nullable|boolean',
            'items.*.available' => 'nullable|boolean',
            'items.*.quantity_issued' => 'nullable|numeric|min:0',
            'items.*.remark' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $mr) {
            $allIssued = true;
            $anyIssued = false;
            $anyUnavailable = false;

            foreach ($validated['items'] as $itemData) {
                $item = MaterialRequestItem::where('id', $itemData['id'])
                    ->where('material_request_id', $mr->id)
                    ->firstOrFail();

                $issued = !empty($itemData['issued']);
                $available = !isset($itemData['available']) || !empty($itemData['available']);

                $qtyIssued = 0;
                if ($issued && $available) {
                    $qtyIssued = isset($itemData['quantity_issued']) && (float) $itemData['quantity_issued'] > 0
                        ? (float) $itemData['quantity_issued']
                        : (float) $item->quantity_requested;
                    $anyIssued = true;
                }

                if (!$available) {
                    $anyUnavailable = true;
                }

                $item->update([
                    'quantity_issued' => $qtyIssued,
                    'is_available' => $available,
                    'remark' => $itemData['remark'] ?? $item->remark,
                ]);

                if (!$item->isFullyIssued()) {
                    $allIssued = false;
                }

                if ($issued && $available && $item->material_id) {
                    $mat = Material::find($item->material_id);
                    if ($mat) {
                        $mat->decrement('quantity_on_hand', $qtyIssued);
                    }
                }
            }

            $status = MaterialRequest::STATUS_PROCESSING;
            if ($allIssued && !$anyUnavailable) {
                $status = MaterialRequest::STATUS_ISSUED;
            } elseif ($anyIssued || $anyUnavailable) {
                $status = MaterialRequest::STATUS_PARTIAL;
            }

            $mr->update([
                'stores_note' => $validated['stores_note'],
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'status' => $status,
            ]);
        });

        return redirect()->route('stores.requests')
            ->with('success', "Material request {$mr->request_number} processed.");
    }

    public function updateItem(Request $request, $id)
    {
        $item = MaterialRequestItem::findOrFail($id);
        $mr = $item->materialRequest;

        $validated = $request->validate([
            'quantity_issued' => 'nullable|numeric|min:0',
            'is_available' => 'nullable|boolean',
            'remark' => 'nullable|string|max:500',
        ]);

        $item->update($validated);

        return back()->with('success', 'Item updated.');
    }

    public function destroy($id)
    {
        $mr = MaterialRequest::findOrFail($id);
        if (!$mr->isPending() && !auth()->user()->can('stores-process')) {
            abort(403);
        }
        $mr->delete();
        return back()->with('success', 'Material request deleted.');
    }

    public function findstores($id)
    {
        $fault = Fault::with(['materialRequests' => function ($q) {
            $q->with('items')->latest();
        }])->findOrFail($id);

        return response()->json($fault->materialRequests);
    }
}
