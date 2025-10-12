<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use Illuminate\Validation\Rule;
use DB;

class PositionController extends Controller
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
        $positions = DB::table('positions')
            ->orderBy('positions.position', 'asc')
            ->get();
        $department = Department::all();
        $section = Section::all();

        return view('positions.index', compact('positions','department','section'))
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
        return view('positions.create',compact('department','section'));
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
                    'section_id' => 'required|integer|exists:sections,id',
                ]);
                $items = $request->input('items', []);
                foreach ($items as $item) {
                    $validated = validator($item, [
                        'position' => ['required','string', Rule::unique('positions','position')->where('section_id', $request->input('section_id'))],
                    ])->validate();
                    Position::create([
                        'section_id' => $request->input('section_id'),
                        'position' => $validated['position'],
                    ]);
                }
                DB::commit();
                return redirect()->route('positions.index')
                    ->with('success','Positions created successfully.');
            }

            // Fallback single create
            $request->validate([
                'department_id' => 'required|integer|exists:departments,id',
                'section_id' => 'required|integer|exists:sections,id',
                'position' => ['required','string', Rule::unique('positions','position')->where('section_id', $request->input('section_id'))],
            ]);

            $position = Position::create([
                'section_id' => $request->input('section_id'),
                'position' => $request->input('position'),
            ]);

            DB::commit();
            return redirect()->route('positions.index')
                ->with('success','Position Created');
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
    public function show(Position $position)
    {
        return view('positions.show',compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position)
    {
        return view('positions.edit',compact('position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'position' => [
                'required',
                'string',
                Rule::unique('positions', 'position')->ignore($position->id),
            ],
        ]);

        $position->update($request->only('position'));

        return redirect()->route('positions.index')
                        ->with('success','Position Updated');
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
