<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterPaymentRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\GenrateUserName;
use App\Repositories\PlanRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Models\Plan;
use App\Events\UserRegistered;  // Make sure you import the event
use Config;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{
    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $duration = $request->get('duration');
            if ($request->has('user') && $request->get('user') !== 'athlete' && $request->get('user') !== 'parent') {
                return redirect(route('plans'))->with('error', 'Invalid user selection.');
            }
            if (! $request->has('plan')) {
                return redirect(route('plans'))->with('error', 'Please select plan.');
            }
            $plan = PlanRepository::findOne(['key' => $request->get('plan'), 'status' => 'active']);
            if (empty($plan) || (empty($duration) || ($duration != 'monthly' && $duration != 'yearly' && $duration != 'free'))) {
                return redirect(route('plans'))->with('error', 'Please select a valid plan.');
            }
            $settings = SettingRepository::getSettings();

            return view('auth.register', compact('plan', 'settings'));
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    public function parentRegister(Request $request){
        try{
            if ($request->has('user_type') && $request->get('user_type') == 'parent') {
                // $plan = Plan::freePlan()->first();
                // if(empty($plan)){
                //     throw new Exception('Free Plan is not availble. Please contact the admin.');
                // }
                return view('auth.parent-register');
            }else{
                abort(404);
            }
        }catch(\Exception $ex){
            abort(404);
        }
    }

    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkoutSuccess(Request $request)
    {
        try {
            if ($request->has(['subscription_id', 'session_id'])) {
                $checkout = UserRepository::getCheckoutDetail($request);
                if (!$checkout) {
                    \Log::warning('Checkout detail not found for session: ' . $request->session_id);
                }
            }

            return view('auth.register-success');
        } catch (\Exception $ex) {
            \Log::error('Checkout Success Error: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            abort(500, 'Something went wrong');
        }
    }


    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkoutCancel()
    {
        return view('auth.register-cancel');
    }

    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPayment()
    {
        try {
            $settings = SettingRepository::getSettings(['stripe-publishable-key']);

            return view('auth.register-payment', compact('settings'));
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    /**
     * Handle account registration request
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $user = UserRepository::register($request);
        if (! empty($user)) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $user,
                    'message' => 'Registration Successful.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => "Somthing wen't wrong.",
                ],
                Config::get('constants.HttpStatus.OK')
            );
        }
    }

    /**
     * Handle account payment request
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribePlan(RegisterPaymentRequest $request)
    {
        try {
            $result = UserRepository::subscribePlan($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Plan successfully subscribed.',
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

    public function saveParentRegister(RegisterRequest $request){
        return $this->handleApiResponse(function () use ($request) {
            return UserRepository::saveParentRegister($request);
        }, 'Parent register successfully.');
    }
}
