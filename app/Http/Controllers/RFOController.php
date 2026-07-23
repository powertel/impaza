<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReasonsForOutage;
use DB;
use Illuminate\Support\Facades\Log;

class RFOController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:assessment-fault-list|assessment-fault-create|assessment-fault-edit|assessment-fault-delete', ['only' => ['index','store']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rfos = DB::table('reasons_for_outages')
        ->orderBy('reasons_for_outages.RFO', 'asc')
        ->get();

           return view('RFO.index', compact('rfos'))
                 ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rfo = ReasonsForOutage::all();
        return view('RFO.create',compact('rfo'));
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
            'RFO' => 'required|string|unique:reasons_for_outages',
        ]);

        try {
            $rfo = ReasonsForOutage::create([
                'RFO' => $request->input('RFO'),
            ]);

            return redirect()->route('rfos.index')
                ->with('success','RFO created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create RFO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'payload' => ['RFO' => $request->input('RFO')],
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create RFO.']);
        }
        
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ReasonsForOutage $rfo)
    {
        return view('RFO.edit',compact('rfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function update(Request $request, ReasonsForOutage $rfo)
    {
        Log::info('RFO update request received', [
            'rfo_id' => $rfo->id,
            'http_method' => $request->method(),
            'payload' => ['RFO' => $request->input('RFO')],
        ]);

        $validated = $request->validate([
            'RFO' => 'required|string|unique:reasons_for_outages,RFO,' . $rfo->id . ',id',
        ]);


        try {
            $rfo->update([
                'RFO' => $validated['RFO'],
            ]);

            Log::info('RFO update succeeded', [
                'rfo_id' => $rfo->id,
                'payload' => $validated,
            ]);

            return redirect()->route('rfos.index')
                ->with('success','RFO updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update RFO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'rfo_id' => $rfo->id,
                'payload' => ['RFO' => $request->input('RFO')],
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update RFO.']);
        }
    }
}
