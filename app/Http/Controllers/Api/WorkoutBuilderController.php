<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Repositories\RewardRepository;
use Illuminate\Http\Request;
use App\Repositories\WorkoutBuilderRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\AgeRangeRepository;
use App\Repositories\UserRepository;
use Config;

class WorkoutBuilderController extends ApiController
{
    /**
     * Display a listing of the resource.
     * Load Difficulties
     * @return Response
     */
    public function loadDifficultyList(Request $request)
    {
        try {
            $results = WorkoutBuilderRepository::loadList($request);
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadEquipments(Request $request)
    {
        try {
            $results = WorkoutBuilderRepository::loadListEquipment($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['equipments' => $results],
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
     * Display a listing of the exercise.
     *
     * @return Response
     */
    public function loadExerciseList(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadExerciseList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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
     * Display a listing of the workout.
     *
     * @return Response
     */
    public function loadWorkoutList(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadWorkoutList($request);
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
            $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
            $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $result, 'categories' => $categories, 'difficulties' => $difficulties, 'ageRanges' => $ageRanges, 'athletes' => $athletes],
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

    public function loadUserWorkouts(Request $request){
        try {
            $request->type = 'my-workouts';
            $result = WorkoutBuilderRepository::loadWorkoutList($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $result],
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
    public function userWorkoutDetail(Request $request){
        try {
            $result = WorkoutBuilderRepository::findOneWorkout(['id' => $request->workoutId]);
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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

    public function getWorkoutGoalList(Request $request){
        try {
            $results = WorkoutBuilderRepository::findAllGoal([['status', '=', 'active']]);
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
     * Save workout goal
     */
    public function saveWorkoutGoal(Request $request)
    {
        try {
            if(!empty($request->id)){
                $result = WorkoutBuilderRepository::saveWorkoutGoal($request);

                return response()->json(
                    [
                        'success' => true,
                        'data' => $result,
                        'message' => 'Goal successfully saved.'
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            }else{
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => "Workout goal id is required.",
                    ],
                    Config::get('constants.HttpStatus.BAD_REQUEST')
                );
            }

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
     * Get my workout goal detail
     */
    public function getWorkoutGoalDetail(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::getWorkoutGoalDetail($request);
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'create-workout-goal','status' => 'active',] , ['reward_game.game']);
            $gameKey = null;
            if($rewardDetail->is_gamification == 1 && !empty($rewardDetail->reward_game)){
                $game = getDynamicGames($rewardDetail);
                $gameKey = $game['game_key']??null;
            }
            return response()->json(
                [
                    'success' => true,
                    'data' => ['result'=>$result,'game_key'=>$gameKey,'reward_detail'=>$rewardDetail],
                    'message' => 'Workout goal detail.'
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
