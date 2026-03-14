<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }

    public function login(Request $request)
    {
        // Accept either username or email; resolve username by local-part lookup
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $identity = trim($credentials['email']);
        if ($identity && strpos($identity, '@') === false) {
            $userMatch = User::where('email', 'like', $identity . '@%')->first();
            if ($userMatch) {
                $identity = $userMatch->email;
            }
        }

        // Attempt with resolved email and enforce is_access = 0 (enabled)
        if (!Auth::attempt(['email' => $identity, 'password' => $credentials['password'], 'is_access' => 0])) {
            // Provide clearer error if credentials are correct but account disabled
            $userProbe = User::where('email', $identity)->first();
            if ($userProbe && Hash::check($credentials['password'], $userProbe->password) && (int)($userProbe->is_access ?? 0) !== 0) {
                return response()->json(['message' => 'Account disabled'], 403);
            }
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        /** @var User $user */
        $user = User::where('email', $identity)->first();
        $token = $user->createToken('powertel-mobile')->plainTextToken;

        // Include role names and key profile fields for parity with web expectations
        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->values()->all() : [];
        $permissions = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name')->values()->all() : [];

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'section_id' => $user->section_id ?? null,
                'position_id' => $user->position_id ?? null,
                'region' => $user->region ?? null,
                'phonenumber' => $user->phonenumber ?? null,
                'roles' => $roles,
                'permissions' => $permissions,
            ],
        ]);
    }
}
