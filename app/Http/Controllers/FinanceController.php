<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use DB;

class FinanceController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:finance|finance-link-update', ['only' => ['index','store']]);
         $this->middleware('permission:finance-link-update', ['only' => ['edit','update']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q',''));
        $statusId = request('status');

        $query = DB::table('links')
            ->leftjoin('customers','links.customer_id','=','customers.id')
            ->leftjoin('cities','links.city_id','=','cities.id')
            ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
            ->leftjoin('pops','links.pop_id','=','pops.id')
            ->leftjoin('link_statuses','links.link_status','=','link_statuses.id')
            ->orderBy('customers.customer', 'asc')
            ->select(['links.id','links.link','links.contract_number','customers.customer','cities.city','pops.pop','suburbs.suburb','link_statuses.link_status']);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('customers.customer', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('links.contract_number', 'like', $like);
            });
        }

        if (!empty($statusId) && $statusId !== 'all') {
            $query->where('links.link_status', '=', (int) $statusId);
        }

        $finance_links = $query->paginate($perPage)->withQueryString();
        $linkStatuses = DB::table('link_statuses')->orderBy('id')->get();
        $statusCounts = DB::table('links')
            ->select('link_status', DB::raw('count(*) as total'))
            ->groupBy('link_status')
            ->pluck('total', 'link_status');
        $totalLinks = DB::table('links')->count();

        return view('finance.index',compact('finance_links','linkStatuses','statusCounts','totalLinks'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $link = DB::table('links')
                ->leftjoin('customers','links.customer_id','=','customers.id')
                ->leftjoin('cities','links.city_id','=','cities.id')
                ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
                ->leftjoin('pops','links.pop_id','=','pops.id')
                ->where('links.id','=',$id)
                ->get(['links.id','links.link','links.contract_number','customers.customer','cities.city','pops.pop','suburbs.suburb'])
                ->first();
        return view('finance.show',compact('link'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $link = DB::table('links')
        ->leftjoin('customers','links.customer_id','=','customers.id')
        ->leftjoin('cities','links.city_id','=','cities.id')
        ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
        ->leftjoin('pops','links.pop_id','=','pops.id')
        ->where('links.id','=',$id)
        ->get(['links.id','links.link','links.contract_number','links.customer_id','links.city_id','links.pop_id','links.suburb_id','customers.customer','cities.city','pops.pop','suburbs.suburb'])
        ->first();
        $customers = Customer::all();
        $cities = City::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        
        return view('finance.edit',compact('link','customers','cities','suburbs','pops',));
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
        request()->validate([
            'contract_number'=> 'required',
        ]);

        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 2;
        $link ->update($req);
        DB::table('audits')->insert([
            'entity_type' => 'link',
            'entity_id' => $link->id,
            'action' => 'link_connect',
            'user_id' => optional($request->user())->id,
            'notes' => 'Link connected',
            'created_at' => now(),
        ]);
        return redirect(route('finance.index'))
        ->with('success','Link Connected');
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

    public function disconnect(Request $request, $id)
    {

        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 3;
        $link ->update($req);
        DB::table('audits')->insert([
            'entity_type' => 'link',
            'entity_id' => $link->id,
            'action' => 'link_disconnect',
            'user_id' => optional($request->user())->id,
            'notes' => 'Link disconnected',
            'created_at' => now(),
        ]);
        return redirect()->back()
        ->with('success','Link Disconnected');
    }

    public function reconnect(Request $request, $id)
    {

        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 2;
        $link ->update($req);
        DB::table('audits')->insert([
            'entity_type' => 'link',
            'entity_id' => $link->id,
            'action' => 'link_reconnect',
            'user_id' => optional($request->user())->id,
            'notes' => 'Link reconnected',
            'created_at' => now(),
        ]);
        return redirect()->back()
        ->with('success','Link Reconnected');
    }

    public function decommission(Request $request, $id)
    {
        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 4;
        $link ->update($req);
        DB::table('audits')->insert([
            'entity_type' => 'link',
            'entity_id' => $link->id,
            'action' => 'link_decommission',
            'user_id' => optional($request->user())->id,
            'notes' => 'Link decommissioned',
            'created_at' => now(),
        ]);
        return redirect()->back()
            ->with('success','Link Decommissioned');
    }


}
