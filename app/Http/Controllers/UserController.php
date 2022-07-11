<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAll(){
        $users = User::latest()->get();
        $users->transform(function($user){
            $user->role = $user->getRoleNames()->first();
            $user->userPermissions = $user->getPermissionNames();
            return $user;
        });

        return response()->json([
            'users' => $users
        ], 200);


    }

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
        $this->validate($request, [
            'name' => 'required|string',
            'phone' => 'required',
            'password' => 'required|alpha_num|min:6',
            'role' => 'required',
            'email' => 'required|email|unique:users'
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);

        $user->assignRole($request->role);

        if($request->has('permissions')){
            $user->givePermissionTo($request->permissions);
        }

        $user->save();

        return response()->json("User Created", 200);
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
        $this->validate($request, [
            'name' => 'required|string',
            'phone' => 'required',
            'password' => 'nullable|alpha_num|min:6',
            'role' => 'required',
            'email' => 'required|email|unique:users,email,'.$id
        ]);

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        if($request->has('password')){
            $user->password = bcrypt($request->password);
        }


        if($request->has('role')){
            $userRole = $user->getRoleNames();
            foreach($userRole as $role){
                $user->removeRole($role);
            }

            $user->assignRole($request->role);
        }

        if($request->has('permissions')){
            $userPermissions = $user->getPermissionNames();
            foreach($userPermissions as $permssion){
                $user->revokePermissionTo($permssion);
            }

            $user->givePermissionTo($request->permissions);
        }


        $user->save();

        return response()->json('ok',200);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json('ok', 200);
    }

    //user profile
    public function profile(){
        return view('profile.index');
    }

    public function postProfile(Request $request){
        $user = auth()->user();

       // dd($user);
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'phone'=>'required'

        ]);

       /// $user->update($request->all());
       $user->update([
        'name'=>$request->name,
        'email'=>$request->email,
        'phone'=>$request->phone
       ]);



        return redirect()->back()->with('success','Profile Successfully updated');
    }

    public function getPassword(){
        return view('profile.password');
    }

    public function postPassword(Request $request){

        $this->validate($request, [
            'newpassword' => 'required|min:6|max:30|confirmed'
        ]);

        $user = auth()->user();

        $user->update([
            'password' => bcrypt($request->newpassword)
        ]);

        return redirect()->back()->with('success', 'Password has been Changed Successfully');
    }

    public function search(Request $request){
        $searchWord = $request->get('s');
        $users = User::where(function($query) use ($searchWord){
            $query->where('name', 'LIKE', "%$searchWord%")
            ->orWhere('email', 'LIKE', "%$searchWord%");
        })->latest()->get();

        $users->transform(function($user){
            $user->role = $user->getRoleNames()->first();
            $user->userPermissions = $user->getPermissionNames();
            return $user;
        });

        return response()->json([
            'users' => $users
        ], 200);

    }

}
