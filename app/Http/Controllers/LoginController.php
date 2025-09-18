<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\QuoteRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\UpsellRepository;
use Config;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        try {
            $settings = SettingRepository::getSettings(['login-background-image']);

            return view('auth.login', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Handle account login request
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        try {
            $userData = getUser();
            $credentials = $request->getCredentials();
            $user = UserRepository::login($credentials);
            
            if (! empty($user) && (Hash::check($request->password, $user->password) || (! empty($request->login_as) && ($request->login_as == 'user' || $request->login_as == 'parent')))
                // || (! empty($userData) && ($userData->user_type == 'parent' //Login as a user if loggedin user is parent
                // || (! empty($userData->parent_id) && $userData->user_type == 'athlete' && $userData->is_parent_login == 'yes')))//Login as a parent if loggedin user is athlete
            ) {
                session()->put('is_login', true);
                if($user->user_type != 'admin'){
                    $request->userData = $user;
                    $request->location = 'popup_after_login';
                    $is_show = UpsellRepository::showUpsellMessage($request);
                    
                    // dd($is_show);
                    if($is_show){
                        session()->put('show_upsell_popup', true);
                    }else{
                        session()->forget('show_upsell_popup', true);
                    }
  
                }
                
                if (($user->user_type == 'admin' && $request->user_type == 'user') || ($user->user_type !== 'admin' && $request->user_type == 'admin')) {
                    return response()->json(
                        [
                            'success' => false,
                            'data' => [],
                            'message' => 'Invalid email/username or password.',
                        ],
                        Config::get('constants.HttpStatus.OK')
                    );
                } elseif ($user->status == 'inactive') {
                    return response()->json(
                        [
                            'success' => false,
                            'data' => [],
                            'message' => 'Your account is inactive. please contact to admin.',
                        ],
                        Config::get('constants.HttpStatus.OK')
                    );
                } elseif ($user->status == 'payment_failed') {
                    $paymentData = UserRepository::getPaymentDetails(['user_id' => $user->id]);
                    return response()->json(
                        [
                            'success' => false,
                            'data' => $paymentData,
                            'message' => 'Your account was so close, but not quite finished.',
                        ],
                        Config::get('constants.HttpStatus.PAYMENT_REQUIRED')
                    );
                } else {
                    Auth::guard('web')->logout(); //Logout current user
                    UserRepository::updateParentLoginFields($request, $userData, $user);
                    $check = \Auth::guard('web')->loginUsingId($user->id);
                    if (! empty($check)) {
                        QuoteRepository::resetUserQuote($user); //Reset quote for set next quote in dashboard

                        return response()->json(
                            [
                                'success' => true,
                                'data' => $user,
                                'message' => 'Login Successful.',
                            ],
                            Config::get('constants.HttpStatus.OK')
                        );
                    } else {
                        return response()->json(
                            [
                                'success' => false,
                                'data' => [],
                                'message' => 'Invalid email/username or password.',
                            ],
                            Config::get('constants.HttpStatus.BAD_REQUEST')
                        );
                    }
                }
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'data' => [],
                        'message' => 'Invalid email/ username or password.',
                    ],
                    Config::get('constants.HttpStatus.BAD_REQUEST')
                );
            }
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Handle response after user authenticated
     *
     * @param  Auth  $user
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended();
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // deleteCookies('allowMenuAccess');
        Auth::guard('web')->logout();
        Session::flash('logout_success', "You've successfully logged out of the system");

        return redirect()->route('home');
    }

    /**
     * The user has logged out of the application.
     *
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Display forgot password page.
     *
     * @return Renderable
     */
    public function forgotPassword()
    {
        try {
            return view('auth.forgot-password');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display forgot password page.
     *
     * @return Renderable
     */
    public function sendForgotPasswordEmail(ForgotPasswordRequest $request)
    {
        try {
            $user = UserRepository::forgotPassword($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reset password link successfully sent to your registered email address.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    public function resetPasswordForm(Request $request)
    {
        try {
            $user = UserRepository::findOne(['verify_token' => $request->verify_token]);
            $verify_token = $request->verify_token;
            if (! empty($user) && ! empty($request->verify_token)) {
                return view('auth.reset-password', compact('user', 'verify_token'));
            } else {
                throw new \Exception('Link expired.', 1);
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * Get admin profile details
     *
     * @param  ForgotPasswordRequest  $request
     * @return Json
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = UserRepository::findOne(['verify_token' => $request->verify_token]);
            if (! empty($user) && ! empty($request->verify_token)) {
                $request['user_id'] = $user->id;
                $result = UserRepository::resetPassword($request);

                return response()->json(
                    [
                        'success' => true,
                        'data' => '',
                        'message' => 'Password successfully reset.',
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            } else {
                throw new \Exception('Link expired.', 1);
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $e->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
