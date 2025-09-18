<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class UserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::guard('web')->check()) {
            return  redirect()->route('home');
        }
        $request->request->add(['guard' => 'web']);
        $userType = $request->route('user_type');
        $loggedInUserType = '';
        //Redirect to dashboard if the user type is mismatch
        if (Auth::guard('web')->check() && ! empty($userType)) {
            $loggedInUserType = Auth::guard('web')->user()->user_type;
            if ($loggedInUserType != $userType) {
                return  redirect()->route('user.dashboard', ['user_type' => $loggedInUserType]);
            }
        }

        $response = $next($request);

        return $response->header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
