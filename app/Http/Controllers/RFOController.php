<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReasonsForOutage;
use DB;

class RFOController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:assessment-fault-list|assessment-fault-create|assessment-fault-edit|assessment-fault-delete', ['only' => ['index','store']]);
         $this->middleware('permission:assessment-fault-create', ['only' => ['create','store']]);
         $this->middleware('permission:assessment-fault-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:assessment-fault-delete', ['only' => ['destroy']]);
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


        DB::beginTransaction();
        try{

            request()->validate([
    
                'RFO' => 'required|string|unique:reasons_for_outages',
            ]);

            $rfo =  ReasonsForOutage::create(
                [
                    'RFO' => $request['RFO'],
                ]
            );
           
            
            if($rfo)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('rfos.index')
            ->with('success','assessment-fault created successfully.');
        }

        catch(Exception $ex)
        {
            DB::rollback();
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
    public function update(Request $request, ReasonsForOutage $rfo)
    {
        $request->validate([
            'RFO' => 'required|string|unique:reasons_for_outages,RFO,'.$rfo->id,
        ]);

        DB::beginTransaction();
        try {
            $rfo->update([
                'RFO' => $request->input('RFO'),
            ]);

            DB::commit();
            return redirect()->route('rfos.index')
                ->with('success','assessment-fault updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update assessment-fault.']);
        }
    }


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
}
