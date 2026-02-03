<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $configured = env('EXTERNAL_API_KEYS', env('API_KEY', ''));
        $validKeys = array_values(array_filter(array_map('trim', preg_split('/[,\s]+/', (string) $configured))));

        $key = $request->header('X-API-Key');
        if (!$key) {
            $auth = (string) $request->header('Authorization', '');
            if (stripos($auth, 'ApiKey ') === 0) {
                $key = trim(substr($auth, 7));
            }
        }

        if (!$key || empty($validKeys) || !in_array($key, $validKeys, true)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

