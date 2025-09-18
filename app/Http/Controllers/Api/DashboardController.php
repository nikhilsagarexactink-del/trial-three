<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\WaterTrackerRepository;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\RecipeRepository;
use Config;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    /**
     * Week activity log
     *
     * @return \Illuminate\Http\Response
     */
    public function loadWeekActivityList(Request $request)
    {
        try {
            $water = WaterTrackerRepository::getActivityLog($request);
            // $height = HealthTrackerRepository::getHealthMeasurementLog($request);
            $results = FitnessProfileRepository::getTodayWorkOutDetail($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $results, 'water' => $water],
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
     * @return \Illuminate\Http\Response
     */
    public function getLatestTrainingRecipe(Request $request)
    {
        try {
            $trainings = TrainingVideoRepository::loadListForUser($request);
            $recipes = RecipeRepository::loadListForUser($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['trainings' => $trainings, 'recipes' => $recipes],
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
