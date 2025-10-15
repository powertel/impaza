<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Fault;
use App\Models\Remark;
use DB;

class RemarkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only(['store', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Fault $fault)
    {
        // Validate: attachment optional and limited to image types
        $validated = $request->validate([
            'remark' => 'required|string',
            'attachment' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'activity' => 'nullable|string',
            'url' => 'nullable|string'
        ]);

        // Resolve activity: prefer explicit, fallback based on URL context
        $activity = $validated['activity'] ?? null;
        if (!$activity) {
            $previous = $validated['url'] ?? '';
            $FAULT_EDIT = Str::contains($previous, 'faults');
            $CT_CLEAR   = Str::contains($previous, 'chief-tech-clear');
            $NOC_CLEAR  = Str::contains($previous, 'noc-clear');
            $REASSIGN   = Str::contains($previous, 'assign');
            if ($FAULT_EDIT)      { $activity = 'ON FAULT EDIT'; }
            elseif ($CT_CLEAR)    { $activity = 'ON CHIEF-TECH CLEAR'; }
            elseif ($NOC_CLEAR)   { $activity = 'ON NOC CLEAR'; }
            elseif ($REASSIGN)    { $activity = 'ON CHIEF-TECH REASSIGN'; }
        }

        // Store attachment if present
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->storePublicly('attachments', 'public');
        }

        // Map activity to remark_activities.id
        $remarkActivityId = null;
        if ($activity) {
            $remarkActivityId = DB::table('remark_activities')
                ->where('activity', '=', $activity)
                ->value('id');
        }

        // Create remark
        $remark = Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => $validated['remark'],
            'remarkActivity_id' => $remarkActivityId,
            'file_path' => $path,
        ]);

        // If this is an AJAX/JSON request, respond with the remark payload
        if ($request->expectsJson()) {
            $fullRemark = DB::table('remarks')
                ->leftJoin('remark_activities', 'remarks.remarkActivity_id', '=', 'remark_activities.id')
                ->leftJoin('users', 'remarks.user_id', '=', 'users.id')
                ->where('remarks.id', '=', $remark->id)
                ->select([
                    'remarks.id',
                    'remarks.fault_id',
                    'remarks.created_at',
                    'remarks.remark',
                    'remarks.file_path',
                    'users.name',
                    'remark_activities.activity'
                ])->first();

            return response()->json(['status' => 'ok', 'remark' => $fullRemark]);
        }

        // Legacy redirects (non-AJAX)
        $previous = $validated['url'] ?? '';
        $CT_CLEAR   = Str::contains($previous, 'chief-tech-clear');
        $NOC_CLEAR  = Str::contains($previous, 'noc-clear');
        $REASSIGN   = Str::contains($previous, 'assign');
        if ($CT_CLEAR) {
            return redirect(route('chief-tech-clear.index'));
        } elseif ($NOC_CLEAR) {
            return redirect(route('noc-clear.index'));
        } elseif ($REASSIGN) {
            return redirect(route('assign.index'));
        } else {
            return back();
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
    public function edit($id)
    {
        //
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
        //
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
