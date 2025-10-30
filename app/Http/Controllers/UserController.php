<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use App\Models\SectionUser;
use App\Models\UserStatus;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update','changePassword']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
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

        $usersQuery = User::query()
            ->join('departments','users.department_id','=','departments.id')
            ->leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('positions','users.position_id','=','positions.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->orderBy('users.name', 'asc')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.department_id',
                'users.position_id',
                'users.section_id',
                'users.phonenumber',
                'users.region',
                'sections.section',
                'departments.department',
                'positions.position',
                'user_statuses.status_name',
            ]);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $usersQuery->where(function($qq) use ($like){
                $qq->where('users.name','like',$like)
                   ->orWhere('users.email','like',$like)
                   ->orWhere('departments.department','like',$like)
                   ->orWhere('sections.section','like',$like)
                   ->orWhere('positions.position','like',$like)
                   ->orWhere('user_statuses.status_name','like',$like)
                   ->orWhere('users.region','like',$like)
                   ->orWhere('users.phonenumber','like',$like);
            });
        }

        $users = $usersQuery->paginate($perPage)->withQueryString();
        
        // Provide supporting datasets for modal-based create/edit in index
        $roles = Role::pluck('name','name')->all();
        $department = Department::all();
        $section = Section::all();
        $position = Position::all();
        $user_statuses = UserStatus::all();
        // Regions list and current user's region for user creation
        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        $currentUserRegion = auth()->user()->region;
        
        return view('users.index',compact('users','roles','department','section','position','user_statuses','regions','currentUserRegion'))
        ->with('i');
    }
    /**
     * Show the form for creating a new  resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        $department = Department::all();
        $section = Section::all();
        $position = Position::all();
        $user_statuses = UserStatus::all();
        return view('users.create',compact('roles','department','section','position','user_statuses'));
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
        $input['password'] = Hash::ma    ke($input['password']);

        $user = User::create($input);
        $user->assignRole($request->    input('roles'));

        return redirect()->route('users.index')
                        ->wit    h('success','User created successfully'); */

        DB::beginTransaction();
        try{
            request()->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:confirm-password',
                'department_id' => 'required',
                'section_id' => 'required',
                'position_id' => 'required',
                'roles' => 'required',
                'region' => 'nullable|string',
                'phonenumber' => ['nullable','string','max:32','regex:/^\+?[0-9\s-]{7,20}$/']
            ]);

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            // Enforce region based on the logged-in user's region if set
            $currentUserRegion = auth()->user()->region;
            if (!empty($currentUserRegion)) {
                $input['region'] = $currentUserRegion;
            }

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
                ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
                ->where('users.id','=',$id)
                ->get(['users.id','users.name','users.email','users.department_id','users.position_id','users.section_id','sections.section','departments.department','positions.position','users.user_status','user_statuses.status_name'])
                ->first();
        //dd($user);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        $department = Department::all();
        $section = Section::all();
        $position = Position::all();
        $user_statuses = UserStatus::all();

        return view('users.edit',compact('user','roles','userRole','department','section','position','user_statuses'));
    }

    /**
     * Update the specified     resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'password' => 'same:confirm-password',
                'roles' => 'required',
                'region' => 'nullable|string',
                'phonenumber' => ['nullable','string','max:32','regex:/^\+?[0-9\s-]{7,20}$/'],
            ]);

            $input = $request->all();
            if (!empty($input['phonenumber'])) {
                // normalize: trim spaces and collapse into digits plus optional leading '+'
                $normalized = preg_replace('/[^0-9+]/', '', $input['phonenumber']);
                // keep only first '+' if present
                $normalized = ltrim($normalized, '+');
                $normalized = (strpos($input['phonenumber'], '+') === 0 ? '+' : '') . $normalized;
                $input['phonenumber'] = $normalized;
            }
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
            ->with('success','User updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error renewing contract: ' . $e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
         *
     * @param  int  $id
     * @return \Illuminate\Ht    tp\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

    //user profile
    public function profile() {
        $u = auth()->user();
        if (!$u) return view('profile.index'); // or redirect to login

        $profile = \App\Models\User::leftJoin('departments','users.department_id','=','departments.id')
            ->leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('positions','users.position_id','=','positions.id')
            ->where('users.id', $u->id)
            ->get(['users.*','departments.department','sections.section','positions.position'])
            ->first();
        return view('profile.index', ['user' => $profile]);
    }

    public function postProfile(Request $request){
        $user = auth()->user();

       // dd($user);
        $this->validate($request,[
            'name'=>'required',
            'phonenumber'=>'required'

        ]);

       /// $user->update($request->all());
       $user->update([
        'name'=>$request->name,
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

    // Admin: change password for a specific user
    public function changePassword(Request $request, $id)
    {
        $this->validate($request, [
            'newpassword' => 'required|min:6|max:30|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => bcrypt($request->newpassword),
        ]);

        return redirect()->route('users.index')->with('success', 'Password changed successfully');
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
