<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotStudent
{
    public function handle($request, Closure $next)
    {
        // Use your student authentication logic here
        if (!Auth::guard('student')->check()) {
            return redirect()->route('student.login');
        }

        return $next($request);
    }
}
