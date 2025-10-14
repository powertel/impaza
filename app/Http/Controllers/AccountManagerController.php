<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\AccountManager;
use DB;

class AccountManagerController extends Controller
{    
    function __construct()
    {
         $this->middleware('permission:account-manager-list|account-manager-create|account-manager-edit|account-manager-delete', ['only' => ['index','store']]);
         $this->middleware('permission:account-manager-create', ['only' => ['create','store']]);
         $this->middleware('permission:account-manager-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:account-manager-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $account_managers = DB::table('account_managers')
                ->leftJoin('users','account_managers.user_id','=','users.id')
                ->orderBy('account_managers.created_at', 'desc')
                ->get(['account_managers.id','account_managers.user_id','users.name']);

        // Provide users list for create/edit modals
        $users = DB::table('users')
                ->orderBy('name','asc')
                ->get(['id','name']);

        return view('account_managers.index',compact('account_managers','users'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('account_managers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'user_id' => 'required|integer|exists:users,id|unique:account_managers,user_id',
        ]);

        $acc_manager = AccountManager::create([
            'user_id' => $request->input('user_id'),
        ]);

        
        if($acc_manager)
        {
            return redirect()->route('account_managers.index')
            ->with('success','Department Created.');
        }
        else
        {
            return back()->with('fail','Something went wrong');
        }

/*         $request -> validate([
            'accountManager' => 'required|string|unique:account_managers'
        ]);

        $acc_manager = new AccountManager();
        $acc_manager -> accountManager = $request -> accountManager;

        $res = $acc_manager ->save();

        if($res)
        {
            return redirect()->route('account_managers.index')
            ->with('success','Department Created.');
        }
        else
        {
            return back()->with('fail','Something went wrong');
        } */
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $acc_manager = DB::table('account_managers')
                ->where('account_managers.id','=',$id)
                ->get()
                ->first();
        return view('account_managers.show',compact('acc_manager'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $acc_manager = DB::table('account_managers')
                ->where('account_managers.id','=',$id)
                ->get()
                ->first();
        return view('account_managers.edit',compact('acc_manager'));
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
            'user_id' => 'required|integer|exists:users,id|unique:account_managers,user_id,'.$id.',id',
        ]);
        
        $acc_manager = AccountManager::find($id);
        $acc_manager->update([
            'user_id' => $request->input('user_id'),
        ]);
        
        if($acc_manager)
        {
            return redirect(route('account_managers.index'))
            ->with('success','Account Manager Updated');
        }
        else
        {
            return back()->with('fail','Something went wrong');
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
        $acc_manager = AccountManager::find($id);
        if(!$acc_manager){
            return redirect()->route('account_managers.index')->with('fail','Account Manager not found');
        }

        // Only allow delete if no customers associated with this manager's user_id
        $customerCount = DB::table('customers')
            ->where('account_manager_id','=',$acc_manager->id)
            ->count();

        if($customerCount > 0){
            return back()->with('fail','Cannot delete: customers are associated with this account manager');
        }

        $acc_manager->delete();
        return redirect()->route('account_managers.index')->with('success','Account Manager deleted');
    }
}
