<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\FoodTrackerRepository;
use App\Http\Requests\FoodSettingRequest;
use App\Http\Requests\UserMealRequest;

use Config;
use View;


class FoodTrackerController extends Controller
{
     /**
     * Show the health tracker index.
     *
     * @return Redirect to health tracker index page
     */
    public function index()
    {
        try {
            $user = getUser();
            $mealContentStatus = FoodTrackerRepository::findMealContentStatus(['user_id'=>$user->id,'status'=>'active']);
            return view('health-tracker.food-tracker.index',compact('mealContentStatus'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Load Food detail
     *
     * @return Json,Html
     */
    public function loadFoodDetail(Request $request)
    {
        try {
            $userData = getUser();
            $request->defaultSettings = json_decode(file_get_contents(base_path('resources/views/health-tracker/food-tracker/settings-data.json')), true);
            $detail = FoodTrackerRepository::loadFoodDetail($request);            
            $view = View::make('health-tracker.food-tracker.food-detail', ['detail' => $detail])->render();
            return response()->json(
                [
                    'success' => true,
                    'data' => ['data' => $detail, 'html' => $view],
                    'message' => 'Food tracker detail.',
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

    public function foodSettingIndex()
    {
        try {
            $user = getUser();
            $currentDate = getTodayDate('Y-m-d');
           
            $detail = FoodTrackerRepository::findSetting(['user_id'=>$user->id,'status'=>'active']);
            $meals = FoodTrackerRepository::findAllMeals();
            $setting = FoodTrackerRepository::findAllSetting(['user_id'=>$user->id,'status'=>'active']);
            $userMealContentStatus = FoodTrackerRepository::findMealContentStatus(['user_id'=>$user->id,'status'=>'active']);
            $selectedMeals = $setting->pluck('meal_master_id')->toArray();

            return view('health-tracker.food-tracker.food-setting', compact(
                'detail', 
                'setting','meals','selectedMeals','userMealContentStatus'));
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }

    /**
     * Save Food Setting
     *
     * @return Json
     */
    public function saveFoodSetting(FoodSettingRequest $request)
    {
        try {
            
            $result = FoodTrackerRepository::saveFoodSetting($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Food Settings successfully saved.',
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
     * Save User Meals Details
     *
     * @return Json
     */
    public function saveUserMeals(UserMealRequest $request)
    {
        try {
            $meal = FoodTrackerRepository::findSingleMeal($request);
            $result = FoodTrackerRepository::saveUserMeals($request);
            

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => !empty($meal)? 'Updated Successfully.':'Saved successfully.',
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
     * Load Food status
     *
     * @return Json,Html
     */

     public function displayUserFoodStatus(Request $request)
     {
        try{
            $report = FoodTrackerRepository::displayUserFoodStatus($request);
            $range = $request->range;
            $view = View::make('health-tracker.food-tracker.food-status',['report'=> $report,'range'=> $range])->render();

            return response()->json(
                        [
                            'success' => true,
                            'data' => ['data' => $report, 'html' => $view],
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
     * Load Single Meal Data for User According to Date
     *
     * @return Json,Html
     */
    public function getSingleMeal(Request $request)
    {
        try {
            $userData = getUser();
            $meal = FoodTrackerRepository::findSingleMeal($request);            
            return response()->json(
                [
                    'success' => true,
                    'data' => $meal,
                    'message' => 'Single Meal',
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
