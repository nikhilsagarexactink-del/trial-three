<?php

namespace App\Repositories;

use App\Models\Difficulty;
use App\Models\Equipment;
use App\Models\WorkoutExercise;
use App\Models\CustomWorkoutName;
use App\Models\WorkoutExerciseAgeRange;
use App\Models\WorkoutExerciseCategory;
use App\Models\WorkoutExerciseDifficulty;
use App\Models\WorkoutExerciseEquipment;
use App\Models\WorkoutExerciseSport;
use App\Models\WorkoutExerciseUsersAssignment;
use App\Models\WorkoutSet;
use App\Models\WorkoutGoal;
use App\Models\CustomWorkoutLavel;
use App\Models\WorkoutSetExercise;
use App\Models\UserWorkoutGoal;
use App\Models\UserWorkoutGoalLog;
use App\Models\FitnessProfile;
use App\Models\WorkoutExerciseGroup;
use App\Services\VimeoService;
use App\Jobs\UserCalendarReminderJob;
use Config;
use DB;
use Exception;
use DateTime;


class WorkoutBuilderRepository
{
    /**
     * Find  one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutExercise
     */
    public static function findOneExercise($where, $with = ['difficulties', 'ageRanges', 'sports', 'categories', 'equipments' ,'workoutGroups'])
    {
        return  WorkoutExercise::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutExercise
     */
    public static function findAllExercise($where, $with = [])
    {
        return WorkoutExercise::with($with)->where($where)->get();
    }

    /**
     * Find one Difficulty
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Difficulty
     */
    public static function findOne($where, $with = [])
    {
        return Difficulty::with($with)->where($where)->first();
    }

    /**
     * Find all Difficulty
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Difficulty
     */
    public static function findAll($where, $with = [])
    {
        return Difficulty::with($with)->where($where)->get();
    }

    /**
     * Find one Equipment
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Equipment
     */
    public static function findOneEquipment($where, $with = [])
    {
        return Equipment::with($with)->where($where)->first();
    }

    /**
     * Find all Equipment
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Equipment
     */
    public static function findAllEquipment($where, $with = [])
    {
        return Equipment::with($with)->where($where)->get();
    }

    /**
     * Find one Workout
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutExercise
     */
    public static function findOneWorkout($where)
    {
        return WorkoutExercise::with(['sets'=> function($q) {
            $q->where('status', 'active');
        },'difficulties',
          'ageRanges',
          'sports',
          'categories',
          'athletes',
          'sets.workoutSetExercises',
          'sets.workoutSetExercises.exercise',
          'sets.workoutSetExercises.exercise.media'])->where($where)->first();
    }

    /**
     * Find all Workout
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutExercise
     */
    public static function findAllWorkout($where, $with = [])
    {
        return WorkoutExercise::with($with)->where($where)->get();
    }
    /**
     * Find  one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutGoal
     */
    public static function findOneGoal($where, $with = [])
    {
        return  WorkoutGoal::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutGoal
     */
    public static function findAllGoal($where, $with = [])
    {
        return WorkoutGoal::with($with)->where($where)->get();
    }
     /**
     * Find  one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  UserWorkoutGoal
     */
    public static function findOneUserGoal($where, $with = [])
    {
        return  UserWorkoutGoal::with($with)->where($where)->first();
    }

     /**
     * Find  all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  UserWorkoutGoal
     */
    public static function findUserGoals($where, $with = [])
    {
        return  UserWorkoutGoal::with($with)->where($where)->get();
    }

    /**
     * Find one custom workout label
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Equipment
     */
    public static function findOneCustomWorkoutName($where, $with = [])
    {
        return CustomWorkoutName::with($with)->where($where)->first();
    }



    /**
     * Load record list for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Difficulty::where('status', '!=', 'deleted')->orderBy($sortBy, $sortOrder);
            // if ($userData->user_type !== 'admin') {
            //     $list->where('created_by', $userData->id);
            // }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('name', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeStatus($request)
    {
        try {
            $model = Difficulty::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Record for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function save($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new Difficulty();
            $model->name = $post['name'];
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update Record for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function update($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->name = $post['name'];
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

      /**
       * Load record list for Equipment
       *
       * @param array
       * @return mixed
       *
       * @throws Throwable $th
       */
      public static function loadListEquipment($request)
      {
          try {
              $post = $request->all();
              $userData = getUser();
              $sortBy = 'created_at';
              $sortOrder = 'DESC';
              $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
              $list = Equipment::where('status', '!=', 'deleted');
              //   if ($userData->user_type !== 'admin') {
            //       $list->where('created_by', $userData->id);
              //   }
              //Search from name
              if (! empty($post['search'])) {
                  $list->where('name', 'like', '%'.$post['search'].'%');
              }
              //Search from status
              if (! empty($post['status'])) {
                  $list->where('status', $post['status']);
              }
              //Sort by
              if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                  $sortBy = $post['sort_by'];
                  $sortOrder = $post['sort_order'];
              }
              $list = $list->orderBy($sortBy, $sortOrder);
              $list = $list->paginate($paginationLimit);

              return $list;
          } catch (\Exception $ex) {
              throw $ex;
          }
      }

    /**
     * Change record status by Id for equipment
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeEquipmentStatus($request)
    {
        try {
            $model = Equipment::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Record for Equipment
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveEquipment($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new Equipment();
            $model->name = $post['name'];
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update Record for Equipment
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateEquipment($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOneEquipment(['id' => $request->id]);
            if (! empty($model)) {
                $model->name = $post['name'];
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                return $model;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   /**
    * Load record list for Workout
    *
    * @param array
    * @return mixed
    *
    * @throws Throwable $th
    */
    public static function loadWorkoutList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $athlete_id = $request->athlete_id;
           if(!empty($athlete_id)){
            $athlete =  UserRepository::findOne(['id' => $athlete_id]);
           }
            $paginationLimit = !empty($post['perPage']) ? $post['perPage'] : 9;

            // Ensure $userData is an array (if it's an object, convert it)
            $sortBy = !empty($post['sort_by']) ? $post['sort_by'] : 'created_at';
            $sortOrder = !empty($post['sort_order']) ? $post['sort_order'] : 'DESC';

            $list = WorkoutExercise::with(['sets'=> function($q) {
                $q->where('status', 'active');
            },'assignments', 'difficulties', 'workoutGroups', 'ageRanges', 'sports', 'categories', 'athletes', 'media','sets.workoutSetExercises','sets.workoutSetExercises.exercise','sets.workoutSetExercises.exercise.media'])
                ->where([['type','workout'], ['status', '!=', 'deleted']])
                ->orderBy($sortBy, $sortOrder);

            if ($userData['user_type'] !== 'admin' && $userData['user_type'] !== 'coach') {
                if (isset($request->type) && $request->type == 'my-workouts') {
                    
                    if(!empty($athlete)){
                        if ($athlete->user_type !== 'admin' && $athlete->user_type !== 'coach') {

                            $list->where(function ($query) use ($athlete) {
                                $query->where('created_by', $athlete->id)
                                      ->orWhereHas('assignments', function ($q) use ($athlete) {
                                          $q->where('user_id', $athlete->id);
                                      })
                                      ->orWhereHas('workoutGroups', function ($q) use ($athlete) {
                                          $q->whereHas('groupUsers', function ($q1) use ($athlete) {
                                              $q1->where('group_users.user_id', $athlete->id);
                                          });
                                      })->orWhere(function($q) {
                                       $q->whereDoesntHave('assignments')
                                         ->whereDoesntHave('workoutGroups')
                                         ->where('type', 'workout');
                                   });
                            })->distinct(); // Prevent duplicate records;
                        }
                       }else{
                        $list->where(function ($query) use ($userData) {
                                $query->where('created_by', $userData->id)
                                      ->orWhereHas('assignments', function ($q) use ($userData) {
                                          $q->where('user_id', $userData->id);
                                      })
                                      ->orWhereHas('workoutGroups', function ($q) use ($userData) {
                                          $q->whereHas('groupUsers', function ($q1) use ($userData) {
                                              $q1->where('group_users.user_id', $userData->id);
                                          });
                                      })->orWhere(function($q) {
                                       $q->whereDoesntHave('assignments')
                                         ->whereDoesntHave('workoutGroups')
                                         ->where('type', 'workout');
                                   });
                            })->distinct();
                        // $list->where(function ($query) use ($userData) {
                        //     $query->where('created_by', $userData['id'])
                        //         ->orWhereHas('assignments', function ($q) use ($userData) {
                        //             $q->where('user_id', $userData['id']);
                        //         });
                        // })->orWhereDoesntHave('assignments')->where('type', 'workout');
                }} else {
                    $list->where(function ($query) use ($userData) {
                        $query->where('created_by', $userData->id);
                    });
                }
            }

            // Search Filters
            
            if (!empty($post['category_id'])) {
                $list->whereHas('categories', function ($q) use ($post) {
                    $q->where('category_id', $post['category_id']);
                });
            }
            if (!empty($post['difficulty_id'])) {
                $list->whereHas('difficulties', function ($q) use ($post) {
                    $q->where('difficulty_id', $post['difficulty_id']);
                });
            }
            if (!empty($post['age_range_id'])) {
                $list->whereHas('ageRanges', function ($q) use ($post) {
                    $q->where('age_range_id', $post['age_range_id']);
                });
            }
            if (!empty($post['athlete_id'])) {
                $list->whereHas('athletes', function ($q) use ($post) {
                    $q->where('user_id', $post['athlete_id']);
                });
            }
            if (!empty($post['search'])) {
                $list->where('name', 'like', '%' . $post['search'] . '%');
            }
            if (!empty($post['status'])) {
                if ($post['status'] === 'inactive') {
                    // Fetch workouts with status 'inactive' or 'draft'
                    $list->whereIn('status', ['inactive', 'draft']);
                } else {
                    // Fetch workouts with the exact given status
                    $list->where('status', $post['status']);
                }
            }


            return $list->paginate($paginationLimit);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   /**
    * Load record list for Workout
    *
    * @param array
    * @return mixed
    *
    * @throws Throwable $th
    */
    public static function loadAllWorkoutList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();

            $sortBy = 'created_at';
            $sortOrder = 'DESC';

            $athlete_id = $request->athlete_id;
            $athlete = !empty($athlete_id) ? UserRepository::findOne(['id' => $athlete_id]) : $userData;

            $user = $athlete ?? $userData;

            $list = WorkoutExercise::with('assignments', 'difficulties', 'ageRanges', 'sports', 'categories', 'athletes', 'media')->where([['type','workout'], ['status', 'active']]);
            // Restrict if user is not admin or coach
            if ($user->user_type !== 'admin' && $user->user_type !== 'coach') {
                $list->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->orWhereHas('assignments', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                })
                ->orWhereDoesntHave('assignments')->where('type','workout')->orderBy($sortBy, $sortOrder);
            }

