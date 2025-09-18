<?php

namespace App\Repositories;

use App\Models\MealMaster;
use App\Models\UserMealSetting;
use App\Models\UserMealContentStatus;
use App\Models\UserMeal;
use App\Services\ChallengeLogger;
use DB;
use Exception;
use Carbon\Carbon;

class FoodTrackerRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthSetting
     */
    public static function findSetting($where, $with = [])
    {
        return UserMealSetting::with($with)->where($where)->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  MealSetting
     */
    public static function findAllSetting($where, $with = [])
    {
        return UserMealSetting::with($with)->where($where)->get();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  MealSetting
     */
    public static function findMealContentStatus($where, $with = [])
    {
        return UserMealContentStatus::with($with)->where($where)->first();
    }



    
    /**
     * Find all

     * @return  allMeals
     */
    public static function findAllMeals()
    {
        return MealMaster::all(); 
    }

    /**
     * Find usear meals according to currentDate
     *
     * @return  Meals
     */
    public static function findCurrentUserMeals($request){
        $userData = getUser();
        $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
        $currentDate = getLocalDateTime('', 'Y-m-d');
        // $date = !empty($request->date)?$request->date:$currentDate;
        // $userMeals = UserMeal::where([['user_id',$userId],['meal_date',$date],['status','!=','deleted']])->get();
        $userMeals = UserMeal::where([['user_id',$userId],['meal_date',$currentDate],['status','!=','deleted']])->get();
        return $userMeals;
    }

    /**
     * Find usear meals according to currentDate
     *
     * @return  Meals
     */
    public static function findSingleMeal($request){
        $userData = getUser();
        $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
        $currentDate = getLocalDateTime('', 'Y-m-d');   
        $date = !empty($request->date) 
        
        ? Carbon::createFromFormat('m-d-Y', $request->date)->format('Y-m-d')
        : $currentDate;
        $mealId = $request->meal_id;
        $userMeals = UserMeal::with('meal')->where([['user_id',$userId],['meal_date',$date],['meal_master_id',$mealId],['status','!=','deleted']])->first();


        return $userMeals;
    }

    /**
     * Find all
     * returning the data needed for showcase into that
     * @return  detail
     */
    public static function loadFoodDetail($request, $userId = '')
    {
        try {
            $userData = getUser();
            $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $default_settings = $request->defaultSettings;
            $setting = self::findAllSetting(['user_id'=>$userId,'status'=>'active']);
            $mealContentStatus = self::findMealContentStatus(['user_id'=>$userId,'status'=>'active']);
            $user_current_data = self::findCurrentUserMeals($request);
            $allMeals = self::findAllMeals();
            $setting = $setting->pluck('meal_master_id')->toArray();
            $setting = $allMeals->whereIn('id',$setting);

            $mealDate = $request->date;
            $mealId = $request->meal_id;
            // if(!empty($mealId) && !empty($mealDate)){
            //     $mealData = self::findSingleMeal($request);
            //     $user_current_data = $user_current_data->map(function ($data) use ($mealId, $mealData) {
            //         if ($data->meal_master_id == $mealId) {
            //             return $mealData;
            //         }
            //         return $data;
            //     });
            // }
           
            
            if($setting->isEmpty()){
                
                $settingData = [];

                    foreach ($default_settings['meals'] as $meal) {
                        $settingData[] = [
                            'meal_master_id' => $meal,
                            'user_id' => $userId,
                            'created_by'=>$userId,
                            'updated_by'=>$userId,
                        ];
                    }
                    UserMealSetting::insert($settingData);
                   
                    
                // foreach($default_settings['meals'] as $meal){
                //     $saveMealSettings = new UserMealSetting();
                //     $saveMealSettings->calories_status = $default_settings['calories_status'];
                //     $saveMealSettings->carbohydrates_status = $default_settings['carbohydrates_status'];
                //     $saveMealSettings->proteins_status = $default_settings['proteins_status'];
                //     $saveMealSettings->meal_master_id = $meal;
                //     $saveMealSettings->user_id = $userId;
                //     $saveMealSettings->save();
                // }
                $setting = self::findAllSetting(['user_id'=>$userId,'status'=>'active']);
            }
            

            if(empty($mealContentStatus)){
                $saveMealContentStatus = new UserMealContentStatus();
                $saveMealContentStatus->calories_status = $default_settings['calories_status'];
                $saveMealContentStatus->carbohydrates_status = $default_settings['carbohydrates_status'];
                $saveMealContentStatus->proteins_status = $default_settings['proteins_status'];
                $saveMealContentStatus->user_id = $userId;
                $saveMealContentStatus->created_by = $userId;
                $saveMealContentStatus->updated_by = $userId;
                $saveMealContentStatus->save();
                
            }
            $detail = [
                'currentDate' => $currentDate,
                'setting' => $setting,
                'user_current_data' => $user_current_data,
                'user_meal_content_status' => $mealContentStatus,
            ];

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
     /**
     * Save Food Setting
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveFoodSetting($request)
    {
        DB::beginTransaction();
        try{
            $post = $request->all();
            $user = getUser();
            $userId = ! empty($userId) ? $userId : $user->id;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            // dd($post);
            
            UserMealSetting::where('user_id',$userId)->delete();
            $mealContentStatus = self::findMealContentStatus(['user_id'=>$userId,'status'=>'active']);
            if(!empty($mealContentStatus)){
                $mealContentStatus->calories_status = isset($post['calories_status']) && $post['calories_status'] === 'on' ? 'enabled' : 'disabled';
                $mealContentStatus->carbohydrates_status = isset($post['carbohydrates_status']) && $post['carbohydrates_status'] === 'on' ? 'enabled' : 'disabled';
                $mealContentStatus->proteins_status = isset($post['proteins_status']) && $post['proteins_status'] === 'on' ? 'enabled' : 'disabled';                
                $mealContentStatus->user_id = $userId;
                $mealContentStatus->updated_by = $userId;
                $mealContentStatus->save();  
            }else{
                $saveMealContentStatus = new UserMealContentStatus();
                $saveMealContentStatus->calories_status = $default_settings['calories_status'];
                $saveMealContentStatus->carbohydrates_status = $default_settings['carbohydrates_status'];
                $saveMealContentStatus->proteins_status = $default_settings['proteins_status'];
                $saveMealContentStatus->user_id = $userId;
                $saveMealContentStatus->created_by = $userId;
                $saveMealContentStatus->updated_by = $userId;
                $saveMealContentStatus->save();
            }
            
            $settingData = [];

                    foreach ($post['meals'] as $meal) {
                        $settingData[] = [
                            'meal_master_id' => $meal,
                            'user_id' => $userId,
                            'created_by'=>$userId,
                            'updated_by'=>$userId,
                        ];
                    }
                    UserMealSetting::insert($settingData);
                DB::commit();
                return true;
        } catch(\Exception $ex){
            throw $ex;
            DB::rollBack();
        }
    }
    
    /**
     * Save and Update Users Meals 
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveUserMeals($request)
    {
        DB::beginTransaction();
        try{
            $post = $request->all();
            $user = getUser();
            $userId = ! empty($userId) ? $userId : $user->id;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            // dd($request->date);
            $date = !empty($request->date)? Carbon::createFromFormat('m-d-Y', $request->date)->format('Y-m-d') : $currentDate;
            
            $existedData = UserMeal::where([['user_id',$userId],['meal_master_id',$post['meal_id']],['meal_date',$date]])->first();
            $mealContentStatus = self::findMealContentStatus(['user_id'=>$userId,'status'=>'active']);
            if(empty($existedData)){
                $userMeal = new UserMeal();
                $userMeal->meal_master_id = $post['meal_id'];
                $userMeal->user_id = $userId;
                if($mealContentStatus->calories_status == 'enabled'){
                    $userMeal->calories = $post['calories'];
                }
                if($mealContentStatus->carbohydrates_status == 'enabled'){
                    $userMeal->carbohydrates = $post['carbohydrates'];
                }
                if($mealContentStatus->proteins_status == 'enabled'){
                    $userMeal->proteins = $post['proteins'];
                }
                $userMeal->meal_date = $date;
                $userMeal->save();
            }else{
                if($mealContentStatus->calories_status == 'enabled'){
                    $existedData->calories = $post['calories'];
                }
                if($mealContentStatus->carbohydrates_status == 'enabled'){
                    $existedData->carbohydrates = $post['carbohydrates'];
                }
                if($mealContentStatus->proteins_status == 'enabled'){
                    $existedData->proteins = $post['proteins'];
                } 
                $existedData->save();
            }
            $log = isset($userMeal) ? $userMeal : $existedData;
            ChallengeLogger::log('food-tracker', $log);
            DB::commit();
            return true;
        }catch(\Exception $ex){
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Find Status period
     *
     * @return  Meals
     */

    public static function displayUserFoodStatus($request)
    {
        try{
            $post = $request->all();
            $userData = getUser();
            $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $range = isset($post['range']) ? $post['range'] : 'week';
             // Get past dates relative to the current date
             $weekDate  = Carbon::parse($currentDate)->subWeek()->format('Y-m-d');
             $monthDate = Carbon::parse($currentDate)->subMonth()->format('Y-m-d');
             $yearDate  = Carbon::parse($currentDate)->subYear()->format('Y-m-d');
            $userSettings = UserMealSetting::where([['user_id', $userData->id],['status','!=','deleted']])->get();
            $userMeals = UserMeal::where([['user_id', $userId],['status','!=','deleted']]);
            if($range == 'month'){
                $userMeals->whereBetween('meal_date',[$monthDate,$currentDate]);
            }elseif($range == 'year'){
                $userMeals->whereBetween('meal_date',[$yearDate,$currentDate]);
            }else{
                $userMeals->whereBetween('meal_date',[$weekDate,$currentDate]);
            }
            $userMeals = $userMeals->get();
            $mealContentStatus = self::findMealContentStatus(['user_id' => $userId, 'status' => 'active']);            
            return $report = [
                'userMeals' => $userMeals,
                'settings' => $userSettings,
                'meal_content_status' => $mealContentStatus,
            ];

        } catch(\Exception $ex){
            throw $ex;
        }
    }
}


