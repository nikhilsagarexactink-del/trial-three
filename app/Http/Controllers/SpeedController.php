<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpeedRequest;
use App\Repositories\SpeedRepository;
use Config;
use Illuminate\Http\Request;

class SpeedController extends Controller
{
    public function index(Request $request)
    {
        try {
            $settings = SpeedRepository::getSettings($request);

            return view('speed.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function inputForm(Request $request)
    {
        try {
            $settings = SpeedRepository::getSettings($request);

            return view('speed.input-form', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the speed setting index.
     *
     * @return \Illuminate\Http\Response
     */
    public function speedSettingIndex(Request $request)
    {
        try {
            $settings = SpeedRepository::getSettings($request);

            return view('speed.setting', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
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
     * Save input
     *
     * @return \Illuminate\Http\Response
     */
    public function saveSpeedInput(SpeedRequest $request)
    {
        try {
            $result = SpeedRepository::saveSpeedInput($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Input successfully added.',
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
     * Display a listing of the resource.
     *
     * @return Response
     */
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
}
