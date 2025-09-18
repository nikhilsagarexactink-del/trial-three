<?php

namespace App\Http\Controllers;

use App\Http\Requests\HealthMarkerRequest;
use App\Http\Requests\HealthMeasurementRequest;
use App\Http\Requests\HealthSettingRequest;
use App\Http\Requests\WeightGoalRequest;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\SettingRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\RewardRepository;
use Config;
use Illuminate\Http\Request;
use View;

class HealthTrackerController extends BaseController
{
    /**
     * Show the health tracker index.
     *
     * @return Redirect to health tracker index page
     */
    public function index(Request $request)
    {
        try {
            $goal = HealthTrackerRepository::getWeightGoal();
            $weightDetail = HealthTrackerRepository::loadHealthDetail($request);
            return view('health-tracker.index', compact('goal' ,'weightDetail')); 
        } catch (\Exception $ex) {
            abort(404);
        }
    }
    
    /**
     * Load Health detail
     *
     * @return Json,Html
     */
    public function loadHealthDetail(Request $request)
    {
        try {
            $userData = getUser();
            $detail = HealthTrackerRepository::loadHealthDetail($request);
            $healthSettings = HealthTrackerRepository::findSetting(['user_id' => $userData->id]);

            if ($request->type == 'dashboard') {
                $view = View::make('dashboard.health-tracker', $detail)->render();
            } else {
                $view = View::make('health-tracker.health-detail', ['detail' => $detail, 'healthSettings' => $healthSettings])->render();
                // $view = view('health-tracker.health-detail', compact('detail'));
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data' => $detail, 'html' => $view],
                    'message' => 'Health tracker detail.',
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
     * Show the health tracker health marker index.
     *
     * @return Redirect to health marker index page
     */
    public function healthMarkerIndex(Request $request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $detail = HealthTrackerRepository::findMarker(['user_id' => $user->id, 'date' => $date], ['images', 'images.media']);
            $setting = HealthTrackerRepository::getAllSettings();
            $adminSettings = SettingRepository::getSettings();
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-health-markers'] , ['reward_game.game']);

            return view('health-tracker.health-marker', compact('detail', 'setting', 'adminSettings','rewardDetail'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save Health Marker
     *
     * @return Json
     */
    public function saveHealthMarker(HealthMarkerRequest $request)
    {
        try {
            $result = HealthTrackerRepository::saveHealthMarker($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Health marker successfully saved.',
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
     * Show the health tracker health marker index.
     *
     * @return Redirect to health marker view page
     */
    public function healthMarkerView()
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $settings = HealthTrackerRepository::getAllSettings();
            $detail = HealthTrackerRepository::findMarker(['user_id' => $user->id, 'date' => $date], ['images', 'images.media']);
            $weightGoal = HealthTrackerRepository::getWeightGoal();

            return view('health-tracker.view-health-marker', compact('detail', 'settings' , 'weightGoal'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Load Health Marker Log
     *
     * @return Json
     */
    public function loadHealthMarkerLog(HealthMarkerRequest $request)
    {
        try {
            $results = HealthTrackerRepository::getHealthMarkerLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Health marker log.',
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
     * Show the health measurement health marker index.
     *
     * @return Redirect to health measurement view page
     */
    public function healthMeasurementView()
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $settings = HealthTrackerRepository::getAllSettings();
            $detail = HealthTrackerRepository::findMeasurement(['user_id' => $user->id, 'date' => $date], ['images', 'images.media']);

            return view('health-tracker.view-health-measurement', compact('detail', 'settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Load Health Marker Log
     *
     * @return Json
     */
    public function loadHealthMeasurementLog(HealthMarkerRequest $request)
    {
        try {
            $results = HealthTrackerRepository::getHealthMeasurementLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Health marker log.',
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
     * Show the health measurement health measurement index.
     *
     * @return Redirect to health measurement index page
     */
    public function healthMeasurementIndex(Request $request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            // $checkinDetail = HealthTrackerRepository::loadHealthDetail($request, 'measurement');
            // $date = ! empty($checkinDetail['measurementNextDate']) ? formatDate($checkinDetail['measurementNextDate'], 'Y-m-d') : $date;
            $detail = HealthTrackerRepository::findMeasurement(['user_id' => $user->id, 'date' => $date]);
            $setting = HealthTrackerRepository::getAllSettings();
            $adminSettings = SettingRepository::getSettings();
            $measurementData = HealthTrackerRepository::getHealthMeasurementValues();
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-health-measurement','status' => 'active',] , ['reward_game.game']);

            return view('health-tracker.health-measurement', compact('detail', 'setting', 'adminSettings', 'measurementData','rewardDetail'));
        } catch (\Exception $ex) {
            // print_r($ex->getMessage());
            // exit;
            abort(404);
        }
    }

    /**
     * Save Health Measurment
     *
     * @return Json
     */
    public function saveHealthMeasurement(HealthMeasurementRequest $request)
    {
        try {
            $result = HealthTrackerRepository::saveHealthMeasurement($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Health measurment successfully saved.',
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
     * Show the health setting index page.
     *
     * @return Redirect to health setting index page
     */
    public function healthSettingIndex()
    {
        try {
            $user = getUser();
            $currentDate = getTodayDate('Y-m-d');
            $reminderData = [];
            $moduleData = [];
            $detail = HealthTrackerRepository::findSetting(['user_id' => $user->id]);
            $setting = HealthTrackerRepository::getAllSettings();
            $notificationTypes = NotificationRepository::getNotificationTypes();
            // Permited module
            // $permissions =  getSidebarPermissions();
            // // $permissions =  getUserModulePermissions();
            
            // foreach($permissions as $permission){
            //     foreach($permission->modules as $module){
            //         if($module->key == 'health-tracker'){
            //             $moduleData = $module; 
            //         }
            //     }
            // }

            $allPermissions = getSidebarPermissions();

           if(!empty($allPermissions) && count($allPermissions) > 0) {
                foreach($allPermissions as $permission) {
                    if($permission['menu_type'] == 'dynamic'){
                        if(!empty($permission['module']) && $permission['show_as_parent']== 1 && $permission['module']['key'] == 'health-tracker'){
                            $moduleData = $permission['module'];
                        }
                    
                    
                    if(!empty($permission['childs']) && count($permission['childs']) > 0){
                        foreach($permission['childs'] as $key => $value) {
                                if(!empty($value['module']) && $value['module']['key'] == 'health-tracker'){
                                    $moduleData = $value['module'];
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($moduleData)){
                $reminderData = NotificationRepository::findOne([['user_id', $user->id],['module_id', $moduleData['id']]]); 
            }
            return view('health-tracker.health-setting', compact('detail', 'setting', 'notificationTypes', 'moduleData', 'reminderData'));
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }

    /**
     * Save Health Setting
     *
     * @return Json
     */
    public function saveHealthSetting(HealthSettingRequest $request)
    {
        try {
            $result = HealthTrackerRepository::saveHealthSetting($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Health measurment successfully saved.',
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
     * Show the health management index.
     *
     * @return Redirect to health measurement index page
     */
    public function healthManagementIndex()
    {
        try {
            $data = HealthTrackerRepository::getHealthMeasurementValues();

            return view('health-tracker.health-management', compact('data'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save health measurement values
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveHealthMeasurementValues(Request $request)
    {
        try {
            $result = HealthTrackerRepository::saveHealthMeasurementValues($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Health measurment successfully updated.',
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
    public function healthtReminderNotificationCron(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return HealthTrackerRepository::healthtReminderNotificationCron($request);
        }, 'Cron run successfully.');
    }

    public function addWeightGoal(WeightGoalRequest $request){
        return $this->handleApiResponse(function () use ($request) {
            return HealthTrackerRepository::addWeightGoal($request);
        }, 'Weight goal added successfully.');
    } 
}
