<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('web')->check()) {
            if (Auth::guard('web')->user()->user_type == 'athlete') {
                return redirect('/athlete/dashboard');
            } elseif (Auth::guard('web')->user()->user_type == 'parent') {
                return redirect('/parent/dashboard');
            } elseif (Auth::guard('web')->user()->user_type == 'content-creator') {
                return redirect('/trainer/dashboard');
            } else {
                return redirect('/admin/dashboard');
            }
        }

        return $next($request);
    }
}
