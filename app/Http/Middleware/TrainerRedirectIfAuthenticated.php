<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class TrainerRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type != 'content-creator') {
            $userType = Auth::guard('web')->user()->user_type;

            return  redirect()->route('user.dashboard', ['user_type' => $userType]);
        }

        return $next($request);
    }
}
