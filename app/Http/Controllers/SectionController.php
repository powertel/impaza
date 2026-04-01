<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use DB;

class SectionController extends Controller
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

        $sections = DB::table('sections')
                ->orderBy('sections.section', 'asc')
                ->get();
        $departments = Department::orderBy('department','asc')->get();

        return view('sections.index', compact('sections','departments'))
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
        $section = Section::all();
        return view('sections.create',compact('department','section'));
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
            // Bulk create via items[] if present
            if ($request->has('items')) {
                $request->validate([
                    'department_id' => 'required|integer|exists:departments,id',
                ]);
                $items = $request->input('items', []);
                foreach ($items as $item) {
                    $validated = validator($item, [
                        'section' => 'required|string|unique:sections,section',
                    ])->validate();
                    Section::create([
                        'department_id' => $request->input('department_id'),
                        'section' => $validated['section'],
                    ]);
                }
                DB::commit();
                return redirect()->route('sections.index')
                    ->with('success','Sections created successfully.');
            }

            // Fallback single create
            $request->validate([
                'department_id' => 'required|integer|exists:departments,id',
                'section' => 'required|string|unique:sections,section',
            ]);
            Section::create([
                'department_id' => $request->input('department_id'),
                'section' => $request->input('section'),
            ]);
            DB::commit();
            return redirect()->route('sections.index')
                ->with('success','Section Created');
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
        return view('sections.show',compact('section'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        return view('sections.edit',compact('section'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'section' => 'required|string|unique:sections,section,' . $section->id . ',id',
        ]);

        $section->update($request->all());

        return redirect()->route('sections.index')
                        ->with('success','Section Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Section::find($id)->delete();
        return redirect()->route('sections.index')
                        ->with('success','Section deleted successfully');
    }
}
