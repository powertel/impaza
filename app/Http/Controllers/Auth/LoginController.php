<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * Enforce that only users with is_access = 0 (enabled) can login.
     */
    protected function credentials(Request $request)
    {
        $login = trim($request->input($this->username()));

        // If user typed a local-part (no '@'), resolve to actual email from DB
        if ($login && strpos($login, '@') === false) {
            $user = \App\Models\User::where('email', 'like', $login . '@%')->first();
            if ($user) {
                $login = $user->email;
            }
            // Do NOT modify the request; keep user input as-is
        }

        return [
            $this->username() => $login,
            'password' => $request->input('password'),
            'is_access' => 0,
        ];
    }
}
