<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use App\Models\UserDeviceToken;

class SetUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ✅ Set User Timezone or Default to UTC
        if(Auth::check()){
            $timezone = Auth::check() && !empty(Auth::user()->timezone) ? Auth::user()->timezone : 'America/Denver';
        }else{
            $token = $request->header('authorization');
            $token = str_replace('Bearer ', '', $token);
            $deviceToken = UserDeviceToken::with('user')->where('token', $token)->first();
            if (!empty($deviceToken) && !empty($deviceToken->user) && !empty($deviceToken->user->timezone)) {
                $timezone = $deviceToken->user->timezone;
            }else{
                $timezone = 'America/Denver';
            }
        }
        

        config(['app.timezone' => $timezone]); // Laravel timezone
        date_default_timezone_set($timezone);  // PHP timezone=
        // Continue and attach timezone to the response

        //Set timezone for api like mobile app
        $response = $next($request);
        $response->headers->set('X-App-Timezone', $timezone);

        return $response;
    }
}
