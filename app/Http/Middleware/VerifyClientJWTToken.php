<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Models\UserDeviceToken;
use Config;

class VerifyClientJWTToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected function user()
    {
        return JWTAuth::parseToken()->authenticate();
    }

    public function handle($request, Closure $next)
    {
        $token = $request->header('authorization');
        try {
            
            $user = $this->user();
            if (!empty($user)) {
                $token = str_replace('Bearer ', '', $token);
                $check = UserDeviceToken::where(['user_id'=>$user->id, 'token'=>$token])->first();
            }
            if (!empty($check)) {
                $user = $this->user();
                if (!empty($user)) {
                    // if ($user->user_type == 'admin') {
                    //     return response()->json(
                    //         [
                    //             'success' => false,
                    //             'data' => '',
                    //             'message' => 'Invalid User.'
                    //         ],
                    //         Config::get('constants.HttpStatus.UNAUTHORIZED')
                    //     );
                    // }
                    if ($user->status == 'inactive') {
                        return response()->json(
                            [
                                'success' => false,
                                'data' => '',
                                'message' => 'You account has been inactive from the admin. Please contact to admin'
                            ],
                            Config::get('constants.HttpStatus.UNAUTHORIZED')
                        );
                    }
                    if ($user->status == 'deleted') {
                        return response()->json(
                            [
                                'success' => false,
                                'data' => '',
                                'message' => 'Token expired.'
                            ],
                            Config::get('constants.HttpStatus.UNAUTHORIZED')
                        );
                    }
                }
            } else {
                throw new TokenExpiredException;
            }
        } catch (JWTException $e) {
            if ($e instanceof TokenExpiredException) {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => 'Session expire.'
                    ],
                    Config::get('constants.HttpStatus.UNAUTHORIZED')
                );
            } else if ($e instanceof TokenInvalidException) {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => 'Session expire.'
                    ],
                    Config::get('constants.HttpStatus.UNAUTHORIZED')
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => 'Session expire.'
                    ],
                    Config::get('constants.HttpStatus.UNAUTHORIZED')
                );
            }
        }
        return $next($request);
    }

}
