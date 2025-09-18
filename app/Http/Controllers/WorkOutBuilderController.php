<?php

namespace App\Http\Controllers;

use App\Http\Requests\DifficultyRequest;
use App\Http\Requests\EquipmentRequest;
use App\Http\Requests\CustomWorkoutNameRequest;
// use App\Http\Requests\ExerciseRequest;
use App\Http\Requests\WorkoutExerciseRequest;
use App\Repositories\AgeRangeRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\RewardRepository;
use App\Repositories\SportRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkoutBuilderRepository;
use App\Repositories\GroupRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class WorkoutBuilderController extends BaseController
{
    /**
     * Show the difficulty index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('workout-builder.difficulty.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function userWorkouts()
    {
        try {
            return view('user-workout-builder.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Retrieve all workout categories that are not deleted and belong to the workout-builder type.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of workout categories.
     */

    private function getWorkoutCategories(){
        $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
        return $categories;
    }

    /**
     * Add difficulty form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            return view('workout-builder.difficulty.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit difficulty form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('workout-builder.difficulty.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add difficulty
     *
     * @return \Illuminate\Http\Response
     */
    public function save(DifficultyRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Difficulty Category successfully created.',
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
     * Update difficulty
     *
     * @return \Illuminate\Http\Response
     */
    public function update(DifficultyRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Difficulty Category successfully updated.',
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
     * Display a listing of the difficulty.
     *
     * @return Response
     */
    public function loadList(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadList($request);
            $view = View::make('workout-builder.difficulty._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Change Status for difficulty
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::changeStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
     * Show the equipment index.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexEquipment()
    {
        try {
            return view('workout-builder.equipment.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add equipment form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addFormEquipment()
    {
        try {
            return view('workout-builder.equipment.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit equipment form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editFormEquipment(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::findOneEquipment(['id' => $request->id]);
            if (! empty($result)) {
                return view('workout-builder.equipment.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add equipment
     *
     * @return \Illuminate\Http\Response
     */
    public function saveEquipment(EquipmentRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::saveEquipment($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Equipment Category successfully created.',
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
     * Update equipment
     *
     * @return \Illuminate\Http\Response
     */
    public function updateEquipment(EquipmentRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::updateEquipment($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Equipment Category successfully updated.',
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
     * Display a listing of the equipment.
     *
     * @return Response
     */
    public function loadListEquipment(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadListEquipment($request);
            $view = View::make('workout-builder.equipment._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Change Status for equipment
     *
     * @return Response
     */
    public function changeEquipmentStatus(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::changeEquipmentStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
      * Display index of workout and exercise
      *
      * @return Response
      */
     public function indexWorkout()
     {
         try {
             $categories = self::getWorkoutCategories();
             $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
             $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
             $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);

             return view('workout-builder.workout-exercise.index', compact('categories', 'difficulties', 'ageRanges', 'athletes'));
         } catch (\Exception $ex) {
             abort(404);
         }
     }

    /**
     * Add workout form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addFormWorkout()
    {
        try {
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
            $sports = SportRepository::findAll([['status', '!=', 'deleted']]);
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
            $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
            $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);
            $equipments = WorkoutBuilderRepository::findAllEquipment([['status', '!=', 'deleted']]);
            $groups = GroupRepository::findAll([['status','=','active']]);
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'build-own-workout'] , ['reward_game.game']);

            return view('workout-builder.workout-exercise.workout.add', compact('categories', 'difficulties', 'ageRanges', 'sports', 'athletes', 'equipments' , 'groups','rewardDetail'));
        } catch (\Exception $ex) {
            // abort(404);
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
  * Show edit workout form.
  *
  * @return \Illuminate\Http\Response
  */
 public function editFormWorkout(Request $request)
 {
     try {
         $result = WorkoutBuilderRepository::findOneWorkout(['id' => $request->id]);
         if (! empty($result)) {
             $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
             $sports = SportRepository::findAll([['status', '!=', 'deleted']]);
             $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
             $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
             $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);
             $equipments = WorkoutBuilderRepository::findAllEquipment([['status', '!=', 'deleted']]);
             $groups = GroupRepository::findAll([['status','=','active']]);
             //  echo '<pre>';
             //  print_r($result);
             //  exit;

             return view('workout-builder.workout-exercise.workout.edit', compact('result', 'categories', 'difficulties', 'ageRanges', 'sports', 'athletes', 'equipments' , 'groups'));
         } else {
             abort(404);
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
         abort(404);
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
            $view = View::make('workout-builder.workout-exercise.workout._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
    public function loadWorkoutExerciseList(Request $request)
    {
        try {
            $exercises = WorkoutBuilderRepository::loadExerciseList($request);
            $view = View::make('workout-builder.workout-exercise.workout.exercise-modal', ['data' => $exercises])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['exercises' => $exercises, 'html' => $view],
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
     * Change Status for workout
     *
     * @return Response
     */
    public function changeWorkoutStatus(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::changeWorkoutStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
     * view detail workout.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewWorkout(Request $request)
    {
        try {
            // $result = WorkoutBuilderRepository::detailWorkout($request);
            $result = WorkoutBuilderRepository::findOneWorkout(['id' => $request->id]);
            return view('workout-builder.workout-exercise.workout.view', compact('result'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add exercise form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addFormExercise()
    {
        try {
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
            $sports = SportRepository::findAll([['status', '!=', 'deleted']]);
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
            $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
            $equipments = WorkoutBuilderRepository::findAllEquipment([['status', '!=', 'deleted']]);
            // $vimeoVideos = WorkoutBuilderRepository::findAllVimeoVideos();
            // dd($vimeoVideos);

            return view('workout-builder.workout-exercise.exercise.add', compact('difficulties', 'ageRanges', 'sports', 'categories', 'equipments'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

 /**
  * Show edit exercise form.
  *
  * @return \Illuminate\Http\Response
  */
 public function editFormExercise(Request $request)
 {
     try {
         $result = WorkoutBuilderRepository::findOneExercise(['id' => $request->id]);
         if (! empty($result)) {
             $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
             $sports = SportRepository::findAll([['status', '!=', 'deleted']]);
             $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
             $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
             $equipments = WorkoutBuilderRepository::findAllEquipment([['status', '!=', 'deleted']]);
             return view('workout-builder.workout-exercise.exercise.edit', compact('result', 'difficulties', 'ageRanges', 'sports', 'equipments', 'categories'));
         } else {
             abort(404);
         }
     } catch (\Exception $ex) {
         abort(404);
     }
 }

    /**
     * Add exercise
     *
     * @return \Illuminate\Http\Response
     */
    public function saveWorkoutExercise(WorkoutExerciseRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::saveWorkoutExercise($request);
            $successMessage = $request->input('type') == 'exercise'
            ? 'Exercise successfully created.'
            : 'Workout successfully created.';

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => $successMessage,
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
     * Update exercise
     *
     * @return \Illuminate\Http\Response
     */
    public function updateExerciseWorkout(WorkoutExerciseRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::updateExerciseWorkout($request);
            $successMessage = $request->input('type') == 'exercise'
            ? 'Exercise successfully updated.'
            : 'Workout successfully updated.';

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => $successMessage,
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
            $view = View::make('workout-builder.workout-exercise.exercise._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Change Status for exercise
     *
     * @return Response
     */
    public function changeWorkoutExerciseStatus(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::changeWorkoutExerciseStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
     * Add exercise detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewExercise(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::detailExercise($request);

            return view('workout-builder.workout-exercise.exercise.view', compact('result'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Change Status for workout
     *
     * @return Response
     */
    public function cloneWorkout(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::cloneWorkout($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Clone successfully added.',
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

    public function userAdvancedWorkouts(Request $request)
    {
        $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type', '=', 'workout-builder']]);
        $sports = SportRepository::findAll([['status', '!=', 'deleted']]);
        $equipments = WorkoutBuilderRepository::findAllEquipment([['status', '!=', 'deleted']]);
        $difficulties = WorkoutBuilderRepository::findAll([['status', '!=', 'deleted']]);
        $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
        $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);

        return view('workout-builder.advanced-workouts.index', compact('difficulties', 'ageRanges', 'sports', 'equipments', 'categories'));
    }

    /**
     * Load record list for Workout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAdvancedWorkout(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadWorkoutList($request);
            $view = View::make('workout-builder.advanced-workouts._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
    public function indexWorkoutCategory()
    {
        try {
            return view('workout-builder.category.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }
    public function addWorkoutCategory(Request $request)
    {
        return view('workout-builder.category.add');
    }

    public function editWorkoutCategory(Request $request)
    {
        try {
           $result = CategoryRepository::findOne([['status', '!=', 'deleted'], ['type', 'workout-builder']]);
            if (! empty($result)) {
                return view('workout-builder.category.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            //print_r($ex->getMessage());die;
            abort(404);
        }
    }

    public function loadUserWorkouts(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadWorkoutList($request);
            $view = View::make('user-workout-builder._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Workout goal index page
     */
    public function indexWorkoutGoal(Request $request)
    {
        try {
            $userData = getUser();
            $workouts = WorkoutBuilderRepository::findAllGoal([['status', '=', 'active']]);
            $userGoals = WorkoutBuilderRepository::getUserGoals($request);
           // $myGoal = WorkoutBuilderRepository::getUserCurrentGoal();
            $myGoal = WorkoutBuilderRepository::getTodayWorkoutGoal($request);
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'create-workout-goal','status' => 'active',] , ['reward_game.game']);
            // echo '<pre>';
            // print_r($myGoal);die;
            return view('workout-builder.goal.index', compact('workouts', 'myGoal','rewardDetail'));
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }

     /**
     * Save workout goal
     */
    public function saveWorkoutGoal(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::saveWorkoutGoal($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Goal successfully saved.'
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
     * Complete today workout
     */
    // public function completeTodayWorkout(Request $request)
    // {
    //     try {
    //         $result = WorkoutBuilderRepository::completeTodayWorkout($request);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $result,
    //                 'message' => 'Workout successfully completed.'
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
     * Get my workout goal detail
     */
    public function getWorkoutGoalDetail(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::getWorkoutGoalDetail($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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

    public function findAllVimeoVideos(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::findAllVimeoVideos($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => ''
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


    public function customWorkoutNamesIndex(Request $request){
        try {
          return  view('workout-builder.custom-workout-names.index'); 
        }
        catch(\Execption $ex){
           abort(404);
        }
    }


    public function addCustomWorkoutNameForm(Request $request){
        try {
          return  view('workout-builder.custom-workout-names.add'); 
        }
        catch(\Execption $ex){
           abort(404);
        }
    }

    
    /**
     * Add custom workout name
     *
     * @return \Illuminate\Http\Response
     */
    public function saveCustomWorkoutName(CustomWorkoutNameRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::saveCustomWorkoutName($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Custom workout name successfully created.',
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
     * Update custom workout name
     *
     * @return \Illuminate\Http\Response
     */
    public function loadListCustomWorkoutName(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::loadListCustomWorkoutName($request);
            $view = View::make('workout-builder.custom-workout-names._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Update Custom name form
     *
     * @return \Illuminate\Http\Response
     */
    public function editFormCustomWorkoutName(Request $request)
    {
        try {
           $result = WorkoutBuilderRepository::findOneCustomWorkoutName(['id' => $request->id]);
            if (! empty($result)) {
                return view('workout-builder.custom-workout-names.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            //print_r($ex->getMessage());die;
            abort(404);
        }
    }


    /**
     * Update equipcustom name form
     *
     * @return \Illuminate\Http\Response
     */
    public function updateCustomWorkoutName(CustomWorkoutNameRequest $request)
    {
        try {
            $result = WorkoutBuilderRepository::updateCustomWorkoutName($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Custom workout name successfully updated.',
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
     * Change Status for  custom  workout name
     *
     * @return Response
     */
    public function changeCustomWorkoutNameStatus(Request $request)
    {
        try {
            $result = WorkoutBuilderRepository::changeCustomWorkoutNameStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
    
    public function sendCustomWorkoutReminder(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return WorkoutBuilderRepository::sendCustomWorkoutReminder($request);
        }, '');
    }

}
