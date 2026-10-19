<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 50);
        $perPage = in_array($perPage, [10, 20, 50, 100, 200]) ? $perPage : 50;
        $q = trim((string) $request->input('q', ''));

        $query = $this->permission::query();

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $permissions = $query->orderBy('name', 'asc')->paginate($perPage);

        $permissionCount = $this->permission::count();
        $permissionGroups = $this->permission::all()->groupBy(function ($p) {
            return explode('-', $p->name)[0] ?? 'general';
        })->count();
        $recentPermissions = $this->permission::where('created_at', '>=', now()->subDays(30))->count();

        return view('permission.index', compact(
            'permissions',
            'permissionCount',
            'permissionGroups',
            'recentPermissions',
            'q',
            'perPage'
        ));
    }

    public function create()
    {
        return view('permission.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:125|unique:permissions,name',
            'guard_name' => 'nullable|string|max:125',
        ]);

        $this->permission::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);

        return redirect()->route('permission.index')
            ->with('success', "Permission '{$validated['name']}' created successfully.");
    }

    public function storeBulk(Request $request)
    {
        $data = $request->validate([
            'permissions' => 'required|array|min:1',
            'permissions.*.name' => 'required|string|max:125',
            'permissions.*.guard_name' => 'nullable|string|max:125',
        ]);

        $created = [];
        $skipped = [];

        foreach ($data['permissions'] as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $guard = $row['guard_name'] ?? 'web';
            $exists = $this->permission::where('name', $name)->where('guard_name', $guard)->exists();
            if ($exists) {
                $skipped[] = $name;
                continue;
            }
            $this->permission::create(['name' => $name, 'guard_name' => $guard]);
            $created[] = $name;
        }

        $msg = count($created) . ' permission(s) created.';
        if (count($skipped)) $msg .= ' ' . count($skipped) . ' skipped (already exist).';

        return redirect()->route('permission.index')
            ->with('success', $msg)
            ->with('skipped', $skipped)
            ->with('created', $created);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $permission = $this->permission::findOrFail($id);
        return view('permission.edit_modal', compact('permission'))->render();
    }

    public function update(Request $request, $id)
    {
        $permission = $this->permission::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:125',
                \Illuminate\Validation\Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'guard_name' => 'required|string|max:125',
        ]);

        $oldName = $permission->name;
        $permission->update([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'],
        ]);

        return redirect()->route('permission.index')
            ->with('success', "Permission updated: '{$oldName}' → '{$validated['name']}'.");
    }

    public function destroy($id)
    {
        $permission = $this->permission::findOrFail($id);
        $name = $permission->name;
        $permission->delete();

        return redirect()->route('permission.index')
            ->with('success', "Permission '{$name}' deleted.");
    }

    public function getAllPermissions()
    {
        $permissions = $this->permission->all();
        return response()->json(['permissions' => $permissions], 200);
    }

    public function getAll()
    {
        $permissions = $this->permission->all();
        return response()->json(['permissions' => $permissions], 200);
    }
}
