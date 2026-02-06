<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Pop;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:technician-configuration', ['only' => ['index','store','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zones = Zone::withCount('pops')->orderBy('name', 'asc')->get();
        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        
        // Fetch pops with their current zone info and region from city
        $pops = Pop::join('cities', 'pops.city_id', '=', 'cities.id')
            ->leftJoin('zones', 'pops.zone_id', '=', 'zones.id')
            ->select('pops.id', 'pops.pop', 'pops.zone_id', 'zones.name as zone_name', 'cities.region')
            ->orderBy('pops.pop')
            ->get();

        return view('zones.index', compact('zones', 'regions', 'pops'))
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
            'pops' => 'nullable|array',
            'pops.*' => 'exists:pops,id',
        ]);

        $zone = Zone::create($request->only('name', 'region'));

        if ($request->has('pops')) {
            // Update selected pops to belong to this zone
            Pop::whereIn('id', $request->pops)->update(['zone_id' => $zone->id]);
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
            'pops' => 'nullable|array',
            'pops.*' => 'exists:pops,id',
        ]);

        $zone = Zone::findOrFail($id);
        $zone->update($request->only('name', 'region'));

        $newPopIds = $request->input('pops', []);
        
        // Remove from zone those not in the new list
        Pop::where('zone_id', $zone->id)
            ->whereNotIn('id', $newPopIds)
            ->update(['zone_id' => null]);
            
        // Add to zone those in the new list
        if (!empty($newPopIds)) {
            Pop::whereIn('id', $newPopIds)->update(['zone_id' => $zone->id]);
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
        
        // Release pops before deleting
        $zone->pops()->update(['zone_id' => null]);

        $zone->delete();
        return redirect()->route('zones.index')
            ->with('success', 'Zone Deleted Successfully');
    }
}
