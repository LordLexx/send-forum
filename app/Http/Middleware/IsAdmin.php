<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow both admins and moderators to access the dashboard
        if (! auth()->check() || !auth()->user()->isStaff()) {
            abort(404, 'Not Found.');
        }

        return $next($request);
    }
}
