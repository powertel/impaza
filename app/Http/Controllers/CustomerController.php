<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use DB;

class CustomerController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index','store']]);
         $this->middleware('permission:customer-create', ['only' => ['create','store']]);
         $this->middleware('permission:customer-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }

    /**
     * Check if an account number is available (AJAX).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAccountNumber(Request $request)
    {
        $value = trim((string) $request->input('account_number'));
        $ignoreId = $request->input('ignore_id');

        if ($value === '') {
            return response()->json(['available' => false]);
        }

        $query = Customer::where('account_number', $value);
        if (!empty($ignoreId)) {
            $query->where('id', '<>', $ignoreId);
        }
        $exists = $query->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Check if a customer name is available (AJAX).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCustomerName(Request $request)
    {
        $value = trim((string) $request->input('customer'));
        $ignoreId = $request->input('ignore_id');

        if ($value === '') {
            return response()->json(['available' => false]);
        }

        $query = Customer::where('customer', $value);
        if (!empty($ignoreId)) {
            $query->where('id', '<>', $ignoreId);
        }
        $exists = $query->exists();

        return response()->json(['available' => !$exists]);
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
        $q = trim((string) request('q', ''));
        $statusId = request('status');

        $customersQuery = DB::table('customers')
                ->leftJoin('account_managers', 'customers.account_manager_id', '=', 'account_managers.id')
                ->leftJoin('users as account_manager_users', 'account_managers.user_id', '=', 'account_manager_users.id')
                ->orderBy('customers.customer', 'asc')
                ->select([
                    'customers.id',
                    'customers.customer',
                    'customers.account_number',
                    'customers.account_manager_id',
                    'customers.customer_status',
                    'customers.address',
                    'customers.contact_number',
                    'account_manager_users.name as accountManager',
                ]);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $customersQuery->where(function($qq) use ($like){
                $qq->where('customers.customer','like',$like)
                   ->orWhere('account_manager_users.name','like',$like)
                   ->orWhere('customers.account_number','like',$like)
                   ->orWhere('customers.address','like',$like)
                   ->orWhere('customers.contact_number','like',$like);
            });
        }

        if (!empty($statusId) && $statusId !== 'all') {
            $customersQuery->where('customers.customer_status', '=', (int) $statusId);
        }

        $customers = $customersQuery->paginate($perPage)->withQueryString();

        $customerStatusCounts = DB::table('customers')
            ->select('customer_status', DB::raw('count(*) as total'))
            ->groupBy('customer_status')
            ->pluck('total','customer_status');
        $totalCustomers = DB::table('customers')->count();

        // Fetch account managers joined with users for display and selection
        $accountManagers = DB::table('account_managers')
            ->leftJoin('users', 'account_managers.user_id', '=', 'users.id')
            ->whereNotNull('account_managers.user_id')
            ->orderBy('users.name', 'asc')
            ->get(['account_managers.id as am_id', 'account_managers.user_id as user_id', 'users.name as name']);

        return view('customers.index',compact('customers','accountManagers','customerStatusCounts','totalCustomers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $city = City::all();
        $customer = Customer::all();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        return view('customers.create',compact('city','location','link','pop'));
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
        try {
            if ($request->has('items')) {
                $items = $request->input('items', []);
                foreach ($items as $item) {
                    $validated = validator($item, [
                        'customer' => 'required|string|unique:customers,customer',
                        'account_number' => 'required|string|unique:customers,account_number',
                        'contract_number' => 'nullable|string|max:100',
                        'account_manager_id' => 'nullable|integer|exists:account_managers,id',
                        'address' => 'nullable|string|max:255',
                        'contact_number' => 'nullable|string|max:50',
                    ])->validate();
                    Customer::create([
                        'customer' => $validated['customer'],
                        'account_number' => $validated['account_number'],
                        'contract_number' => $validated['contract_number'] ?? null,
                        'account_manager_id' => $validated['account_manager_id'] ?? null,
                        'address' => $validated['address'] ?? null,
                        'contact_number' => $validated['contact_number'] ?? null,
                    ]);
                }
                DB::commit();
                return redirect()->route('customers.index')
                    ->with('success','Customer(s) Created.');
            }

            $request->validate([
                'customer' => 'required|string|unique:customers,customer',
                'account_number' => 'required|string|unique:customers,account_number',
                'contract_number' => 'nullable|string|max:100',
                'account_manager_id' => 'nullable|integer|exists:account_managers,id',
                'address' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:50',
            ]);
            $customer = Customer::create($request->only('customer','account_number','contract_number','account_manager_id','address','contact_number'));

            DB::commit();
            return redirect()->route('customers.index')
                ->with('success','Customer Created.');
        } catch (\Exception $ex) {
            DB::rollBack();
            return back()->withErrors(['error' => $ex->getMessage()])->withInput();
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
        // $customer = DB::table('customers')
        //         ->leftjoin('cities','customers.city_id','=','cities.id')
        //         ->leftjoin('suburbs','customers.suburb_id','=','suburbs.id')
        //         ->leftjoin('pops','customers.pop_id','=','pops.id')
        //         ->leftjoin('links','customers.id','=','links.customer_id')
        //         ->where('customers.id','=',$id)
        //         ->get(['customers.id','customers.customer','cities.city','links.link','pops.pop','suburbs.suburb'])
        //         ->first();
        // return view('customers.show',compact('customer'));
        $customer = DB::table('customers')
        ->where('customers.id','=',$id)
        ->get()
        ->first();
        return view('customers.show',compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = DB::table('customers')
                    ->where('customers.id','=',$id)
                    ->get(['customers.id','customers.customer'])
                    ->first();
        $cities = City::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        return view('customers.edit',compact('customer','cities','suburbs','pops'));
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
        $customer = Customer::find($id);
        $validated = $request->validate([
            'customer' => ['required','string', Rule::unique('customers','customer')->ignore($id)],
            'account_number' => ['required','string', Rule::unique('customers','account_number')->ignore($id)],
            'contract_number' => ['nullable','string','max:100'],
            'account_manager_id' => ['required','integer','exists:users,id'],
            'customer_status' => ['nullable','integer','in:1,2,3,4'],
            'address' => ['nullable','string','max:255'],
            'contact_number' => ['nullable','string','max:50'],
        ]);
        $customer->update($validated);
        return redirect(route('customers.index'))
            ->with('success','Customer updated successfully'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $hasLinks = DB::table('links')->where('customer_id', $id)->exists();
            if ($hasLinks) {
                return redirect()->route('customers.index')
                    ->withErrors(['error' => 'Cannot delete customer: there are associated links referencing this customer.']);
            }
            Customer::findOrFail($id)->delete();
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
        } catch (QueryException $e) {
            return redirect()->route('customers.index')
                ->withErrors(['error' => 'Delete failed due to existing references.']);
        }
    }

    public function disconnect(Request $request, $id)
    {
        $cust = Customer::findOrFail($id);
        $cust->update(['customer_status' => 3]);
        DB::table('audits')->insert([
            'entity_type' => 'customer',
            'entity_id' => $cust->id,
            'action' => 'customer_disconnect',
            'user_id' => optional($request->user())->id,
            'notes' => 'Customer disconnected',
            'created_at' => now(),
        ]);
        $affected = DB::table('links')
            ->where('customer_id', $cust->id)
            ->where('link_status', '<>', 4)
            ->pluck('id');
        DB::table('links')
            ->whereIn('id', $affected)
            ->update(['link_status' => 3]);
        foreach ($affected as $lid) {
            DB::table('audits')->insert([
                'entity_type' => 'link',
                'entity_id' => $lid,
                'action' => 'link_disconnect',
                'user_id' => optional($request->user())->id,
                'notes' => 'Link disconnected due to customer disconnect',
                'created_at' => now(),
            ]);
        }
        return back()->with('success','Customer disconnected and related links updated');
    }

    public function reconnect(Request $request, $id)
    {
        $cust = Customer::findOrFail($id);
        $cust->update(['customer_status' => 2]);
        DB::table('audits')->insert([
            'entity_type' => 'customer',
            'entity_id' => $cust->id,
            'action' => 'customer_reconnect',
            'user_id' => optional($request->user())->id,
            'notes' => 'Customer reconnected',
            'created_at' => now(),
        ]);
        $affected = DB::table('links')
            ->where('customer_id', $cust->id)
            ->where('link_status', '=', 3)
            ->pluck('id');
        DB::table('links')
            ->whereIn('id', $affected)
            ->update(['link_status' => 2]);
        foreach ($affected as $lid) {
            DB::table('audits')->insert([
                'entity_type' => 'link',
                'entity_id' => $lid,
                'action' => 'link_reconnect',
                'user_id' => optional($request->user())->id,
                'notes' => 'Link reconnected due to customer reconnect',
                'created_at' => now(),
            ]);
        }
        return back()->with('success','Customer reconnected; previously disconnected links restored');
    }

    public function reconnectDecommissioned(Request $request, $id)
    {
        $cust = Customer::findOrFail($id);
        $cust->update(['customer_status' => 2]);
        DB::table('audits')->insert([
            'entity_type' => 'customer',
            'entity_id' => $cust->id,
            'action' => 'customer_reconnect_decommissioned',
            'user_id' => optional($request->user())->id,
            'notes' => 'Customer reconnected from decommissioned',
            'created_at' => now(),
        ]);
        $lastCustDecom = DB::table('audits')
            ->where('entity_type', 'customer')
            ->where('entity_id', $cust->id)
            ->where('action', 'customer_decommission')
            ->orderByDesc('created_at')
            ->first();

        $customerLinkIds = DB::table('links')
            ->where('customer_id', $cust->id)
            ->pluck('id');

        $affected = DB::table('audits')
            ->where('entity_type', 'link')
            ->whereIn('entity_id', $customerLinkIds)
            ->where('action', 'link_decommission')
            ->where('notes', 'Link decommissioned due to customer decommission')
            ->when($lastCustDecom, function($q) use ($lastCustDecom){
                $q->where('created_at', '>=', $lastCustDecom->created_at);
            })
            ->pluck('entity_id');

        if ($affected->count() > 0) {
            DB::table('links')
                ->whereIn('id', $affected)
                ->where('link_status', 4)
                ->update(['link_status' => 2]);
        }

        foreach ($affected as $lid) {
            DB::table('audits')->insert([
                'entity_type' => 'link',
                'entity_id' => $lid,
                'action' => 'link_reconnect_from_decommission',
                'user_id' => optional($request->user())->id,
                'notes' => 'Link reconnected from customer decommissioned state',
                'created_at' => now(),
            ]);
        }
        return back()->with('success','Customer reconnected; decommissioned links restored');
    }

    public function decommission(Request $request, $id)
    {
        $cust = Customer::findOrFail($id);
        $cust->update(['customer_status' => 4]);
        DB::table('audits')->insert([
            'entity_type' => 'customer',
            'entity_id' => $cust->id,
            'action' => 'customer_decommission',
            'user_id' => optional($request->user())->id,
            'notes' => 'Customer decommissioned',
            'created_at' => now(),
        ]);
        $affected = DB::table('links')
            ->where('customer_id', $cust->id)
            ->where('link_status', '<>', 4)
            ->pluck('id');
        DB::table('links')
            ->whereIn('id', $affected)
            ->update(['link_status' => 4]);
        foreach ($affected as $lid) {
            DB::table('audits')->insert([
                'entity_type' => 'link',
                'entity_id' => $lid,
                'action' => 'link_decommission',
                'user_id' => optional($request->user())->id,
                'notes' => 'Link decommissioned due to customer decommission',
                'created_at' => now(),
            ]);
        }
        return back()->with('success','Customer decommissioned and all links decommissioned');
    }
}
