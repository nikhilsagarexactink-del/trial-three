<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request\ApiRequest;
use App\Repositories\FoodTrackerRepository;
use App\Http\Requests\Api\FoodSettingRequest;
use App\Http\Requests\Api\UserMealRequest;
use Illuminate\Http\Request;
use Config;


class FoodTrackerController extends ApiController
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
            return $mealContentStatus;
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
            
            return response()->json(
                [
                    'success' => true,
                    'data' => $detail,
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

            return response()->json(
                [
                    'success' => true,
                    'data' => ['singleSetting'=> $detail, 'settings'=>$setting, 'user_meal_content_status'=> $userMealContentStatus, 'meals'=>$meals],
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

     public function getFoodSettings(Request $request)
     {
        try{
            $user = getUser();
            $setting = FoodTrackerRepository::findAllSetting(['user_id'=>$user->id,'status'=>'active']);
            $userMealContentStatus = FoodTrackerRepository::findMealContentStatus(['user_id'=>$user->id,'status'=>'active']);
            $range = $request->range;

            return response()->json(
                [
                    'success' => true,
                    'data' => ['settings' => $setting, 'range' => $range, 'user_meal_content_status'=>$userMealContentStatus],
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

    /**
    * Load Food status
    *
    * @return Json,Html
    */

    public function displayUserFoodStatus(Request $request)
    {
        try{
            $report = FoodTrackerRepository::displayUserFoodStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $report,
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
