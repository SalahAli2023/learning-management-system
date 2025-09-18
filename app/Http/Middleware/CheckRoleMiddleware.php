<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *Usage in routes: ->middleware('role:admin') or ->middleware('role:instructor,admin')
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            // not authenticated
            abort(403, 'Unauthorized');
        }

        // If roles list empty, deny
        if (empty($roles)) {
            abort(403, 'Unauthorized');
        }

        // Check if user's role is in roles list
        if (!in_array($user->role, $roles)) {
            abort(403, 'Forbidden - insufficient role');
        }
        return $next($request);
    }
}
