<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_admin) {
            $user = auth()->user();

            // New: catch unverified users and send them to verify their email
            if ($user->status === 'unverified') {
                $userId = $user->id;
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('verify.email', ['id' => $userId])
                    ->with('error', 'Please verify your email address to continue.');
            }

            if ($user->status === 'pending') {
                auth()->logout();
                return redirect()->route('still.pending');
            }
            if ($user->status === 'banned') {
                auth()->logout();
                return redirect()->route('suspended');
            }
            if ($user->status === 'rejected') {
                auth()->logout();
                return redirect()->route('rejected');
            }
        }

        return $next($request);
    }
}
