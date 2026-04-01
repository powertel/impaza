<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    /**
     * Timeout in minutes.
     *
     * @var int
     */
    protected int $timeout = 30;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity');
            $now = now()->timestamp;

            if ($lastActivity && (($now - $lastActivity) > ($this->timeout * 60))) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', 'You have been logged out due to inactivity.');
            }

            $request->session()->put('last_activity', $now);
        }

        return $next($request);
    }
}