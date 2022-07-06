<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\AccountManager;
use DB;

class AccountManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $account_managers = DB::table('account_managers')
                ->orderBy('account_managers.created_at', 'desc')
                ->get();
        return view('account_managers.index',compact('account_managers'))
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
            'accountManager' => 'required|string|unique:account_managers'
        ]);

        $acc_manager = AccountManager::create($request->all());

        
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
            'accountManager' => 'required|string|unique:account_managers'
        ]);
        
        $acc_manager = AccountManager::find($id);
        $acc_manager ->update($request->all());
        
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
        //
    }
}
