<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\HealthMarkerRequest;
use App\Http\Requests\Api\HealthMeasurementRequest;
use App\Http\Requests\Api\HealthSettingRequest;
use App\Http\Requests\Api\WeightGoalRequest;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\RewardRepository;
use App\Repositories\SettingRepository;
use App\Repositories\NotificationRepository;
use Config;
use Illuminate\Http\Request;

class HealthTrackerController extends ApiController
{
    /**
     * Get today detail
     *
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        try {
            $detail = HealthTrackerRepository::loadHealthDetail($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $detail,
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
     * Save Health Marker
     *
     * @return \Illuminate\Http\Response
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
     * Save Health Measurment
     *
     * @return \Illuminate\Http\Response
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
     * Show the health tracker health measurement index.
     *
     * @return \Illuminate\Http\Response
     */
    public function healthSettingIndex()
    {
        try {
            $user = getUser();
            $reminderData = [];
            $moduleData = [];
            $detail = HealthTrackerRepository::findSetting(['user_id' => $user->id]);
            $notificationTypes = NotificationRepository::getNotificationTypes();

           $allPermissions = getSidebarPermissions();
            // $permissions =  getUserModulePermissions();

           if(!empty($allPermissions) && count($allPermissions) > 0) {
                foreach($allPermissions as $permission) {
                    if($permission['menu_type'] == 'dynamic'){
                        if(!empty($permission['module'])&& $permission['module']['show_as_parent']== 1 && $permission['module']['key'] == 'health-tracker'){
                            $moduleData = $permission['module'];
                        }
                    
                    
                    if(!empty($permission['childs'] && count($permission['childs']) > 0)){
                        foreach($permission['childs'] as $key => $value) {
                                if($value['module']['key'] == 'health-tracker'){
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

            return response()->json(
                [
                    'success' => true,
                    'data' =>[ 'detail'=>$detail, 'reminderData'=> $reminderData,'notificationTypes'=>$notificationTypes],
                    'message' => 'Health setting listing fetched.',
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
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Load Health Marker Log
     *
     * @return \Illuminate\Http\Response
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
     * Load Health Marker Log
     *
     * @return \Illuminate\Http\Response
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
     * Save Health Setting
     *
     * @return \Illuminate\Http\Response
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
     * Show the health tracker health marker index.
     *
     * @return \Illuminate\Http\Response
     */
    public function healthMarkerIndex(Request $request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $checkinDetail = HealthTrackerRepository::loadHealthDetail($request, 'marker');
            $date = !empty($checkinDetail['markerNextDate']) ? formatDate($checkinDetail['markerNextDate'], 'Y-m-d') : $date;
            $detail = HealthTrackerRepository::findMarker(['user_id' => $user->id, 'date' => $date]);
            $setting = HealthTrackerRepository::getAllSettings();
            $adminSettings = SettingRepository::getSettings();
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-health-markers'] , ['reward_game.game']);
            $gameKey = null;
            if($rewardDetail->is_gamification == 1 && !empty($rewardDetail->reward_game)){
                $game = getDynamicGames($rewardDetail);
                $gameKey = $game['game_key']??null;
            }
            
            return response()->json(
                [
                    'success' => true,
                    'data' => ['detail' => $detail, 'setting' => $setting, 'adminSetting' => $adminSettings,'reward_detail'=>$rewardDetail,'game_key'=>$gameKey],
                    'message' => 'Health measurment successfully saved.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    /**
     * Show the health tracker health measurement index.
     *
     * @return \Illuminate\Http\Response
     */
    public function healthMeasurementIndex(Request $request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $checkinDetail = HealthTrackerRepository::loadHealthDetail($request, 'measurement');
            $date = !empty($checkinDetail['measurementNextDate']) ? formatDate($checkinDetail['measurementNextDate'], 'Y-m-d') : $date;
            $detail = HealthTrackerRepository::findMeasurement(['user_id' => $user->id, 'date' => $date]);
            $setting = HealthTrackerRepository::getAllSettings();
            $measurementData = HealthTrackerRepository::getHealthMeasurementValues();
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-health-measurement','status' => 'active',] , ['reward_game.game']);

            $gameKey = null;
            if($rewardDetail->is_gamification == 1 && !empty($rewardDetail->reward_game)){
                $game = getDynamicGames($rewardDetail);
                $gameKey = $game['game_key']??null;
            }
            return response()->json(
                [
                    'success' => true,
                    'data' => ['detail' => $detail, 'setting' => $setting,'measurementData' => $measurementData,'reward_detail'=>$rewardDetail,'game_key'=>$gameKey],
                    'message' => 'Mesurement detail'
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    
    /**
     * Add weight goal 
     *
     * @return \Illuminate\Http\Response
     */
    public function addWeightGoal(WeightGoalRequest $request)
    {
        try {
            $result = HealthTrackerRepository::addWeightGoal($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Weight goal added successfully.',
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
