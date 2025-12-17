<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // GET /api/mobile/profile
    public function show(Request $request)
    {
        $u = $request->user();

        $profile = DB::table('users')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('sections', 'users.section_id', '=', 'sections.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.id', $u->id)
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.section_id',
                'users.position_id',
                'users.region',
                'users.phonenumber',
                'departments.department as department',
                'sections.section as section',
                'positions.position as position',
            ])
            ->first();

        return response()->json(['user' => $profile]);
    }

    // PUT /api/mobile/profile
    public function update(Request $request)
    {
        $u = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phonenumber' => 'required|string|max:255',
        ]);

        DB::table('users')->where('id', $u->id)->update([
            'name' => $data['name'],
            'phonenumber' => $data['phonenumber'],
        ]);

        return response()->json(['success' => true, 'message' => 'Profile updated']);
    }

    // POST /api/mobile/profile/password
    public function changePassword(Request $request)
    {
        $request->validate([
            'newpassword' => [
                'required',
                'string',
                'min:8',
                'max:30',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ]);

        $u = $request->user();

        DB::table('users')->where('id', $u->id)->update([
            'password' => Hash::make($request->input('newpassword')),
        ]);

        return response()->json(['success' => true, 'message' => 'Password changed successfully']);
    }
}
