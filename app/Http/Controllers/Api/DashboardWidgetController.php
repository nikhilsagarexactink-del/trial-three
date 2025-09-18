<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\DashboardWidgetRepository;
use App\Repositories\WaterTrackerRepository;
use App\Repositories\WorkoutBuilderRepository;
use App\Repositories\RecipeRepository;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\RewardRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SpeedRepository;
use App\Repositories\BaseballRepository;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\UserRepository;
use App\Repositories\MotivationSectionRepository;
use App\Repositories\FoodTrackerRepository;
use App\Repositories\FitnessChallengeRepository;



use Config;
use Illuminate\Http\Request;


class DashboardWidgetController  extends ApiController
{
    public function getDynamicDashboard(Request $request){
        try {
            $athlete_id = $request->athlete_id;
            $userData = getUser ();
            

            $userId = $athlete_id? $athlete_id:$userData->id; 
            $athlete = UserRepository::findOne(['id' => $athlete_id]);
            // Get all widgets
            $widgets = DashboardWidgetRepository::getDynamicDashboard($request);
            $userTotalEarning = RewardRepository::userTotalReward([['user_id', $userId]]);
            $userTodayEarning = RewardRepository::userTodayReward([['user_id', $userId]]);
            $userMonthlyEarning = RewardRepository::userMonthlyReward([['user_id', $userId]]);

            $userTotalVideoWatched = TrainingVideoRepository::UserVideoWatched(['user_id' => $userId]);
            $UniqueUserRecipies = UserRepository::UniqueUserRecipies(['user_id'=>$userId]);
            // $VideoWatchedlist = TrainingVideoRepository::UserVideoWatchedlist(['user_id'=>$userData->id]);
            $UserRecipiesUsed =  UserRepository::UserRecipiesUsed(['user_id'=>$userId]);
            $UserRecipiesReview = RecipeRepository::UserReviewCount(['user_id'=>$userId]);

            $request->defaultSettings = json_decode(file_get_contents(base_path('resources/views/health-tracker/food-tracker/settings-data.json')), true);
            $foodDetail = FoodTrackerRepository::loadFoodDetail($request);
            $foodReport = FoodTrackerRepository::displayUserFoodStatus($request);
            $workoutGoals = WorkoutBuilderRepository::getWorkoutGoalDetail($request);
            $workouts = $workoutGoals['workouts'];
            $completedWorkouts= collect($workouts?$workouts->where('is_completed',1):"")->pluck('is_completed');
            $totalCompletedWorkout = $completedWorkouts->count();
            $allAthletes = UserRepository::loadAthleteList($request);
            $showLeaderboard = FitnessChallengeRepository::getChallengeLeaderboard();
            if (empty($showLeaderboard) && isset($widgets->widgets)) {
                $widgets->widgets = $widgets->widgets->reject(function ($widget) {
                    return $widget->widget_key === 'leaderboard';
                })->values(); // reindex the collection if needed
            }



            $rewards = [
                'total' =>  $userTotalEarning,
                'today' => $userTodayEarning,
                'month' => $userMonthlyEarning 
            ];

            $activities = [
                'unique_Recipies' =>  $UniqueUserRecipies,
                'recipe_review' =>    $UserRecipiesReview,
                'recipe_used' =>  $UserRecipiesUsed,
                'video_watched' =>  $userTotalVideoWatched
            ];

            $motivation = [
                'categories' => CategoryRepository::findAll([['status', 'active'], ['type', 'motivation-section']]),
                'mativation_data'=> MotivationSectionRepository::loadList($request),
                
            ];

            $speed = [
                'tabs' => SpeedRepository::getSettings($request),
                'speed_data'=> SpeedRepository::loadList($request),
            ];
            $workoutGoal = [
                'completedWorkouts'=> $totalCompletedWorkout,
                'workoutsGoal' => WorkoutBuilderRepository::findAllGoal([['status', '=', 'active']]),
                'userGoals' => WorkoutBuilderRepository::getUserGoals($request),
                'myGoal' => $workoutGoals,
            ];


            // Initialize an empty array for widget data
            $widgetData = [];

            // Loop through widgets and call only necessary repositories with different keys
            foreach ($widgets->widgets as $widget) {
                switch ($widget->widget['widget_key']) {
                    case 'water-tracker':
                        $widgetData['water'] = WaterTrackerRepository::getActivityLog($request);
                        break;
                        case 'my-workouts':
                            $widgetData['workouts'] =  WorkoutBuilderRepository::loadAllWorkoutList($request);
                            break;
                            case 'new-recipes':
                                $widgetData['recipes'] = RecipeRepository::loadListForUser($request);
                                break;
                                case 'health-tracker':
                                    $widgetData['healths'] = HealthTrackerRepository::loadHealthDetail($request);
                                    break;
                                    case 'progress-pictures':
                                        $widgetData['fitness'] =  FitnessProfileRepository::getWorkOutReport($request);
                                        break;
                                        case 'my-rewards':
                                            $widgetData['rewards'] =  $rewards;
                                            break;
                                            case 'getting-started':
                                                $widgetData['getting_starteds'] =  CategoryRepository::findAll([['status', 'active'], ['type', 'getting-started']],['gettingStartedVideos']);
                                                break;
                                                case 'speed':
                                                    $widgetData['speeds'] = $speed;
                                                    break;
                                                    case 'workouts':
                                                        $widgetData['week_data'] =   FitnessProfileRepository::getTodayWorkOutDetail($request);
                                                        break;
                                                        case 'sports':
                                                            $request->perPage = 1;
                                                            $widgetData['sports'] =     BaseballRepository::loadPracticeList($request);
                                                            break;
                                                            case 'motivation':
                                                                $widgetData['motivations'] = $motivation;
                                                                break;
                                                                case 'activity-tracker':
                                                                    $widgetData['activity_trackers'] =   $activities;
                                                                    break;
                                                                    case 'food-tracker':
                                                                        $widgetData['food_tracker'] = $foodReport;
                                                                        break;
                                                                        case 'workout-goal':
                                                                            $widgetData['workout_goal']=$workoutGoal;
                                                                            break;
                                                                            case 'login-activity':$widgetData['login_activity']= $allAthletes;
                                                                            break;
                                                                            case 'athletes-rewards':$widgetData['athletes_rewards']= $allAthletes;
                                                                            break;
                                                                            case 'leaderboard' : $widgetData['leaderboard']  = $showLeaderboard;

                    default:
                        // If the widget_id doesn't match predefined cases, you can ignore or log it
                        break;
                }
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $widgetData,
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
