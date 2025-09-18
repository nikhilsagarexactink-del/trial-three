<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppearanceSettingRequest;
use App\Http\Requests\CaptchaSettingRequest;
use App\Http\Requests\EmailSettingRequest;
use App\Http\Requests\LegalSettingRequest;
use App\Http\Requests\PaymentProcessorSettingRequest;
use App\Repositories\SettingRepository;
use App\Http\Requests\SettingRequest;
use Config;
use File;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show the plan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userData = getUser();
            // Only admin can access
            if($userData->user_type == 'admin') {
                $settings = SettingRepository::getSettings();
                return view('setting.email.index', compact('settings'));
            }else{
                return redirect()->route('user.dashboard', ['user_type' => $userData->user_type])->with('error', 'You do not have access to this page.');
            }
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }

    /**
     * Update Email setting
     *
     * @param  Request  $request
     * @return Json
     */
    public function updateEmailSettings(EmailSettingRequest $request)
    {
        try {
            $result = SettingRepository::updateSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting successfully updated.',
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

    /**
     * Update Legal setting
     *
     * @param  Request  $request
     * @return Json
     */
    public function updateLegalSettings(LegalSettingRequest $request)
    {
        try {
            $result = SettingRepository::updateSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting successfully updated.',
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

    /**
     * Show the plan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function legalIndex()
    {
        try {
            $settings = SettingRepository::getSettings();

            return view('setting.legal.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the plan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function captchaIndex()
    {
        try {
            $settings = SettingRepository::getSettings();

            return view('setting.captcha.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update captcha setting
     *
     * @param  Request  $request
     * @return Json
     */
    public function updateCaptchaSettings(CaptchaSettingRequest $request)
    {
        try {
            $result = SettingRepository::updateSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting successfully updated.',
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

    /**
     * Get setting
     *
     * @return Json
     */
    public function getSetting(Request $request)
    {
        try {
            $result = SettingRepository::getAdminSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting detail.',
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

    /**
     * Show the payment processor index.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentProcessorIndex()
    {
        try {
            $settings = SettingRepository::getSettings();

            return view('setting.payment-processor.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update payment processor setting
     *
     * @param  Request  $request
     * @return Json
     */
    public function updatePaymentProcessorSettings(PaymentProcessorSettingRequest $request)
    {
        try {
            $result = SettingRepository::updateSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting successfully updated.',
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

    /**
     * Show the appearance index.
     *
     * @return \Illuminate\Http\Response
     */
    public function appearanceIndex()
    {
        try {
            $settings = SettingRepository::getSettings();
            // echo '<pre>';
            // print_r($settings);
            // exit;

            return view('setting.appearance.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update appearance setting
     *
     * @param  Request  $request
     * @return Json
     */
    public function updateAppearanceSettings(AppearanceSettingRequest $request)
    {
        try {
            $result = SettingRepository::updateSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting successfully updated.',
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

    /**
     * Show the general setting index.
     *
     * @return \Illuminate\Http\Response
     */
    public function generalSettingIndex()
    {
        try {
            $settings = SettingRepository::getSettings();
            $contents = File::get(base_path('public/assets/timezones.json'));
            $timezone = json_decode(json: $contents, associative: true);
            // echo '<pre>';
            // print_r($settings);
            // exit;

            return view('setting.general-settings.index', compact('settings', 'timezone'));
        } catch (\Exception $ex) {
            //abort(404);
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
     * Update General setting
     *
     * @return Json
     */
    public function updateGeneralSettings(SettingRequest $request)
    {
        try {
            $result = SettingRepository::updateSettings($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Setting successfully updated.',
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
}