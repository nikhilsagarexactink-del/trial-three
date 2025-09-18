<?php

namespace App\Http\Controllers;

use App\Http\Requests\FitnessSettingRequest;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\RewardRepository;
use App\Repositories\SettingRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\WorkoutBuilderRepository;
use Config;
use Illuminate\Http\Request;
use View;

class FitnessProfileController extends BaseController
{
    /**
     * Show the fitness profile index page.
     *
     * @return Redirect to fitness profile index page
     */
    public function index()
    {
        try {
            $rewardDetail =  RewardRepository::findRewardManagement(['feature_key' => 'complete-workout','status'=>'active'],['reward_game.game']);
            return view('fitness-profile.index',compact('rewardDetail'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function showTimer()
    {
        try {
            return view('fitness-profile.timer');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the setting index page.
     *
     * @return Redirect to setting index page
     */
    public function settingsIndex(Request $request)
    {
        try {
            $userData = getUser();
            $reminderData = [];
            $moduleData = [];
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            //$days = json_decode(file_get_contents(base_path('resources/views/fitness-profile/days-data.json')), true);
            $data = FitnessProfileRepository::getSettings('', ['staticExercises', 'customExercises']);
            $settings = SettingRepository::getSettings();
            $notificationTypes = NotificationRepository::getNotificationTypes();
            $exercises = FitnessProfileRepository::getUserExercises();
            $daySettings = FitnessProfileRepository::findProfileDaySetting(['user_id'=>$userData->id]);

            $myWorkouts = WorkoutBuilderRepository::loadWorkouts('my_workout');
            $availableWorkouts = WorkoutBuilderRepository::loadWorkouts('available_workout');
            // Permited module
            $allPermissions =  getSidebarPermissions();

            if (!empty($allPermissions) && count($allPermissions) > 0) {
                foreach ($allPermissions as $permission) {
                    if ($permission['menu_type'] == 'dynamic') {
                        if (!empty($permission['module']) && $permission['module']['show_as_parent'] == 1 && $permission['module']['key'] == 'fitness-profile') {
                            $moduleData = $permission['module'];
                        }

                        // Check if 'childs' exists and is an array
                        if (!empty($permission['childs']) && is_array($permission['childs']) && count($permission['childs']) > 0) {
                            foreach ($permission['childs'] as $key => $value) {
                                // Check if 'module' exists and is an array
                                if (isset($value['module']) && isset($value['module']['key']) && $value['module']['key'] == 'fitness-profile') {
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
            return view('fitness-profile.settings', compact('data', 'settings','daySettings','moduleData', 'notificationTypes','reminderData','exercises','myWorkouts', 'availableWorkouts'));
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
            abort(404);
        }
    }

    /**
     * Save Settings
     *
     * @return Json
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
     * Save Settings Exercise
     *
     * @return Json
     */
    public function saveSettingCustomExercise(Request $request)
    {
        try {
            $result = FitnessProfileRepository::saveSettingCustomExercise($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Settings successfully updated.',
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
     * Save Settings Exercise
     *
     * @return Json
     */
    public function saveSettingStaticExercise(Request $request)
    {
        try {
            $result = FitnessProfileRepository::saveSettingStaticExercise($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Settings successfully updated.',
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
     * @return Redirect to workout detail page
     */
    public function getTodayWorkOutDetail(Request $request)
    {
        try {
            $results = FitnessProfileRepository::getTodayWorkOutDetail($request);
            $nextWorkoutDate = FitnessProfileRepository::getNextWorkoutDate($request);
            $completeWorkoutReward = RewardRepository::findRewardManagement([['feature_key', '=', 'complete-workout']]);
            //echo '<pre>';
            //print_r($results['customWorkoutData']);die;
            //print_r($results['workoutData']->toArray());die;
            $view = View::make('fitness-profile._workout', [
                'data' => $results['workoutData'],
                'customExercises' => $results['customWorkoutData'],
                'isNewUser' => $results['isNewUser'],
                'completeWorkoutReward' => $completeWorkoutReward,
                //'dayOff' => $dayOff,
                'todayPendingWorkout' => $results['todayPendingWorkout'],
                'weekData' => $results['weekData'],
            ])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'html' => $view,
                        //'dayOff' => $dayOff,
                        'workout' => $results['workoutData'],
                        'isNewUser' => $results['isNewUser'],
                        'todayPendingWorkout' => $results['todayPendingWorkout'],
                        'nextDateArr' => $nextWorkoutDate,
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
     * @return Json
     */
    public function getWorkOutReport(Request $request)
    {
        try {
            $results = FitnessProfileRepository::getWorkOutReport($request);

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

    /**
     * Complete fitness profile workout
     *
     * @return Json
     */
    public function markComplete(Request $request)
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

    /**
     * Get Workout Report
     *
     * @return Json
     */
    public function gerWoroutReport(Request $request)
    {
        try {
            $userData = getUser();
            $results = FitnessProfileRepository::findAllProfileDetail([['user_id', $userData->id], ['date', '<=', $request->date]]);

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

    /**
     * Save log cron
     *
     * @return Json
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
     * Get workout log data
     *
     * @return Json
     */
    public function loadWorkoutLog(Request $request)
    {
        try {
            $results = FitnessProfileRepository::loadWorkoutLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results, //['html' => $view, 'pagination' => $pagination],
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
     * Run workout reminder notification cron
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function workoutReminderNotificationCron(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return FitnessProfileRepository::workoutReminderNotificationCron($request);
        }, 'Cron run successfully.');
    }

     /**
     * Show the calendar page.
     *
     * @return Redirect to calendar page
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

     /**
     * Show the calendar page.
     *
     * @return Redirect to calendar page
     */
    public function deleteFitnessExercise(Request $request)
    {
        try {
            $results = FitnessProfileRepository::deleteFitnessExercise($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Exercise successfully deleted',
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
