<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Read via config so it still works after `php artisan config:cache`.
        $configured = (string) config('services.external_api.keys', '');
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

        // Expose the matched key so controllers can apply per-key rules (e.g. channel binding).
        $request->attributes->set('api_key', $key);

        return $next($request);
    }
}

