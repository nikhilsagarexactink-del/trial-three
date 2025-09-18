<?php

namespace App\Http\Controllers;

use App\Repositories\BroadcastRepository;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\RecipeRepository;
use App\Repositories\RewardRepository;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\WaterTrackerRepository;
use App\Repositories\DashboardWidgetRepository;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\UpsellRepository;
use App\Repositories\UserRepository;
use App\Repositories\FoodTrackerRepository;


use Config;
use Illuminate\Http\Request;
use View;

class DashboardController extends Controller
{
    /**
     * Show the quote index page.
     *
     * @return Redirect to quote index page
     */
    public function index(Request $request)
    {
        try {
            $planCount = 0;
            $userCount = 0;
            $userData = getUser();
            $request->userType = userType();

            $request->location = 'popup_after_login';
            if (! empty($userData->quote_id)) {
                $quote = QuoteRepository::findOne([['id', $userData->quote_id]]);
            } else {
                $quote = QuoteRepository::setUserQuote($userData);
            }
            $userRedeemData = RewardRepository::getUserRedeemData();
            $broadcastAlert = BroadcastRepository::getDashboardAlert();
            $currentDay = strtoupper(getLocalDateTime('', 'l'));
            $request->defaultSettings = json_decode(file_get_contents(base_path('resources/views/health-tracker/food-tracker/settings-data.json')), true);
            $foodDetail = FoodTrackerRepository::loadFoodDetail($request);
            $userMealSettingData = $foodDetail['setting']->pluck('meal_master_id')->toArray();
            $foodSettings = FoodTrackerRepository::findSetting(['user_id' => $userData->id]);
            $allMeals = FoodTrackerRepository::findAllMeals();
            $userDashboard = null;
            $userDashboard = DashboardWidgetRepository::getDynamicDashboard($request);
            $upsells = UpsellRepository::loadUpsellList($request);
            $athletes = UserRepository::loadAthleteList($request);
            $widgetKeys = [];
            
            $allowedWidgets = collect(DashboardWidgetRepository::getWidgets($request))->pluck('widget_key')->toArray();
            // dd($allowedWidgets);
            if(! empty($userDashboard)){
                foreach($userDashboard->widgets as $widget){
                    $widgetKeys[] = $widget->widget->widget_key;
                }
            }
            // dd($widgetKeys);

            return view('dashboard', compact('userDashboard', 'widgetKeys', 'planCount', 'userCount', 'quote', 'broadcastAlert','upsells','userRedeemData','athletes', 'currentDay','allowedWidgets'));
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
     * Get activity log
     *
     * @return Json,Html
     */
    public function loadDashboardActivityList(Request $request)
    {
        try {
            $currentDay = strtoupper(getLocalDateTime('', 'l'));
            $water = WaterTrackerRepository::getActivityLog($request);
            $results = FitnessProfileRepository::getTodayWorkOutDetail($request);
            $allowedWidgets = $request->allowedWidgets;
            $fitnessWeekData = View::make('dashboard.fitness-week-log', ['weekData' => $results['weekData'], 'currentDay' => $currentDay,'allowedWidgets'=>$allowedWidgets])->render();
           
            return response()->json(
                [
                    'success' => true,
                    'data' => ['water' => $water, 'fitnessWeekData' => $fitnessWeekData, 'weekData' => $results['weekData'],'allowedWidgets'=>$allowedWidgets],
                    'message' => 'Activity log.',
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
     * Get latest training and recipe
     *
     * @return Json,Html
     */
    public function getLatestTrainingRecipe(Request $request)
    {
        try {
            $trainings = TrainingVideoRepository::loadListForUser($request);
            $recipes = RecipeRepository::loadListForUser($request);
            $allowedWidgets = $request->allowedWidgets;
            $view = View::make('dashboard.training-recipe', compact('trainings', 'recipes','allowedWidgets'))->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view],
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
}
