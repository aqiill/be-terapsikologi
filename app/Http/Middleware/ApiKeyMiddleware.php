<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyMiddleware
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('api-key');
        if ($apiKey !== 'qwe123qwe#') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
