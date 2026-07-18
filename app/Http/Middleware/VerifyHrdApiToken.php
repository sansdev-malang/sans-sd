<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHrdApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-API-TOKEN');

        if (!$token || $token !== config('app.hrd_api_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing API token.'
            ], 401);
        }

        return $next($request);
    }
}
