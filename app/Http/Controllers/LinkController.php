<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use App\Models\LinkType;
use Illuminate\Validation\Rule;
use DB;

class LinkController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:link-list|link-create|link-edit|link-delete', ['only' => ['index','store']]);
         $this->middleware('permission:link-create', ['only' => ['create','store']]);
         $this->middleware('permission:link-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:link-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = DB::table('links')
            ->leftjoin('customers','links.customer_id','=','customers.id')
            ->leftjoin('cities','links.city_id','=','cities.id')
            ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
            ->leftjoin('pops','links.pop_id','=','pops.id')
            ->leftJoin('link_types','links.linkType_id','=','link_types.id')
            ->orderBy('cities.city', 'asc')
            ->get(['links.id','links.jcc_number','links.link','link_types.linkType as linkType','links.service_type','links.capacity','customers.customer','cities.city','pops.pop','suburbs.suburb']);
        $customers = DB::table('customers')->orderBy('customers.customer', 'asc')->get();
        $cities = City::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        $linkTypes = LinkType::all();
        return view('links.index',compact('links','customers','cities','suburbs','pops','linkTypes'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $city = City::all();
        //$customer = Customer::all();
        $customer = DB::table('customers')
        ->orderBy('customers.customer', 'asc')
        ->get();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        $linkType = LinkType::all();
        return view('links.create',compact('customer','city','linkType','location','link','pop'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Support both single create and batch create via repeater items
        if ($request->has('items')) {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.city_id' => 'required|exists:cities,id',
                'items.*.suburb_id' => 'required|exists:suburbs,id',
                'items.*.linkType_id' => 'required|exists:link_types,id',
                'items.*.pop_id' => 'required|exists:pops,id',
                'items.*.link' => 'required|string|unique:links,link',
                'items.*.jcc_number' => 'nullable|string|max:255|unique:links,jcc_number',
                'items.*.service_type' => 'nullable|string|in:Internet,VPN,Carrier Services,E-Vending',
                'items.*.capacity' => 'nullable|string|max:255',
            ]);

            $customerId = $request->input('customer_id');
            $created = 0;
            foreach ($request->input('items') as $item) {
                $data = [
                    'customer_id' => $customerId,
                    'city_id' => $item['city_id'],
                    'suburb_id' => $item['suburb_id'],
                    'linkType_id' => $item['linkType_id'],
                    'pop_id' => $item['pop_id'],
                    'link' => $item['link'],
                    'jcc_number' => $item['jcc_number'] ?? null,
                    'service_type' => $item['service_type'] ?? null,
                    'capacity' => $item['capacity'] ?? null,
                    'link_status' => 1,
                ];
                Link::create($data);
                $created++;
            }

            return redirect()->route('links.index')
                ->with('success', $created.' link(s) created');
        } else {
            $request->validate([
                'city_id' => 'required|exists:cities,id',
                'suburb_id' => 'required|exists:suburbs,id',
                'linkType_id' => 'required|exists:link_types,id',
                'pop_id' => 'required|exists:pops,id',
                'customer_id' => 'required|exists:customers,id',
                'link' => 'required|string|unique:links,link',
                'jcc_number' => 'nullable|string|max:255|unique:links,jcc_number',
                'service_type' => 'nullable|string|in:Internet,VPN,Carrier Services,E-Vending',
                'capacity' => 'nullable|string|max:255',
            ]);
            $req = $request->all();
            $req['link_status'] = 1;
            $link = Link::create($req);

            if($link)
            {
                return redirect()->route('links.index')
                ->with('success','Link Created');
            }
            else
            {
                return back()->with('fail','Something went wrong');
            }
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
        $link = DB::table('links')
                ->leftjoin('customers','links.customer_id','=','customers.id')
                ->leftjoin('cities','links.city_id','=','cities.id')
                ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
                ->leftjoin('pops','links.pop_id','=','pops.id')
                ->leftjoin('link_types','links.linkType_id','=','link_types.id')
                ->where('links.id','=',$id)
                ->get(['links.id','links.link','customers.customer','link_types.linkType','cities.city','pops.pop','suburbs.suburb','links.jcc_number','links.service_type','links.capacity'])
                ->first();
        return view('links.show',compact('link'));
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
                ->leftjoin('link_types','links.linkType_id','=','link_types.id')
                ->where('links.id','=',$id)
                ->get(['links.id','links.linkType_id','links.link','links.customer_id','link_types.linkType','links.city_id','links.pop_id','links.suburb_id','customers.customer','cities.city','pops.pop','suburbs.suburb','links.jcc_number','links.service_type','links.capacity'])
                ->first();
                $customers = Customer::all();
                $cities = City::all();
                $suburbs = Suburb::all();
                $pops = Pop::all();
                $linkTypes =LinkType::all();
        return view('links.edit',compact('link','customers','cities','suburbs','pops','linkTypes'));
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
        $link = Link::find($id);
        // Validate link uniqueness on update (similar to customer validation)
        $validated = $request->validate([
            'link' => ['required','string', Rule::unique('links','link')->ignore($id)],
            'jcc_number' => ['nullable','string','max:255', Rule::unique('links','jcc_number')->ignore($id)],
            'service_type' => 'nullable|string|in:Internet,VPN,Carrier Services,E-Vending',
            'capacity' => 'nullable|string|max:255',
        ]);
        $link->update($request->all());
        return redirect(route('links.index'))
            ->with('success','Link Updated');
    }

    /**
     * Check if a link name is available (AJAX).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLinkName(Request $request)
    {
        $value = trim((string) $request->input('link'));
        $ignoreId = $request->input('ignore_id');

        if ($value === '') {
            return response()->json(['available' => false]);
        }

        $query = Link::where('link', $value);
        if (!empty($ignoreId)) {
            $query->where('id', '<>', $ignoreId);
        }
        $exists = $query->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Check if a JCC number is available (AJAX).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkJccNumber(Request $request)
    {
        $value = trim((string) $request->input('jcc_number'));
        $ignoreId = $request->input('ignore_id');

        // If empty, treat as available (field is optional)
        if ($value === '') {
            return response()->json(['available' => true]);
        }

        $query = Link::where('jcc_number', $value);
        if (!empty($ignoreId)) {
            $query->where('id', '<>', $ignoreId);
        }
        $exists = $query->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Link::find($id)->delete();
        return redirect()->route('links.index');
                       
    }

}
