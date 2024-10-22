<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotStaff
{
    public function handle($request, Closure $next)
    {
        // Use your student authentication logic here
        if (!Auth::guard('staff')->check()) {
            return redirect()->route('staff.login');
        }

        return $next($request);
    }
}
