<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FitnessSettingRequest;
use App\Http\Requests\Api\FitnessWorkoutCompleteRequest;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\RewardRepository;
use App\Repositories\SettingRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\WorkoutBuilderRepository;
use Config;
use Illuminate\Http\Request;
use View;

class FitnessProfileController extends ApiController
{
    /**
     * Get settings detail
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        try {
            $data = FitnessProfileRepository::getSettings();

            return response()->json(
                [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Settings detail.',
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
     * Save Settings
     *
     * @return \Illuminate\Http\Response
     */
    public function saveSettings(FitnessSettingRequest $request)
    {
        try {
            $result = FitnessProfileRepository::saveSettings($request);


            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Settings successfully saved.',
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
     * Get Today Workout Detail
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodayWorkOutDetail(Request $request)
    {
        try {
            $results = FitnessProfileRepository::getTodayWorkOutDetail($request);
            // echo '<pre>';
            // print_r($results['workoutData']);
            // exit;
            $nextWorkoutDate = FitnessProfileRepository::getNextWorkoutDate($request);
            $completeWorkoutReward = RewardRepository::findRewardManagement([['feature_key', '=', 'complete-workout']]);
            // $dayOff = $results['workoutData']->filter(function ($value) {
            //     return $value['value'] == 'DAY_OFF';
            // });


            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        // 'html' => $view,
                        //'dayOff' => $dayOff,
                        'workout' => $results['workoutData'],
                        'isNewUser' => $results['isNewUser'],
                        'customExercises' => $results['customWorkoutData'],
                        'todayPendingWorkout' => $results['todayPendingWorkout'],
                        'nextDateArr' => $nextWorkoutDate,
                        'completeWorkoutReward' => $completeWorkoutReward,
                        'weekData' => $results['weekData'],
                    ],
                    'message' => 'Today workout detail.',
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
     * Get Workout Report
     *
     * @return \Illuminate\Http\Response
     */
    public function getWorkOutReport(Request $request)
    {
        try {
            $results = FitnessProfileRepository::getWorkOutReport($request);
            $rewardDetail =  RewardRepository::findRewardManagement(['feature_key' => 'complete-workout','status'=>'active'],['reward_game.game']);
            $gameKey=null;
            if($rewardDetail->is_gamification == 1 && !empty($rewardDetail->reward_game)){
                $game = getDynamicGames($rewardDetail);
                $gameKey = $game['game_key']??null;
            }
            return response()->json(
                [
                    'success' => true,
                    'data' => ['result'=>$results,'reward_detail'=>$rewardDetail,'game_key'=>$gameKey],
                    'message' => 'Workout report.',
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
     * Save Workout
     *
     * @return \Illuminate\Http\Response
     */
    public function markComplete(FitnessWorkoutCompleteRequest $request)
    {
        try {
            $result = FitnessProfileRepository::markComplete($request);
            $workoutReward = RewardRepository::findRewardManagement([['feature_key', '=', 'complete-workout']],['reward_game.game']);
            $message = 'Workout successfully added.';
            if (!empty($workoutReward) && $workoutReward->is_gamification == 0) {
                $message = '"Congratulations!!  You have earned  '.$workoutReward['point'].' points for complete a workout."';
            }elseif(!empty($workoutReward) && $workoutReward->is_gamification == 1 && !empty($workoutReward->reward_game)){
                $message = "Congratulations!!  You will earn reward for complete a workout after playing a game.";
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => $message,
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

    // /**
    //  * Get Workout Report
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function getWorkOutReport(Request $request)
    // {
    //     try {
    //         $userData = getUser();
    //         $results = FitnessProfileRepository::findAllProfileDetail([['user_id', $userData->id], ['date', '<=', $request->date]]);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $results,
    //                 'message' => 'Workout report.',
    //             ],
    //             Config::get('constants.HttpStatus.OK')
    //         );
    //     } catch (\Exception $ex) {
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'data' => '',
    //                 'message' => $ex->getMessage(),
    //             ],
    //             Config::get('constants.HttpStatus.BAD_REQUEST')
    //         );
    //     }
    // }

    /**
     * Get Workout Report
     *
     * @return \Illuminate\Http\Response
     */
    public function saveLogCron(Request $request)
    {
        try {
            $results = FitnessProfileRepository::saveLogCron($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Workout log created.',
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
     * Get Workout Report
     *
     * @return \Illuminate\Http\Response
     */
    public function loadWorkoutLog(Request $request)
    {
        try {
            $results = FitnessProfileRepository::loadWorkoutLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Workout report.',
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

    public function settingsIndex(Request $request)
    {
        try {
            $userData = getUser();
            $moduleData = [];
            $reminderData = [];
           // $days = json_decode(file_get_contents(base_path('resources/views/fitness-profile/days-data.json')), true);
            $data = FitnessProfileRepository::getSettings('', ['staticExercises', 'customExercises']);
            $settings = SettingRepository::getSettings();
            $notificationTypes = NotificationRepository::getNotificationTypes();
            $exercises = FitnessProfileRepository::getUserExercises();
            $workouts = WorkoutBuilderRepository::loadAllWorkoutList($request);
            $myWorkouts = WorkoutBuilderRepository::loadWorkouts('my_workout');
            $availableWorkouts = WorkoutBuilderRepository::loadWorkouts('available_workout');
            // Permited module
            $allPermissions =  getSidebarPermissions();
            // $permissions =  getUserModulePermissions();
            if(!empty($allPermissions) && count($allPermissions) > 0) {
                foreach($allPermissions as $permission) {
                    if($permission['menu_type'] == 'dynamic'){
                        if(!empty($permission['module']) && $permission['module']['show_as_parent']== 1 && $permission['module']['key'] == 'fitness-profile'){
                            $moduleData = $permission['module'];
                        }
                    
                    
                    if(!empty($permission['childs'] && count($permission['childs']) > 0)){
                        foreach($permission['childs'] as $key => $value) {
                                if($value['module']['key'] == 'fitness-profile'){
                                    $moduleData = $value['module'];
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($moduleData)){
                $reminderData = NotificationRepository::findOne([['user_id', $userData->id],['module_id', $moduleData['id']]]);
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => ['days' => [], 'data' => $data,'reminderData'=>$reminderData,'notificationTypes'=>$notificationTypes, 'exercises'=>$exercises,'my_workouts'=>$myWorkouts,'available_workouts'=>$availableWorkouts,'workouts'=>$workouts],
                    'settings' => $settings,
                    'message' => 'Data fetch successfully.',
                ]);
            Config::get('constants.HttpStatus.OK');
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }


    
    /**
     *  Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadFitnessCalendarData(Request $request)
    {
        try {
            $results = FitnessProfileRepository::loadFitnessCalendarData($request);

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
