<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! $request->user()) {
            abort(403, 'Unauthorized');
        }

        if ($request->user()->role === 'super_admin') {
            return $next($request);
        }

        if (! in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
