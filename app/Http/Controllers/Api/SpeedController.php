<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\SpeedRepository;
use Config;
use Illuminate\Http\Request;

class SpeedController extends ApiController
{
    public function loadSpeedData(Request $request)
    {
        try {
            $results = SpeedRepository::loadList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
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
     * Save setting
     *
     * @return \Illuminate\Http\Response
     */
    public function saveSpeedSetting(Request $request)
    {
        try {
            $result = SpeedRepository::saveSetting($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
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
     * Show the speed setting.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadSpeedSetting(Request $request)
    {
        try {
            $settings = SpeedRepository::getSettings($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $settings,
                    'message' => 'Setting successfully fetched.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            abort(404);
        }
    }
}
