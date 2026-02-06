<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Suburb;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    function __construct()
    {
         // Using similar permissions to cities for now, or we can assume new permissions are needed.
         // For now, I'll use 'technician-configuration' as it seems relevant to the user's request context
         // or generic CRUD permissions if they existed. 
         // Given the user context "On sidebar where they is configuration", let's use 'technician-configuration'
         // or maybe reuse 'department-list' or 'city-list' if lazy, but better to be safe.
         // Let's stick to standard auth for now and maybe 'technician-configuration' for access control.
         $this->middleware('permission:technician-configuration', ['only' => ['index','store','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zones = Zone::withCount('suburbs')->orderBy('name', 'asc')->get();
        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        
        // Fetch suburbs with their current zone info and region from city
        $suburbs = Suburb::join('cities', 'suburbs.city_id', '=', 'cities.id')
            ->leftJoin('zones', 'suburbs.zone_id', '=', 'zones.id')
            ->select('suburbs.id', 'suburbs.suburb', 'suburbs.zone_id', 'zones.name as zone_name', 'cities.region')
            ->orderBy('suburbs.suburb')
            ->get();

        return view('zones.index', compact('zones', 'regions', 'suburbs'))
            ->with('i');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:zones,name',
            'region' => 'nullable|string',
            'suburbs' => 'nullable|array',
            'suburbs.*' => 'exists:suburbs,id',
        ]);

        $zone = Zone::create($request->only('name', 'region'));

        if ($request->has('suburbs')) {
            // Update selected suburbs to belong to this zone
            Suburb::whereIn('id', $request->suburbs)->update(['zone_id' => $zone->id]);
        }

        return redirect()->route('zones.index')
            ->with('success', 'Zone Created Successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:zones,name,'.$id,
            'region' => 'nullable|string',
            'suburbs' => 'nullable|array',
            'suburbs.*' => 'exists:suburbs,id',
        ]);

        $zone = Zone::findOrFail($id);
        $zone->update($request->only('name', 'region'));

        // Handle suburbs assignment
        // 1. Dissociate suburbs that are currently in this zone but NOT in the submitted list
        // Note: If no suburbs submitted (empty array), all should be dissociated.
        // If 'suburbs' is not in request at all, do we assume no change or empty?
        // Usually checkboxes/selects send nothing if empty.
        // But if it's a nullable array, and user unselects all, it might send nothing or empty array.
        // Let's assume if it's present, we sync. If not present, we might assume no change?
        // But for a multi-select, usually we want to explicit sync.
        
        $newSuburbIds = $request->input('suburbs', []);
        
        // Remove from zone those not in the new list
        Suburb::where('zone_id', $zone->id)
            ->whereNotIn('id', $newSuburbIds)
            ->update(['zone_id' => null]);
            
        // Add to zone those in the new list
        if (!empty($newSuburbIds)) {
            Suburb::whereIn('id', $newSuburbIds)->update(['zone_id' => $zone->id]);
        }

        return redirect()->route('zones.index')
            ->with('success', 'Zone Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        
        // Release suburbs before deleting
        $zone->suburbs()->update(['zone_id' => null]);

        $zone->delete();
        return redirect()->route('zones.index')
            ->with('success', 'Zone Deleted Successfully');
    }
}
