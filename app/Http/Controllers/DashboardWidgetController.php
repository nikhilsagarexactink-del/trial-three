<?php

namespace App\Http\Controllers;

use App\Repositories\BroadcastRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DashboardWidgetRepository;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\FoodTrackerRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\RecipeRepository;
use App\Repositories\RewardRepository;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\WaterTrackerRepository;
use App\Repositories\HeaderTextRepository;
use App\Repositories\UserRepository;
use App\Repositories\SpeedRepository;
use App\Repositories\MotivationSectionRepository;
use App\Repositories\BaseballRepository;
use App\Repositories\WorkoutBuilderRepository;
use App\Repositories\FitnessChallengeRepository;
use App\Models\User;

use Config;
use Illuminate\Http\Request;
use View;

class DashboardWidgetController extends Controller
{
    //  Admin Side
    public function index(Request $request)
    {
        return view('manage-widget.index');
    }

    public function getWidgets(Request $request)
    {
        try {
            $masterWidgets = DashboardWidgetRepository::getWidgets($request);              
            $view = View::make('manage-widget._list', ['data' => $masterWidgets])->render();


            //    $pagination = getPaginationLink($masterWidgets);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view],
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

    public function changeStatus(Request $request)
    {
        try {
            $result = DashboardWidgetRepository::changeStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Status successfully updated.',
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
    //  User Side

    public function customizeDashboard(Request $request)
    {

        $planCount = 0;
        $userCount = 0;
        $userData = getUser();
        if (! empty($userData->quote_id)) {
            $quote = QuoteRepository::findOne([['id', $userData->quote_id]]);
        } else {
            $quote = QuoteRepository::setUserQuote($userData);
        }
        $request->userType = userType();
        $trainings = TrainingVideoRepository::loadListForUser($request);
        $recipes = RecipeRepository::loadListForUser($request);
        $userRedeemData = RewardRepository::getUserRedeemData();
        $broadcastAlert = BroadcastRepository::getDashboardAlert();
        $currentDay = strtoupper(getLocalDateTime('', 'l'));
       
        
        $userDashboard = DashboardWidgetRepository::getDynamicDashboard($request);
        $athletes = UserRepository::loadAthleteList($request);
        $categories_motivation = CategoryRepository::findAll([['status', 'active'], ['type', 'motivation-section']]);

        
        $allowedWidgets = collect(DashboardWidgetRepository::getWidgets($request))->pluck('widget_key')->toArray();
        $request->defaultSettings = json_decode(file_get_contents(base_path('resources/views/health-tracker/food-tracker/settings-data.json')), true);
        $foodDetail = FoodTrackerRepository::loadFoodDetail($request);
        $userMealSettingData = $foodDetail['setting']->pluck('meal_master_id')->toArray();
        $foodSettings = FoodTrackerRepository::findSetting(['user_id' => $userData->id]);
        $allMeals = FoodTrackerRepository::findAllMeals();

        // dd($categories_motivation);

        
        $isCustomize = true;
        $toggleInput = false;
        $dashboardName = '';

        if ($request->has('dashboardName')) {
            $dashboardName = $request->dashboardName;
        } elseif (! empty($userDashboard)) {
            $dashboardName = $userDashboard->dashboard_name;
        } else {
            $dashboardName = 'Dashboard';
        }

        return view('customize-dashboard.index', compact('planCount', 'userCount', 'quote', 'broadcastAlert', 'userRedeemData', 'currentDay', 'toggleInput', 'dashboardName', 'trainings', 'recipes', 'userDashboard','isCustomize','allowedWidgets','athletes'));
    }

    public function getActiveWidgets(Request $request)
    {
        try {

            $masterWidgets = DashboardWidgetRepository::getWidgets($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $masterWidgets,
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

    public function displayActiveWidgets(Request $request)
    {
        try {
            $athlete_id = $request->athlete_id;
            $userData = getUser ();
            $currentDate = getTodayDate('Y-m-d');
            
            $userId = $athlete_id? $athlete_id:$userData->id; 
            $athlete = UserRepository::findOne(['id' => $athlete_id]);

            $widgetsData = DashboardWidgetRepository::displayActiveWidgets($request);
            $totalRewardPoints = $userData->total_reward_points ?? 0;
            $totalCarts = RewardRepository::userCarts();
            $userTotalEarning = RewardRepository::userTotalReward([['user_id', $userId]]);
            $userTodayEarning = RewardRepository::userTodayReward([['user_id', $userId]]);
            $userMonthlyEarning = RewardRepository::userMonthlyReward([['user_id', $userId]]);
            $userDetail = UserRepository::findOne([['id', $userId]]);
            $userTotalVideoWatched = TrainingVideoRepository::UserVideoWatched(['user_id' => $userId]);
            $UniqueUserRecipies = UserRepository::UniqueUserRecipies(['user_id'=>$userId]);
            // $VideoWatchedlist = TrainingVideoRepository::UserVideoWatchedlist(['user_id'=>$userData->id]);
            $UserRecipiesUsed =  UserRepository::UserRecipiesUsed(['user_id'=>$userId]);
            $UserRecipiesReview = RecipeRepository::UserReviewCount(['user_id'=>$userId]);
            // $userTotalRedeemed = RewardRepository::userTotalRedeemed([['user_id', $userData->id]]);
            $settings = SpeedRepository::getSettings($request);
            $motivation_loadlist = MotivationSectionRepository::loadList($request);
            
            $sportResult = BaseballRepository::loadPracticeList($request);
            $userWorkoutList  = WorkoutBuilderRepository::loadAllWorkoutList($request);
            $weekData = FitnessProfileRepository::getTodayWorkOutDetail($request); 
            $results = FitnessProfileRepository::getWorkOutReport($request);            
            $water = WaterTrackerRepository::getActivityLog($request);
            $detail = HealthTrackerRepository::loadHealthDetail($request);
            $categories_motivation = CategoryRepository::findAll([['status', 'active'], ['type', 'motivation-section']]);                        
            $categories_gettingStarted = CategoryRepository::findAll([['status', 'active'], ['type', 'getting-started']],['gettingStartedVideos']); 
            $foodReport = FoodTrackerRepository::displayUserFoodStatus($request);
            $mealContentStatus = FoodTrackerRepository::findMealContentStatus(['user_id'=>$userId,'status'=>'active']);

            $sportView = View::make('baseball.practice._list', ['data' => $sportResult])->render();
            $allAthletes = UserRepository::loadAthleteList($request);
            
            $selectedWidgets = $request->widgets??[];
            $user = $request->user_data;
            $currentDay = $request->currentDay;
            $water = WaterTrackerRepository::getActivityLog($request);
            $detail = HealthTrackerRepository::loadHealthDetail($request);
            $results = FitnessProfileRepository::getWorkOutReport($request);
            $isCustomize = $request->isCustomize;$categories_motivation = CategoryRepository::findAll([['status', 'active'], ['type', 'motivation-section']]);$categories_motivation = $request->categories_motivation;
            $categories_gettingStarted = CategoryRepository::findAll([['status', 'active'], ['type', 'getting-started']],['gettingStartedVideos']);
            
            $widgetViews = $widgetsData;
            $weekData = FitnessProfileRepository::getTodayWorkOutDetail($request);
            $trainings = TrainingVideoRepository::loadListForUser($request);
            $recipes = RecipeRepository::loadListForUser($request);
            $allowedWidgets = DashboardWidgetRepository::getWidgets($request);
            $allowedWidgetKeys = collect($allowedWidgets)->pluck('widget_key');



            $workoutsGoal = WorkoutBuilderRepository::findAllGoal([['status', '=', 'active']]);
            $userGoals = WorkoutBuilderRepository::getUserGoals($request);
           // $myGoal = WorkoutBuilderRepository::getUserCurrentGoal();
            $myGoal = WorkoutBuilderRepository::getTodayWorkoutGoal($request);

            $widgets = collect($selectedWidgets)->intersect($allowedWidgetKeys)->unique();
            
            if ($userData->user_type == 'parent' && $athlete_id != $userData->id) {
                foreach (['login-activity', 'athletes-rewards'] as $key) {
                    if (in_array($key, $selectedWidgets)) {
                        if (! $widgets->contains($key)) {
                            $widgets->push($key);
                        }
                    } else {
                        $widgets = $widgets->reject(fn($widget) => $widget === $key);
                    }
                }
            }
            
            $printedWidgets = [];
            if (! empty($widgets)) {
                foreach ($widgets as $widgetKey) {
                    if (isset($widgetViews[$widgetKey])) {
                            $widgetContent = View::make($widgetViews[$widgetKey], [
                                'widget_key' => $widgetKey,
                                'trainings' => $trainings,
                                'workouts' => $userWorkoutList,
                                'recipes' => $recipes,
                                'detail' => $detail,
                                'weekData' => $weekData,
                                'results' => $results,
                                'water' => $water,
                                'categories_motivation' => $categories_motivation,
                                'categories_gettingStarted' => $categories_gettingStarted,
                                'isCustomize' => $isCustomize,
                                'totalRewardPoints'=> $totalRewardPoints,
                                'totalCarts'=>$totalCarts,
                                'userTotalEarning'=>$userTotalEarning,
                                'userDetail'=>$userDetail,
                                'userTodayEarning'=>$userTodayEarning,
                                'settings'=>$settings,
                                'motivation_loadlist' => $motivation_loadlist,
                                'sportView' => $sportView,
                                'sportResult' => $sportResult,
                                'userMonthlyEarning' => $userMonthlyEarning,
                                'userTotalVideoWatched'=>$userTotalVideoWatched,
                                'uniqueUserRecipies'=>$UniqueUserRecipies,
                                // 'videoWatchedlist'=>$VideoWatchedlist,
                                'userRecipiesUsed'=>$UserRecipiesUsed,
                                'userRecipiesReview'=> $UserRecipiesReview,
                                'athlete_id' => $athlete_id,
                                'athlete' => $athlete,
                                'allAthletes' => $allAthletes,
                                'foodReport' => $foodReport,
                                'mealContentStatus'=>$mealContentStatus,
                                'workoutsGoal' => $workoutsGoal,
                                'userGoals' => $userGoals,
                                'myGoal' => $myGoal,
                            ])->render();
                            if(count($widgets) > count($printedWidgets)){
                                $printedWidgets[]= $widgetContent;
                        } 
                    }
                }
            }
            return response()->json(
                [
                    'success' => true,
                    'data' => array_unique($printedWidgets),
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
     * save the dashboard Name.
     *
     * @return Redirect to dashboard page with updated dashboard.
     */
    public function saveDashboard(Request $request)
    {
        try {
            $dashboard = DashboardWidgetRepository::saveDashboard($request);
            session(['dashboardName' => 'Dashboard']);

            return response()->json(
                [
                    'success' => true,
                    'data' => $dashboard,
                    'message' => 'Dashboard saved Successfully',
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

    public function getDynamicDashboard(Request $request)
    {
        try {

            $userDashboard = DashboardWidgetRepository::getDynamicDashboard($request);
        

            return response()->json(
                [
                    'success' => true,
                    'data' => $userDashboard,
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
