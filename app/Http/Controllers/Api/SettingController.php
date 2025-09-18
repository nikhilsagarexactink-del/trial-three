<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\CaptchaSettingRequest;
use App\Http\Requests\Api\EmailSettingRequest;
use App\Http\Requests\Api\LegalSettingRequest;
use App\Http\Requests\Api\PaymentProcessorSettingRequest;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\SportRepository;
use Config;
use Illuminate\Http\Request;

class SettingController extends ApiController
{
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
        public function getSettings(Request $request)
        {
            try {
                $result = SettingRepository::getSettings();

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
     * Get sports list
     *
     * @return Response
     */
    public function loadSports(Request $request)
    {
        try {
            $sports = SportRepository::loadList($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $sports,
                    'message' => '',
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
     * Get permission list
     *
     * @return Response
     */
    public function loadPermission(Request $request)
    {
        try {
            $permision = getModulePermission();
            $activityPermission = UserRepository::userActivityPermission();
            return response()->json(
                [
                    'success' => true,
                    'data' => ['permissions' =>$permision, 'activtyPermission' =>$activityPermission],
                    'message' => 'Permission load successfully.',
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