            return $list->distinct()->orderBy($sortBy, $sortOrder)->get();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * Load all workouts list
     * load workouts according to fintess profile dropdown
     *
     * @param string $request
     *
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadWorkouts($request)
    {
        try {
            $userData = getUser();

            $sortBy = 'created_at';
            $sortOrder = 'DESC';

            $list = WorkoutExercise::with([
                'assignments',
                'difficulties',
                'workoutGroups',
                'ageRanges',
                'sports',
                'categories',
                'athletes',
                'media',
            ])
            ->where('type', 'workout')
            ->where('status', 'active');

            if ($request === 'my_workout') {
                $list->where('created_by', $userData->id);

            } elseif ($request === 'available_workout') {
                $list->where(function ($query) use ($userData) {
                    $query->whereHas('assignments', function ($q) use ($userData) {
                            $q->where('user_id', $userData->id)
                            ->where('created_by', '!=', $userData->id);
                        })
                        ->orWhereHas('workoutGroups.groupUsers', function ($q) use ($userData) {
                            $q->where('group_users.user_id', $userData->id);
                        })
                        ->orWhere(function ($q) {
                            $q->whereDoesntHave('assignments')
                            ->whereDoesntHave('workoutGroups');
                        });
                });
            }

            return $list->distinct()->orderBy($sortBy, $sortOrder)->get();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    /**
     * Detail for workout
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function detailWorkout($request)
    {
        try {
            $userData = getUser();
            $detail = WorkoutExercise::where('id', $request->id)->with('difficulties', 'sports', 'categories', 'media')->where([['type', '=', 'workout'], ['status', '!=', 'deleted']])->first();

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   /**
    * Load record list for Exercise
    *
    * @param array
    * @return mixed
    *
    * @throws Throwable $th
    */
   public static function loadExerciseList($request)
   {
       try {
           $post = $request->all();
           $userData = getUser();
           $sortBy = 'created_at';
           $sortOrder = 'DESC';
           $paginationLimit = ! empty($request->limit) ? $request->limit : Config::get('constants.DefaultValues.PAGINATION_RECORD');

           $list = WorkoutExercise::with('difficulties', 'ageRanges', 'sports', 'categories', 'equipments', 'media')->where([['type', '=', 'exercise'], ['status', '!=', 'deleted']])->orderBy($sortBy, $sortOrder);
           if ($userData->user_type !== 'admin' && $userData->user_type !== 'coach') {
               $list->where('created_by', $userData->id);
           }

           // Search from name
           if (! empty($post['search'])) {
               $list->where('name', 'like', '%'.$post['search'].'%');
           }
           //Search from category
           if (! empty($post['category_id'])) {
               $list->whereHas('categories', function ($q) use ($post) {
                   $q->where('category_id', $post['category_id']);
               });
           }
           //Search from difficulty
           if (! empty($post['difficulty_id'])) {
               $list->whereHas('difficulties', function ($q) use ($post) {
                   $q->where('difficulty_id', $post['difficulty_id']);
               });
           }
           //Search from difficulty
           if (! empty($post['equipment_id'])) {
               $list->whereHas('equipments', function ($q) use ($post) {
                   $q->where('equipment_id', $post['equipment_id']);
               });
           }
           //Search from age range
           if (! empty($post['age_range_id'])) {
               $list->whereHas('ageRanges', function ($q) use ($post) {
                   $q->where('age_range_id', $post['age_range_id']);
               });
           }
           // Search from status
           if (! empty($post['status'])) {
               $list->where('status', $post['status']);
           }

           // Sort by
           if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
               $sortBy = $post['sort_by'];
               $sortOrder = $post['sort_order'];
           }

           $list = $list->paginate($paginationLimit);

           return $list;
       } catch (\Exception $ex) {
           throw $ex;
       }
   }

    /**
     * Change record status by Id for Workut & Exercise
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeWorkoutExerciseStatus($request)
    {
        try {
            $model = WorkoutExercise::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id for Workut & Exercise
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function cloneWorkout($request)
    {
        DB::beginTransaction();
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $userData = getUser();
            $model = WorkoutExercise::where(['id' => $request->id])->first();
            $difficulties = WorkoutExerciseDifficulty::where(['workout_exercise_id' => $model->id])->get();
            $age_ranges = WorkoutExerciseAgeRange::where(['workout_exercise_id' => $model->id])->get();
            $sports = WorkoutExerciseSport::where(['workout_exercise_id' => $model->id])->get();
            $categories = WorkoutExerciseCategory::where(['workout_exercise_id' => $model->id])->get();
            $equipments = WorkoutExerciseEquipment::where(['workout_exercise_id' => $model->id])->get();
            $athletes = WorkoutExerciseUsersAssignment::where(['workout_exercise_id' => $model->id])->get();
            $WorkoutExerciseGroups = WorkoutExerciseGroup::where(['workout_exercise_id' => $model->id])->get();
            $sets = WorkoutSet::where(['workout_exercise_id' => $model->id])->get();

            if (!empty($model)) {
                $cloneData = $model->replicate();
                $cloneData->name = $model->name . ' clone';
                $cloneData->save();

                $clone_difficulties = [];
                if (!empty($difficulties)) {
                    foreach ($difficulties as $key => $difficulty) {
                        $clone_difficulties[$key]['difficulty_id'] = $difficulty->difficulty_id;
                        $clone_difficulties[$key]['workout_exercise_id'] = $cloneData->id;
                        $clone_difficulties[$key]['created_by'] = $userData->id;
                        $clone_difficulties[$key]['updated_by'] = $userData->id;
                        $clone_difficulties[$key]['created_at'] = $currentDateTime;
                        $clone_difficulties[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseDifficulty::insert($clone_difficulties);
                }

                $clone_age_ranges = [];
                if (!empty($age_ranges)) {
                    foreach ($age_ranges as $key => $age_range) {
                        $clone_age_ranges[$key]['age_range_id'] = $age_range->age_range_id;
                        $clone_age_ranges[$key]['workout_exercise_id'] = $cloneData->id;
                        $clone_age_ranges[$key]['created_by'] = $userData->id;
                        $clone_age_ranges[$key]['updated_by'] = $userData->id;
                        $clone_age_ranges[$key]['created_at'] = $currentDateTime;
                        $clone_age_ranges[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseAgeRange::insert($clone_age_ranges);
                }

                $clone_sports = [];
                if (!empty($sports)) {
                    foreach ($sports as $key => $sport) {
                        $clone_sports[$key]['sport_id'] = $sport->sport_id;
                        $clone_sports[$key]['workout_exercise_id'] = $cloneData->id;
                        $clone_sports[$key]['created_by'] = $userData->id;
                        $clone_sports[$key]['updated_by'] = $userData->id;
                        $clone_sports[$key]['created_at'] = $currentDateTime;
                        $clone_sports[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseSport::insert($clone_sports);
                }

                $clone_categories = [];
                if (!empty($categories)) {
                    foreach ($categories as $key => $category) {
                        $clone_categories[$key]['category_id'] = $category->category_id;
                        $clone_categories[$key]['workout_exercise_id'] = $cloneData->id;
                        $clone_categories[$key]['created_by'] = $userData->id;
                        $clone_categories[$key]['updated_by'] = $userData->id;
                        $clone_categories[$key]['created_at'] = $currentDateTime;
                        $clone_categories[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseCategory::insert($clone_categories);
                }

                $clone_equipment = [];
                if (!empty($equipments)) {
                    foreach ($equipments as $key => $equipment) {
                        $clone_equipment[$key]['equipment_id'] = $equipment->equipment_id;
                        $clone_equipment[$key]['workout_exercise_id'] = $cloneData->id;
                        $clone_equipment[$key]['created_by'] = $userData->id;
                        $clone_equipment[$key]['updated_by'] = $userData->id;
                        $clone_equipment[$key]['created_at'] = $currentDateTime;
                        $clone_equipment[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseEquipment::insert($clone_equipment);
                }

                $clone_athletes = [];
                if (!empty($athletes)) {
                    foreach ($athletes as $key => $athlete) {
                        $clone_athletes[$key]['user_id'] = $athlete->user_id;
                        $clone_athletes[$key]['workout_exercise_id'] = $cloneData->id;
                        $clone_athletes[$key]['created_by'] = $userData->id;
                        $clone_athletes[$key]['updated_by'] = $userData->id;
                        $clone_athletes[$key]['created_at'] = $currentDateTime;
                        $clone_athletes[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseUsersAssignment::insert($clone_athletes);
                }


                $groups = [];
                if (!empty($WorkoutExerciseGroups)) {
                    foreach ($WorkoutExerciseGroups as $group) {
                        $groups[] = [
                            'group_id' => $group->group_id,
                            'workout_exercise_id' => $cloneData->id,
                            'created_by' => $userData->id,
                            'updated_by' => $userData->id,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ];
                    }
                    WorkoutExerciseGroup::insert($groups);
                }
                

                if (!empty($sets)) {
                    foreach ($sets as $key => $set) {
                        $setModel = new WorkoutSet();
                        $setModel->set_no = $key + 1;
                        $setModel->workout_exercise_id = $cloneData->id;
                        $setModel->created_by = $userData->id;
                        $setModel->save();

                        $workoutSetExercises = WorkoutSetExercise::where(['workout_set_id'=> $set->id])->get();

                        $clone_workoutSetExercises = [];
                        foreach ($workoutSetExercises as $setExercise) {
                            $clone_workoutSetExercises[] = [
                                'workout_set_id' => $setModel->id,
                                'workout_exercise_id' => $setExercise->workout_exercise_id ?? 0,
                                'no_of_reps' => $setExercise->no_of_reps ?? 0,
                                'created_by' => $userData->id,
                                'created_at' => $currentDateTime,
                                'updated_at' => $currentDateTime
                            ];
                        }

                        if (!empty($clone_workoutSetExercises)) {
                            WorkoutSetExercise::insert($clone_workoutSetExercises);
                        }
                    }
                }

                DB::commit();
                return $cloneData;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }


    /**
     * Add Record for Exercise & Workout
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveWorkoutExercise($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();

            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new WorkoutExercise();
            $model->name = $post['name'];
            $model->type = $post['type'];
            $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : '';
            $model->video_url = ! empty($post['video_url']) ? $post['video_url'] : '';
            $model->days = ! empty($post['days']) ? json_encode($post['days']) : '';
            //$model->no_of_reps = ! empty($post['no_of_reps']) ? $post['no_of_reps'] : '';
            $model->duration = ! empty($post['duration']) ? $post['duration'] : 0;
            $model->visibility = ! empty($post['visibility']) ? $post['visibility'] : '';
            $model->description = ! empty($post['description']) ? $post['description'] : '';
            $model->total_sets = ! empty($post['total_sets']) ? $post['total_sets'] : 0;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            if ($post['type'] == 'workout' && ! empty($post['is_draft']) && $post['is_draft'] == 1) {
                $model->status = 'draft';
            }
            $model->save();

            $difficulties = [];
            if (! empty($request->difficulty_id)) {
                foreach ($request->difficulty_id as $key => $id) {
                    $difficulties[$key]['difficulty_id'] = $id;
                    $difficulties[$key]['workout_exercise_id'] = $model->id;
                    $difficulties[$key]['created_by'] = $userData->id;
                    $difficulties[$key]['updated_by'] = $userData->id;
                    $difficulties[$key]['created_at'] = $currentDateTime;
                    $difficulties[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseDifficulty::insert($difficulties);
            }

            $age_ranges = [];
            if (! empty($request->age_range_id)) {
                foreach ($request->age_range_id as $key => $id) {
                    $age_ranges[$key]['age_range_id'] = $id;
                    $age_ranges[$key]['workout_exercise_id'] = $model->id;
                    $age_ranges[$key]['created_by'] = $userData->id;
                    $age_ranges[$key]['updated_by'] = $userData->id;
                    $age_ranges[$key]['created_at'] = $currentDateTime;
                    $age_ranges[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseAgeRange::insert($age_ranges);
            }

            $sports = [];
            if (! empty($request->sport_id)) {
                foreach ($request->sport_id as $key => $id) {
                    $sports[$key]['sport_id'] = $id;
                    $sports[$key]['workout_exercise_id'] = $model->id;
                    $sports[$key]['created_by'] = $userData->id;
                    $sports[$key]['updated_by'] = $userData->id;
                    $sports[$key]['created_at'] = $currentDateTime;
                    $sports[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseSport::insert($sports);
            }

            $categories = [];
            if (! empty($request->category_id)) {
                foreach ($request->category_id as $key => $id) {
                    $categories[$key]['category_id'] = $id;
                    $categories[$key]['workout_exercise_id'] = $model->id;
                    $categories[$key]['created_by'] = $userData->id;
                    $categories[$key]['updated_by'] = $userData->id;
                    $categories[$key]['created_at'] = $currentDateTime;
                    $categories[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseCategory::insert($categories);
            }

            $equipment = [];
            if (! empty($request->equipment_id)) {
                foreach ($request->equipment_id as $key => $id) {
                    $equipment[$key]['equipment_id'] = $id;
                    $equipment[$key]['workout_exercise_id'] = $model->id;
                    $equipment[$key]['created_by'] = $userData->id;
                    $equipment[$key]['updated_by'] = $userData->id;
                    $equipment[$key]['created_at'] = $currentDateTime;
                    $equipment[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseEquipment::insert($equipment);
            }

            $athletes = [];
            if (! empty($request->athlete_user_ids)) {
                foreach ($request->athlete_user_ids as $key => $id) {
                    $equipment[$key]['user_id'] = $id;
                    $equipment[$key]['workout_exercise_id'] = $model->id;
                    $equipment[$key]['created_by'] = $userData->id;
                    $equipment[$key]['updated_by'] = $userData->id;
                    $equipment[$key]['created_at'] = $currentDateTime;
                    $equipment[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseUsersAssignment::insert($equipment);
            }


            $groups = [];
            if (! empty($request->group_ids)) {
                foreach ($request->group_ids as $key => $id) {
                    $groups[$key]['group_id'] = $id;
                    $groups[$key]['workout_exercise_id'] = $model->id;
                    $groups[$key]['created_by'] = $userData->id;
                    $groups[$key]['updated_by'] = $userData->id;
                    $groups[$key]['created_at'] = $currentDateTime;
                    $groups[$key]['updated_at'] = $currentDateTime;
                }
                WorkoutExerciseGroup::insert($groups);
            }



            if (! empty($post['sets'])) {
                foreach ($post['sets'] as $key => $set) {
                    $setModel = new WorkoutSet();
                    $setModel->set_no = $key;
                    $setModel->workout_exercise_id = $model->id;
                    $setModel->created_by = $userData->id;
                    $setModel->save();

                    if (is_array($set) && ! empty($set)) {
                        // echo '<pre>';
                        // print_r($set);
                        // exit;
                        foreach ($set as $setExercise) {
                            $workoutSetExercise = new WorkoutSetExercise();
                            $workoutSetExercise->workout_set_id = $setModel->id;
                            $workoutSetExercise->workout_exercise_id = ! empty($setExercise['exercise']) ? $setExercise['exercise'] : 0;
                            $workoutSetExercise->no_of_reps = ! empty($setExercise['reps']) ? $setExercise['reps'] : 0;
                            $workoutSetExercise->created_by = $userData->id;
                            $workoutSetExercise->save();
                        }
                    }
                }
            }
            if($post['type'] == 'workout'){
                $reward = [
                    'feature_key' => 'build-own-workout',
                    'module_id' => $model->id,
                    'module' => 'workout-builder',
                    'allow_multiple' => 1,
                ];
                $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'build-own-workout'] , ['reward_game.game']);

                if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                    RewardRepository::saveUserReward($reward);
                }
            }

            // echo '<pre>';
            // print_r($post['sets']);
            // exit;
            DB::commit();

            return $model;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update Record for Exercise $ Workout
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateExerciseWorkout($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            if ($post['type'] == 'exercise') {
                $model = self::findOneExercise(['id' => $request->id]);
            } else {
                $model = self::findOneWorkout(['id' => $request->id]);
            }

            if (! empty($model)) {
                $model->type = $post['type'];
                $model->name = $post['name'];
                $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : '';
                $model->video_url = ! empty($post['video_url']) ? $post['video_url'] : '';
                //$model->no_of_reps = $post['no_of_reps'];
                $model->duration = ! empty($post['duration']) ? $post['duration'] : 0;
                $model->visibility = ! empty($post['visibility']) ? $post['visibility'] : '';
                $model->description = ! empty($post['description']) ? $post['description'] : '';
                $model->days = ! empty($post['days']) ? ($post['days']) : '';
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                if ($post['type'] == 'workout' && ! empty($post['is_draft']) && $post['is_draft'] == 1) {
                    $model->status = 'draft';
                } elseif ($model->status = 'draft') {
                    $model->status = 'active';
                }
                $model->save();

                $difficulties = [];
                WorkoutExerciseDifficulty::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->difficulty_id)) {
                    foreach ($request->difficulty_id as $key => $id) {
                        $difficulties[$key]['difficulty_id'] = $id;
                        $difficulties[$key]['workout_exercise_id'] = $model->id;
                        $difficulties[$key]['created_by'] = $userData->id;
                        $difficulties[$key]['updated_by'] = $userData->id;
                        $difficulties[$key]['created_at'] = $currentDateTime;
                        $difficulties[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseDifficulty::insert($difficulties);
                }

                $age_ranges = [];
                WorkoutExerciseAgeRange::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->age_range_id)) {
                    foreach ($request->age_range_id as $key => $id) {
                        $age_ranges[$key]['age_range_id'] = $id;
                        $age_ranges[$key]['workout_exercise_id'] = $model->id;
                        $age_ranges[$key]['created_by'] = $userData->id;
                        $age_ranges[$key]['updated_by'] = $userData->id;
                        $age_ranges[$key]['created_at'] = $currentDateTime;
                        $age_ranges[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseAgeRange::insert($age_ranges);
                }

                $sports = [];
                WorkoutExerciseSport::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->sport_id)) {
                    foreach ($request->sport_id as $key => $id) {
                        $sports[$key]['sport_id'] = $id;
                        $sports[$key]['workout_exercise_id'] = $model->id;
                        $sports[$key]['created_by'] = $userData->id;
                        $sports[$key]['updated_by'] = $userData->id;
                        $sports[$key]['created_at'] = $currentDateTime;
                        $sports[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseSport::insert($sports);
                }

                $categories = [];
                WorkoutExerciseCategory::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->category_id)) {
                    foreach ($request->category_id as $key => $id) {
                        $categories[$key]['category_id'] = $id;
                        $categories[$key]['workout_exercise_id'] = $model->id;
                        $categories[$key]['created_by'] = $userData->id;
                        $categories[$key]['updated_by'] = $userData->id;
                        $categories[$key]['created_at'] = $currentDateTime;
                        $categories[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseCategory::insert($categories);
                }

                $equipment = [];
                WorkoutExerciseEquipment::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->equipment_id)) {
                    foreach ($request->equipment_id as $key => $id) {
                        $equipment[$key]['equipment_id'] = $id;
                        $equipment[$key]['workout_exercise_id'] = $model->id;
                        $equipment[$key]['created_by'] = $userData->id;
                        $equipment[$key]['updated_by'] = $userData->id;
                        $equipment[$key]['created_at'] = $currentDateTime;
                        $equipment[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseEquipment::insert($equipment);
                }

                $athletes = [];
                WorkoutExerciseUsersAssignment::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->athlete_user_ids)) {
                    foreach ($request->athlete_user_ids as $key => $id) {
                        $equipment[$key]['user_id'] = $id;
                        $equipment[$key]['workout_exercise_id'] = $model->id;
                        $equipment[$key]['created_by'] = $userData->id;
                        $equipment[$key]['updated_by'] = $userData->id;
                        $equipment[$key]['created_at'] = $currentDateTime;
                        $equipment[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseUsersAssignment::insert($equipment);
                }

                $setExIds = [];
                $setExercises = WorkoutSet::select("wse.id")
                                ->rightJoin('workout_set_exercises as wse', 'wse.workout_set_id','=','workout_sets.id')
                                ->where('workout_sets.workout_exercise_id', $model->id)->get()->toArray();
                foreach ($setExercises as $key => $data) {
                    array_push($setExIds, $data['id']);
                }
                // echo '<pre>';
                // print_r($setExIds);die;
                WorkoutSet::where('workout_exercise_id', $model->id)->update(['status' => 'deleted', 'updated_by'=>$userData->id]);
                WorkoutSetExercise::whereIn('id',$setExIds)->update(['status' => 'deleted']);
                if (! empty($post['sets'])) {
                    foreach ($post['sets'] as $key => $set) {
                        $setModel = WorkoutSet::where('workout_exercise_id', $model->id)
                                    ->where('set_no', $key)->first();
                        if(!empty($setModel)){
                            $setModel->status = 'active';
                            $setModel->updated_by = $userData->id;
                            $setModel->save();
                        }else{
                            $setModel = new WorkoutSet();
                            $setModel->set_no = $key;
                            $setModel->workout_exercise_id = $model->id;
                            $setModel->created_by = $userData->id;
                            $setModel->save();
                        }

                        // WorkoutSetExercise::where('workout_exercise_id', $model->id)->update(['status' => 'deleted', 'updated_by'=>$userData->id]);
                        if (is_array($set) && ! empty($set)) {
                            foreach ($set as $setExercise) {
                                $exerciseId = ! empty($setExercise['exercise']) ? $setExercise['exercise'] : 0;
                                $workoutSetExercise = WorkoutSetExercise::where('workout_set_id', $setModel->id)->where('workout_exercise_id', $exerciseId)->first();
                                if(!empty($workoutSetExercise)){
                                    $workoutSetExercise->status = 'active';
                                    $workoutSetExercise->save();
                                }else{
                                    $workoutSetExercise = new WorkoutSetExercise();
                                    $workoutSetExercise->workout_set_id = $setModel->id;
                                    $workoutSetExercise->workout_exercise_id = ! empty($setExercise['exercise']) ? $setExercise['exercise'] : 0;
                                    $workoutSetExercise->no_of_reps = ! empty($setExercise['reps']) ? $setExercise['reps'] : 0;
                                    $workoutSetExercise->created_by = $userData->id;
                                    $workoutSetExercise->save();
                                }
                            }
                        }

                    }
                }


                $groups = [];
                WorkoutExerciseGroup::where('workout_exercise_id', $model->id)->delete();
                if (! empty($request->group_ids)) {
                    foreach ($request->group_ids as $key => $id) {
                        $groups[$key]['group_id'] = $id;
                        $groups[$key]['workout_exercise_id'] = $model->id;
                        $groups[$key]['created_by'] = $userData->id;
                        $groups[$key]['updated_by'] = $userData->id;
                        $groups[$key]['created_at'] = $currentDateTime;
                        $groups[$key]['updated_at'] = $currentDateTime;
                    }
                    WorkoutExerciseGroup::insert($groups);
                }
                // WorkoutSet::where('workout_exercise_id', $model->id)->delete();
                // if (! empty($post['sets'])) {
                //     foreach ($post['sets'] as $key => $set) {
                //         $setModel = new WorkoutSet();
                //         $setModel->set_no = $key;
                //         $setModel->workout_exercise_id = $model->id;
                //         $setModel->created_by = $userData->id;
                //         $setModel->save();

                //         if (is_array($set) && ! empty($set)) {
                //             foreach ($set as $setExercise) {
                //                 $workoutSetExercise = new WorkoutSetExercise();
                //                 $workoutSetExercise->workout_set_id = $setModel->id;
                //                 $workoutSetExercise->workout_exercise_id = ! empty($setExercise['exercise']) ? $setExercise['exercise'] : 0;
                //                 $workoutSetExercise->no_of_reps = ! empty($setExercise['reps']) ? $setExercise['reps'] : 0;
                //                 $workoutSetExercise->created_by = $userData->id;
                //                 $workoutSetExercise->save();
                //             }
                //         }
                //     }
                // }
                DB::commit();

                return $model;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Detail for exercise
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function detailExercise($request)
    {
        try {
            $userData = getUser();
            $detail = WorkoutExercise::where('id', $request->id)->with('difficulties', 'ageRanges', 'sports', 'categories', 'equipments', 'media')->where([['type', '=', 'exercise'], ['status', '!=', 'deleted']])->first();

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

     /**
     * Add Record for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveWorkoutGoal($request)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $fromDate = getTodayDate('Y-m-d');
            $workoutGoal = self::findOneGoal([['id','=',$request->id]]);
            $previousData = UserWorkoutGoal::where('user_id', $userData->id)->latest('id')->first();
            if(!empty($workoutGoal)){
                UserWorkoutGoal::where('user_id', $userData->id)->update(['status' => 'inactive']);
                $days = !empty($workoutGoal->days) ? $workoutGoal->days : 0;
                $toDate = addSubDate($fromDate, $days, 'Y-m-d', 'add');
                $model = new UserWorkoutGoal();
                $model->workout_goal_id = $request->id;
                $model->from_date = $fromDate;
                $model->to_date = $toDate;
                $model->user_id = $userData->id;
                $model->created_by = $userData->id;
                $model->updated_by = $userData->id;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->save();

                $reward = [
                    'feature_key' => 'create-workout-goal',
                    'module_id' => $model->id,
                    'module' => 'workout-builder',
                    'allow_multiple' => 1,
                ];
                if(empty($workoutGoal) || $workoutGoal->to_date < $fromDate){
                    $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'create-workout-goal'] , ['reward_game.game']);

                    if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                        RewardRepository::saveUserReward($reward);
                    }
                }

                DB::commit();
                return ['current_data'=>$model,'previous_data'=>$previousData];
            }else{
                throw new Exception('Invalid goal.', 1);
                DB::rollBack();
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function getUserGoals($request = null)
    {
        $userData = getUser();
        $userId = (!empty($request) && !empty($request->athlete_id)) ? $request->athlete_id : $userData->id;
        $currentDate = getLocalDateTime('','Y-m-d');
        $myGoal = self::findUserGoals([['id',$userId], ['to_date','>=',$currentDate], ['status','=', 'active']],['workoutGoal']);
        return $myGoal;
    }

    // public static function getUserCurrentGoal()
    // {
    //     $userData = getUser();
    //     $currentDate = getLocalDateTime('','Y-m-d');
    //     $myGoal = self::findOneUserGoal([['id','=',$userData->id], ['to_date','>=',$currentDate], ['status','=', 'active']],['workoutGoal']);
    //     return $myGoal;
    // }

    public static function getTodayWorkoutGoal($request)
    {
        $userData = getUser();
        $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
        $currentDate = getTodayDate('Y-m-d');
        $todayWorkout = UserWorkoutGoal::select(
                    'user_workout_goals.id',
                    'user_workout_goals.from_date',
                    'user_workout_goals.to_date',
                    'wg.workouts',
                    'wg.days',
                    'wg.id as goal_id'
                    )
                    ->join('workout_goals as wg', 'wg.id','=','user_workout_goals.workout_goal_id')
                    ->where('user_workout_goals.user_id',$userId)
                    ->where('user_workout_goals.from_date', '<=', $currentDate)
                    ->where('user_workout_goals.to_date', '>=', $currentDate)
                    ->where('user_workout_goals.status', 'active')->first();
        return $todayWorkout;
    }

    // public static function completeTodayWorkout($request)
    // {
    //     $userData = getUser();
    //     $currentDate = getLocalDateTime('','Y-m-d');
    //     $todayDate = getTodayDate('Y-m-d');
    //     $todayData = [];
    //     $todayWorkout = UserWorkoutGoal::select(
    //                 'user_workout_goals.id',
    //                 'user_workout_goals.from_date',
    //                 'user_workout_goals.to_date',
    //                 'wg.workouts',
    //                 'wg.days'
    //                 )
    //                 ->with(['userWorkoutGoalLog'=> function($q) use($currentDate) {
    //                         $q->where('date', '=', $currentDate);
    //                         $q->where('status', '=', 'completed');
    //                     }])
    //                 ->join('workout_goals as wg', 'wg.id','=','user_workout_goals.workout_goal_id')
    //                 ->where('user_workout_goals.id',$request->id)
    //                 ->where('user_workout_goals.user_id',$userData->id)
    //                 ->where('user_workout_goals.from_date', '<=', $currentDate)
    //                 ->where('user_workout_goals.to_date', '>=', $currentDate)
    //                 ->where('user_workout_goals.status', 'active')->first();
    //     if(!empty($todayWorkout)){
    //         foreach ($todayWorkout->userWorkoutGoalLog as $log) {
    //             if ($log['date'] == $currentDate) {
    //                 $todayData = $log;
    //             }
    //         }
    //         if(empty($todayData)){
    //             $workoutLog = new UserWorkoutGoalLog();
    //             $workoutLog->user_workout_goal_id = $request->id;
    //             $workoutLog->date = $currentDate;
    //             $workoutLog->status = 'completed';
    //             $workoutLog->created_by = $userData->id;
    //             $workoutLog->updated_by = $userData->id;
    //             $workoutLog->created_at = $todayDate;
    //             $workoutLog->updated_at = $todayDate;
    //             $workoutLog->save();
    //             return true;
    //         }else{
    //             throw new Exception('You already completed the today workout.', 1);
    //         }

    //     }else{
    //         throw new Exception('Invalid workout.', 1);
    //     }

    // }

    public static function getWorkoutGoalDetail($request){

        $userData = getUser();
        $currentDate = getLocalDateTime('','Y-m-d');
        $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
        $workouts = [];
        $userWorkoutGoal = UserWorkoutGoal::with('workoutGoal')
        ->where([['user_id', $userId], ['to_date','>=',$currentDate],['from_date','<=',$currentDate], ['status','=', 'active']])
        ->first();
        if(!empty($userWorkoutGoal)){
            $workouts = FitnessProfile::where('user_id', $userId)->where([
                ['date', '>=', $userWorkoutGoal->from_date],
                ['date', '<=', $userWorkoutGoal->to_date],
                ['exercise', '!=', 'DAY_OFF'],
                ['status', '!=', 'deleted']
                ])
                //->whereNotIn('day', ['REPEAT', 'WEEK_FIRST_DAY'])
                ->get();
        }
        // echo '<pre>';
        // print_r($userWorkoutGoal);
        // die;
        $completedWorkouts = $workouts && count($workouts ) > 0 ? $workouts->where('is_completed', 1)->count() : 0;

        return [
            'workouts'=>$workouts,
            'userWorkoutGoal'=>$userWorkoutGoal,
            'completed_workouts' => $completedWorkouts,
        ];
    }

    public static function findAllVimeoVideos($request){
        try{
            $videos = VimeoService::findAllVimeoVideos($request);
            return $videos;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    


        /**
     * Add Record for custom label
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveCustomWorkoutName($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $selectedDay = strtoupper($post['day']); // e.g. "monday", "tuesday", etc.
            $today = new DateTime(); // Today’s date
            $todayDayName = strtoupper($today->format('l')); // Day name like "monday"
            // If today is the selected day, use today. Otherwise, get next occurrence of the selected day.
            if ($selectedDay == $todayDayName) {
                $scheduledDate = $today;
            } else {
                $scheduledDate = new DateTime("next $selectedDay");
            }
            $model = new CustomWorkoutName();
            $model->title = $post['title'];
            $model->scheduled_date = $scheduledDate->format('Y-m-d');
            $model->day = $post['day'];
            $model->reminder_time = $post['reminder_time'];
            $model->user_id = $userData->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    
    /**
     * Load record list for today custom workout labels
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadListCustomWorkoutName($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $todayDate = getTodayDate('Y-m-d');
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = CustomWorkoutName::where([['status', '!=', 'deleted']]);

            if($userData->user_type != 'admin'){
                $list = $list->where([['user_id' ,  $userData->id]]);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('is_completed', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    /**
     * Update Record for Custom workout label
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateCustomWorkoutName($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOneCustomWorkoutName(['id' => $request->id]);
            if (! empty($model)) {
                $selectedDay = strtoupper($post['day']); // e.g. "monday", "tuesday", etc.
                $today = new DateTime(); // Today’s date
                $todayDayName = strtoupper($today->format('l')); // Day name like "monday"
                // If today is the selected day, use today. Otherwise, get next occurrence of the selected day.
                if ($selectedDay == $todayDayName) {
                    $scheduledDate = $today;
                } else {
                    $scheduledDate = new DateTime("next $selectedDay");
                }
                $model->title = $post['title'];
                $model->reminder_time = $post['reminder_time'];
                $model->day = $post['day'];
                $model->scheduled_date = $scheduledDate->format('Y-m-d');
                $model->user_id = $userData->id;
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                return $model;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    

    /**
     * Change record status by Id for custom workout labels
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeCustomWorkoutNameStatus($request)
    {
        try {
            $model = CustomWorkoutName::where(['id' => $request->id])->first();
            if (! empty($model)) {
                if($request->status == 'complete'){
                    $model->is_completed = 1;
                }else{
                    $model->status = $request->status;
                }
                $model->save();
                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public static function sendCustomWorkoutReminder(){
        try{
            $currentDate = getTodayDate('Y-m-d');
            $currentTime = getTodayDate('H:i:00');
            $workouts = CustomWorkoutName::where(['scheduled_date' => $currentDate])->get();
            
            if(!empty($workouts)){
                foreach($workouts as $workout){
                    $placeholder = $workout->title;
                    $messageBody = "Reminder: It's time for your $placeholder workout. Stay committed to your wellness goals! 💪";
                    $user = $workout->user;
                    if($workout->reminder_time == $currentTime){
                        $userData = [
                            'name' => $user->first_name,
                            'email' => $user->email,
                            'title' => $placeholder,
                            'cell_phone_number' => $user->cell_phone_number ?? '',
                            'message' => $messageBody,
                        ];
                        UserCalendarReminderJob::dispatch($userData);
                    }
                }
            }
        }catch(\Exception $ex){
            throw $ex;
        }
    }

}
