<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use DB;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:material-list|material-create|material-edit|material-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:material-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:material-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:material-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        $q = trim((string) $request->input('q', ''));
        $category = trim((string) $request->input('category', 'all'));
        $stockFilter = trim((string) $request->input('stock', 'all'));

        $query = Material::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            });
        }

        $query->byCategory($category);

        if ($stockFilter === 'low') {
            $query->lowStock();
        } elseif ($stockFilter === 'out') {
            $query->where('quantity_on_hand', '<=', 0);
        } elseif ($stockFilter === 'active') {
            $query->active();
        }

        $materials = $query->orderBy('name', 'asc')->paginate($perPage);

        $categories = Material::whereNotNull('category')->distinct()->pluck('category')->sort()->values();

        $stats = [
            'total' => Material::count(),
            'active' => Material::active()->count(),
            'lowStock' => Material::lowStock()->count(),
            'outOfStock' => Material::where('quantity_on_hand', '<=', 0)->count(),
        ];

        return view('materials.index', compact('materials', 'categories', 'stats', 'q', 'category', 'stockFilter', 'perPage'));
    }

    public function create()
    {
        $categories = Material::whereNotNull('category')->distinct()->pluck('category')->sort()->values();
        $material = new Material();
        return view('materials.create_modal', compact('categories', 'material'))->render();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:materials,sku',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'quantity_on_hand' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['reorder_level'] = $validated['reorder_level'] ?? 0;

        Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material added to inventory successfully.');
    }

    public function show($id)
    {
        $material = Material::with('createdBy')->findOrFail($id);
        return view('materials.view_modal', compact('material'))->render();
    }

    public function edit($id)
    {
        $material = Material::findOrFail($id);
        $categories = Material::whereNotNull('category')->distinct()->pluck('category')->sort()->values();
        return view('materials.edit_modal', compact('material', 'categories'))->render();
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('materials', 'sku')->ignore($material->id),
            ],
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'quantity_on_hand' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['reorder_level'] = $validated['reorder_level'] ?? 0;

        $material->update($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material updated successfully.');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Material removed from inventory.');
    }

    public function ajaxList(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $query = Material::active()->orderBy('name', 'asc');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        $materials = $query->limit(50)->get([
            'id',
            'name',
            'sku',
            'category',
            'unit',
            'quantity_on_hand',
        ]);

        return response()->json($materials);
    }
}
