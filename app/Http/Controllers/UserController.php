<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use App\Models\SectionUser;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::join('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->leftjoin('positions','users.position_id','=','positions.id')
                ->get(['users.id','users.name','users.email','users.department_id','users.position_id','users.section_id','sections.section','departments.department','positions.position']);
        return view('users.index',compact('users'))
        ->with('i');
    }
    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        $department = Department::all();
        $section = Section::all();
        $position = Position::all();
        return view('users.create',compact('roles','department','section','position'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
/*         $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'department_id' => 'required',
            'section_id' => 'required',
            'position_id' => 'required',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User created successfully'); */

        DB::beginTransaction();
        try{
            request()->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:confirm-password',
                'department_id' => 'required',
                'section_id' => 'required',
                'position_id' => 'required',
                'roles' => 'required'
            ]);

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            $user->assignRole($request->input('roles'));

            $section_user = SectionUser::create(
                [
                    'section_id'=> $user->section_id,
                    'user_id'=> $user->id,
                ]
            );

            if($user && $section_user)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('users.index')
            ->with('success','User created successfully');
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
        $user = User::leftjoin('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->leftjoin('positions','users.position_id','=','positions.id')
                ->where('users.id','=',$id)
                ->get(['users.id','users.name','users.email','users.department_id','users.position_id','users.section_id','sections.section','departments.department','positions.position'])
                ->first();
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::leftjoin('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->leftjoin('positions','users.position_id','=','positions.id')
                ->where('users.id','=',$id)
                ->get(['users.id','users.name','users.email','users.department_id','users.position_id','users.section_id','sections.section','departments.department','positions.position'])
                ->first();
//dd($user);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        $department = Department::all();
        $section = Section::all();
        $position = Position::all();
    
        return view('users.edit',compact('user','roles','userRole','department','section','position'));
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
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
            'phonenumber'=>'required'

        ]);

       /// $user->update($request->all());
       $user->update([
        'name'=>$request->name,
        'email'=>$request->email,
        'phonenumber'=>$request->phonenumber
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


    public function getUsers()
    {
        $users = User::join('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->leftjoin('positions','users.position_id','=','positions.id')
                ->get(['users.id','users.name','users.email','users.department_id','users.position_id','users.section_id','sections.section','departments.department','positions.position']);
        return response()->json($users);
    }

}
