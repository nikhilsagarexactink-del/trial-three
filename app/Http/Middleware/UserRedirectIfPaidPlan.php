<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Auth;

class UserRedirectIfPaidPlan
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
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type != 'admin') {
            $user = Auth::guard('web')->user();
            $subscription = UserSubscription::where('user_id', $user->id)->activeSubscription()->first();
            if(!empty($subscription) && $subscription->subscription_type != 'free'){
                return $next($request);
            }else{
                return redirect()->route('user.dashboard', ['user_type' => $user->user_type]);
            }
        }
        return $next($request);
    }
}
