<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use DB;

class DepartmentController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:department-list|department-create|department-edit|department-delete', ['only' => ['index','store']]);
         $this->middleware('permission:department-create', ['only' => ['create','store']]);
         $this->middleware('permission:department-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:department-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $departments = DB::table('departments')
                ->orderBy('departments.department', 'asc')
                ->get();

        return view('departments.index', compact('departments'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $department = Department::all();
        return view('departments.create',compact('department'));
    }

    public function findSection($id)
    {
        $section = Section::where('department_id',$id)
        ->pluck("section","id");
        return response()->json($section);
    }

    public function findPosition($id)
    {
        $position = Position::where('section_id',$id)
        ->pluck("position","id");
        return response()->json($position);
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
    
                'department' => 'required|string|unique:departments',
                'section' => 'required|string|unique:sections',
                'position' => 'required|string|unique:positions'
            ]);

            $department =  Department::create(
                [
                    'department' => $request['department'],
                ]
            );
            $section = Section::create(
                [
                    'department_id' => $department->id,
                    'section' => $request['section'],
                ]
            );
            $position= Position::create(
                [
                    'section_id' => $section->id,
                    'position' => $request['position'],
                ]
            );
            
            
            if($department&&$section&&$position)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('departments.index')
            ->with('success','Department created successfully.');
        }

        catch(Exception $ex)
        {
            DB::rollback();
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return view('departments.show',compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        return view('departments.edit',compact('department'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department' => 'required|string|unique:departments',
            
        ]);
      
        $department->update($request->all());
      
        return redirect()->route('departments.index')
                        ->with('success','Department Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        Department::find($id)->delete();
        return redirect()->route('departments.index')
                        ->with('success','Department deleted successfully');
    }

}
