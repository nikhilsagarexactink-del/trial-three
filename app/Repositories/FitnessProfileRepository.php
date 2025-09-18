<?php

namespace App\Repositories;

use App\Models\FitnessProfile;
use App\Models\FitnessProfileExercise;
use App\Models\FitnessSetting;
use App\Models\FitnessProfileDaySetting;
use App\Models\UserModuleNotificationSetting;
use App\Repositories\NotificationRepository;
use App\Models\User;
use App\Models\WorkoutSet;
use App\Services\ChallengeLogger;
use Carbon\Carbon;
use Config;
use DB;
use Exception;

class FitnessProfileRepository
{

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return FitnessProfileDaySetting
     */
    public static function findProfileDaySetting($where, $with = [])
    {
        return FitnessProfileDaySetting::with($with)->where($where)->first();
    }
    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return FitnessProfile
     */
    public static function findAllProfileDetail($where, $with = [])
    {
        return FitnessProfile::with($with)->where($where)->get();
    }

    /**
     * Get workoutes
     *
     * @param  array  $where
     * @param  array  $with
     * @return FitnessSetting
     */
    public static function getSettings($where = '', $with = [])
    {
        try {
            $userData = getUser();
            $settings = FitnessSetting::with($with)->where('user_id', $userData->id)->get();

            return $settings->toArray();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save Fitness Settings
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveSettings($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            // echo '<pre>';
            // print_r($post);die;
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $todayDay = getLocalDateTime('', 'l');
            $todayDay = strtoupper($todayDay);

            $settings = [];
            $fitnessData = [];
            FitnessSetting::where('user_id', $userData->id)->delete();
            if (! empty($post)) {
                $staticExercises = FitnessProfileExercise::where('status', '!=', 'deleted')->get()->toArray();
                $workouts = WorkoutBuilderRepository::loadAllWorkoutList($request)->toArray();

                foreach ($post as $key => $data) {
                    if ($key != 'guard') {
                        if (! empty($data['static_exercise_id'])) {
                            foreach ($data['static_exercise_id'] as $childKey => $dayData) {
                                if (! empty($dayData)) {
                                    $filteredStaticExercise = array_filter($staticExercises, function ($item) use ($dayData) {
                                        if ($item['id'] == $dayData) {
                                            return true;
                                        }

                                        return false;
                                    });
                                    $filteredStaticExercise = array_values($filteredStaticExercise);
                                    array_push($settings, [
                                        'day' => $key,
                                        'exercise_type' => 'static',
                                        'duration' => ! empty($data['static_duration'][$dayData]) ? $data['static_duration'][$dayData] : 0,
                                        'type' => 'static',
                                        'value' => ! empty($filteredStaticExercise) ? $filteredStaticExercise[0]['title'] : null,
                                        'fitness_profile_exercise_id' => $dayData,
                                        'workout_exercise_id' => null,
                                        'user_id' => $userData->id,
                                        'created_by' => $userData->id,
                                        'updated_by' => $userData->id,
                                        'created_at' => $currentDateTime,
                                        'updated_at' => $currentDateTime,
                                    ]);

                                    if ($todayDay == $key) {
                                        array_push($fitnessData, [
                                            'day' => $key,
                                            'date' => $currentDate,
                                            'type' => 'static',
                                            'exercise' => ! empty($filteredStaticExercise) ? $filteredStaticExercise[0]['title'] : null,
                                            'workout_exercise_id' => null,
                                            'fitness_profile_exercise_id' => $dayData,
                                            'workout_set_id' => null,
                                            'set_no' => 0,
                                            'duration' => ! empty($data['static_duration'][$dayData]) ? $data['static_duration'][$dayData] : 0,
                                            'user_id' => $userData->id,
                                            'created_by' => $userData->id,
                                            'updated_by' => $userData->id,
                                            'created_at' => $currentDateTime,
                                            'updated_at' => $currentDateTime,
                                        ]);
                                    }
                                }
                            }
                        }
                        if (! empty($data['custom_exercise_id'])) {

                            foreach ($data['custom_exercise_id'] as $childKey => $dayData) {
                                if (! empty($dayData)) {

                                    $filteredCustomExercise = array_filter($workouts, function ($item) use ($dayData) {
                                        if ($item['id'] == $dayData) {
                                            return true;
                                        }

                                        return false;
                                    });
                                    $filteredCustomExercise = array_values($filteredCustomExercise);

                                    array_push($settings, [
                                        'day' => $key,
                                        'exercise_type' => ! empty($data['custom_exercise_types'][$dayData]) ? $data['custom_exercise_types'][$dayData] : null,
                                        'duration' => ! empty($data['custom_duration'][$dayData]) ? $data['custom_duration'][$dayData] : 0,
                                        'type' => 'custom',
                                        'value' => ! empty($filteredCustomExercise) ? $filteredCustomExercise[0]['name'] : null,
                                        'fitness_profile_exercise_id' => null,
                                        'workout_exercise_id' => $dayData,
                                        'user_id' => $userData->id,
                                        'created_by' => $userData->id,
                                        'updated_by' => $userData->id,
                                        'created_at' => $currentDateTime,
                                        'updated_at' => $currentDateTime,
                                    ]);
                                    // echo '<pre>';
                                    // print_r($settings);die;
                                    //echo $todayDay.'=='. $key;die;
                                    if ($todayDay == $key) {
                                        //echo 3;die;
                                        $workoutSetExercises = WorkoutSet::select(
                                            'workout_sets.workout_exercise_id',
                                            'workout_sets.id AS workout_set_id',
                                            'workout_sets.set_no',
                                            'we.name',
                                            'we.duration',
                                            'workout_sets.id AS set_id',
                                            'workout_sets.set_no'
                                        )
                                        ->rightJoin('workout_set_exercises AS wse', 'wse.workout_set_id', '=', 'workout_sets.id')
                                        ->join('workout_exercises AS we', 'we.id', '=', 'wse.workout_exercise_id')
                                        ->where('workout_sets.workout_exercise_id', $dayData)->get();
                                        foreach ($workoutSetExercises as $seKey => $workoutSetExercise) {
                                            array_push($fitnessData, [
                                                'day' => $key,
                                                'date' => $currentDate,
                                                'type' => 'custom',
                                                'exercise' => ! empty($workoutSetExercise) ? $workoutSetExercise['name'] : null,
                                                'workout_exercise_id' => $dayData,
                                                'fitness_profile_exercise_id' => null,
                                                'workout_set_id' => ! empty($workoutSetExercise) ? $workoutSetExercise['workout_set_id'] : null,
                                                'set_no' => ! empty($workoutSetExercise) ? $workoutSetExercise['set_no'] : 0,
                                                'duration' => ! empty($workoutSetExercise) ? $workoutSetExercise['duration'] : 0,
                                                'user_id' => $userData->id,
                                                'created_by' => $userData->id,
                                                'updated_by' => $userData->id,
                                                'created_at' => $currentDateTime,
                                                'updated_at' => $currentDateTime,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        if (! empty($data['custom_sessions'])) {
                            foreach ($data['custom_sessions'] as $childKey => $dayData) {
                                if (! empty($dayData)) {
                                    array_push($settings, [
                                        'day' => $key,
                                        'exercise_type' => ! empty($data['custom_session_types'][$dayData]) ? $data['custom_session_types'][$dayData] : null,
                                        'duration' => 0,
                                        'type' => 'session',
                                        'value' => $dayData,
                                        'fitness_profile_exercise_id' => null,
                                        'workout_exercise_id' => null,
                                        'user_id' => $userData->id,
                                        'created_by' => $userData->id,
                                        'updated_by' => $userData->id,
                                        'created_at' => $currentDateTime,
                                        'updated_at' => $currentDateTime,
                                    ]);

                                    if ($todayDay == $key) {
                                        array_push($fitnessData, [
                                            'day' => $key,
                                            'date' => $currentDate,
                                            'type' => 'session',
                                            'exercise' => $dayData,
                                            'workout_exercise_id' => null,
                                            'fitness_profile_exercise_id' => null,
                                            'workout_set_id' => null,
                                            'set_no' => 0,
                                            'duration' => 0,
                                            'user_id' => $userData->id,
                                            'created_by' => $userData->id,
                                            'updated_by' => $userData->id,
                                            'created_at' => $currentDateTime,
                                            'updated_at' => $currentDateTime,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                // echo '<pre>';
                // print_r($fitnessData);
                // exit;
                FitnessSetting::insert($settings);
                if (! empty($fitnessData)) {
                    FitnessProfile::where('user_id', $userData->id)->where('is_completed', 0)->where('day', $todayDay)->delete();
                    //FitnessProfile::insert($fitnessData);
                    foreach ($fitnessData as $key => $fitnessArr) {
                        $fitness = FitnessProfile::where('user_id', $userData->id)->where('date', $fitnessArr['date'])->where('exercise', $fitnessArr['exercise']);
                        if($fitnessArr['type']=='custom'){
                            $fitness->where('set_no', $fitnessArr['set_no'])->where('workout_exercise_id', $fitnessArr['workout_exercise_id']);
                        }
                        $fitness = $fitness->first();
                        if (! empty($fitness)) {
                            $fitness->duration = $fitnessArr['duration'];
                            $fitness->save();
                        } else {
                            FitnessProfile::insert([$fitnessArr]);
                        }
                    }
                }
            }

            UserModuleNotificationSetting::where([['user_id', $userData->id], ['module_id', $post['module_id']]])->delete();
            if (! empty($post['notification_type'])) {
                $notification = new UserModuleNotificationSetting();
                $notification->user_id = $userData->id;
                $notification->module_id = $post['module_id'];
                $notification->master_notification_type_id = $post['notification_type'];
                $notification->reminder_time = $post['reminder_time'];
                $notification->created_by = $userData->id;
                $notification->updated_by = $userData->id;
                $notification->created_at = $currentDateTime;
                $notification->updated_at = $currentDateTime;
                $notification->save();
            }

            $daySettings = FitnessProfileDaySetting::where('user_id',$userData->id)->first();
            if(!empty($daySettings)){
                $daySettings->week_first_day = !empty($post['week_first_day']) ? $post['week_first_day'] : null;
                $daySettings->repeat = !empty($post['repeat']) ? $post['repeat'] : null;
                $daySettings->save();
            }else{
                $daySettings = new FitnessProfileDaySetting();
                $daySettings->week_first_day = !empty($post['week_first_day']) ? $post['week_first_day'] : null;
                $daySettings->repeat = !empty($post['repeat']) ? $post['repeat'] : null;
                $daySettings->user_id = $userData->id;
                $daySettings->created_by = $userData->id;
                $daySettings->created_at = $currentDateTime;
                $daySettings->updated_at = $currentDateTime;
                $daySettings->save();
            }
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

     /**
     * Save Fitness Settings Exercise
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveSettingCustomExercise($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            // echo '<pre>';
            // print_r($post);die;
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $todayDay = getLocalDateTime('', 'l');
            $todayDay = strtoupper($todayDay);

            $day = !empty($post['day']) ? $post['day'] : null;
            $fitnessData = [];
            $fitnessSetting = new FitnessSetting();
            $fitnessSetting->day = $day;
            $fitnessSetting->value = !empty($post['value']) ? $post['value'] : null;
            $fitnessSetting->type = !empty($post['type']) ? $post['type'] : null;
            $fitnessSetting->exercise_type = !empty($post['exercise_type']) ? $post['exercise_type'] : null;
            $fitnessSetting->duration = !empty($post['duration']) ? $post['duration'] : 0;
            $fitnessSetting->user_id = $userData->id;
            $fitnessSetting->created_by = $userData->id;
            $fitnessSetting->updated_by = $userData->id;
            $fitnessSetting->created_at = $currentDateTime;
            $fitnessSetting->updated_at = $currentDateTime;
            $fitnessSetting->save();
            if ($todayDay == $day) {
                $workOutId = !empty($post['workout_exercise_id']) ? $post['workout_exercise_id'] : null;
                if($post['type'] == 'custom' && $workOutId) {
                    $workoutSetExercises = WorkoutSet::select(
                        'workout_sets.workout_exercise_id',
                        'workout_sets.id AS workout_set_id',
                        'workout_sets.set_no',
                        'we.name',
                        'we.duration',
                        'workout_sets.id AS set_id',
                        'workout_sets.set_no'
                    )
                    ->rightJoin('workout_set_exercises AS wse', 'wse.workout_set_id', '=', 'workout_sets.id')
                    ->join('workout_exercises AS we', 'we.id', '=', 'wse.workout_exercise_id')
                    ->where('workout_sets.workout_exercise_id', $workOutId)->get();
                    foreach ($workoutSetExercises as $seKey => $workoutSetExercise) {
                        array_push($fitnessData, [
                            'day' => $day,
                            'date' => $currentDate,
                            'type' => 'custom',
                            'exercise' => ! empty($workoutSetExercise) ? $workoutSetExercise['name'] : null,
                            'workout_exercise_id' => $workOutId,
                            'fitness_profile_exercise_id' => null,
                            'workout_set_id' => ! empty($workoutSetExercise) ? $workoutSetExercise['workout_set_id'] : null,
                            'set_no' => ! empty($workoutSetExercise) ? $workoutSetExercise['set_no'] : 0,
                            'duration' => ! empty($workoutSetExercise) ? $workoutSetExercise['duration'] : 0,
                            'user_id' => $userData->id,
                            'created_by' => $userData->id,
                            'updated_by' => $userData->id,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ]);
                    }
                } if($post['type'] == 'session'){
                    array_push($fitnessData, [
                        'day' => $day,
                        'date' => $currentDate,
                        'type' => 'session',
                        'exercise' => !empty($post['value']) ? $post['value'] : null,
                        'workout_exercise_id' => null,
                        'fitness_profile_exercise_id' => null,
                        'workout_set_id' => null,
                        'set_no' => 0,
                        'duration' => 0,
                        'user_id' => $userData->id,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                    ]);
                }
            }

            if (! empty($fitnessData)) {
                // FitnessProfile::where('user_id', $userData->id)
                //                 ->where('date', $currentDate)
                //                 ->where('is_completed', 0)
                //                 ->where('day', $todayDay)->delete();
                foreach ($fitnessData as $key => $fitnessArr) {
                    $fitness = FitnessProfile::where('user_id', $userData->id)
                                ->where('date', $fitnessArr['date'])
                                ->where('exercise', $fitnessArr['exercise']);
                    if($fitnessArr['type']=='custom'){
                        $fitness->where('set_no', $fitnessArr['set_no'])->where('workout_exercise_id', $fitnessArr['workout_exercise_id']);
                    }
                    $fitness = $fitness->first();
                    if (! empty($fitness)) {
                        $fitness->duration = $fitnessArr['duration'];
                        $fitness->save();
                    } else {
                        FitnessProfile::insert([$fitnessArr]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public static function saveSettingStaticExercise($request){
        DB::beginTransaction();
        try{
            $post = $request->all();
            // echo '<pre>';
            // print_r($post);die;
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $todayDay = getLocalDateTime('', 'l');
            $todayDay = strtoupper($todayDay);

            $settingData = [];
            $fitnessData = [];
            $staticExercises = FitnessProfileExercise::where('status', '!=', 'deleted')->get()->toArray();
            FitnessSetting::where('exercise_type', 'static')->where('day', $post['day'])->where('user_id', $userData->id)->delete();
            foreach ($post as $key => $data) {
                if ($key != 'guard' && $key !='day') {
                    foreach ($data['exercise'] as $childKey => $exeId) {
                        $filteredStaticExercise = array_filter($staticExercises, function ($item) use ($exeId) {
                            if ($item['id'] == $exeId) {return true;}
                            return false;
                        });
                        $filteredStaticExercise = array_values($filteredStaticExercise);
                        array_push($settingData, [
                            'day' => $key,
                            'exercise_type' => 'static',
                            'duration' => ! empty($data['duration'][$exeId]) ? $data['duration'][$exeId] : 0,
                            'type' => 'static',
                            'value' => ! empty($filteredStaticExercise) ? $filteredStaticExercise[0]['title'] : null,
                            'fitness_profile_exercise_id' => $exeId,
                            'workout_exercise_id' => null,
                            'user_id' => $userData->id,
                            'created_by' => $userData->id,
                            'updated_by' => $userData->id,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ]);

                        if ($todayDay == $key) {
                            array_push($fitnessData, [
                                'day' => $key,
                                'date' => $currentDate,
                                'type' => 'static',
                                'exercise' => ! empty($filteredStaticExercise) ? $filteredStaticExercise[0]['title'] : null,
                                'workout_exercise_id' => null,
                                'fitness_profile_exercise_id' => $exeId,
                                'workout_set_id' => null,
                                'set_no' => 0,
                                'duration' => ! empty($data['duration'][$exeId]) ? $data['duration'][$exeId] : 0,
                                'user_id' => $userData->id,
                                'created_by' => $userData->id,
                                'updated_by' => $userData->id,
                                'created_at' => $currentDateTime,
                                'updated_at' => $currentDateTime,
                            ]);
                        }
                    }
                }
            }
            if(!empty($settingData)){
                FitnessSetting::insert($settingData);
            }
            if (! empty($fitnessData)) {
                FitnessProfile::where('user_id', $userData->id)
                ->where('type', 'static')
                ->where('date', $currentDate)
                ->where('is_completed', 0)
                ->where('day', $todayDay)->delete();
                foreach ($fitnessData as $key => $fitnessArr) {
                    $fitness = FitnessProfile::where('user_id', $userData->id)
                                ->where('date', $fitnessArr['date'])
                                ->where('exercise', $fitnessArr['exercise'])->first();
                    if (! empty($fitness)) {
                        $fitness->duration = $fitnessArr['duration'];
                        $fitness->save();
                    } else {
                        FitnessProfile::insert([$fitnessArr]);
                    }
                }
            }
            // echo '<pre>';
            // print_r($settingData);die;
            DB::commit();
            return true;
        }catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
    /**
     * Get workout detail
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function getTodayWorkOutDetail($request)
    {
        try {
            $post = $request->all();
            $userData =  getUser();
            $date = $request->date;
            $userId = !empty($post['athlete_id'])?$post['athlete_id']:$userData->id;
            $todayPendingWorkout = 0;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $daySettings = FitnessProfileDaySetting::where('user_id', $userId)->where('status', 'active')->first();
            $startDay = ! empty($daySettings) && ! empty($daySettings['week_first_day']) ? $daySettings['week_first_day'] : '';
            $weekDaysArr = getWeekDays($startDay);
            $weekDays = [];
            foreach ($weekDaysArr as $wday) {
                $weekDays[$wday] = [];
            }

            $workoutData = FitnessProfile::select(
                'fitness_profiles.*',
                //'fp.title AS exercise',
                //DB::raw('CASE WHEN fp.title IS NOT NULL THEN fp.title ELSE we .name END AS exercise'),
                DB::raw('CASE WHEN fitness_profiles.type="custom" THEN fp.title ELSE fitness_profiles.exercise END AS exercise'),
            )->leftJoin('fitness_profile_exercises AS fp', 'fp.id', '=', 'fitness_profiles.fitness_profile_exercise_id')
                        ->where('fitness_profiles.date', $date)
                        ->where('fitness_profiles.exercise', '!=', 'DAY_OFF')
                        //->whereNotIn('fitness_profiles.day', ['REPEAT', 'WEEK_FIRST_DAY'])
                        ->where('fitness_profiles.user_id', $userId)
                        //->where('fitness_profiles.type', 'static')
                        ->whereIn('fitness_profiles.type', ['static','session'])
                        ->where('fitness_profiles.status', '!=', 'deleted')->get();

            $newUser = FitnessProfile::where('user_id', $userId)
                        ->where('exercise', '!=', 'DAY_OFF')
                        //->whereNotIn('day', ['REPEAT', 'WEEK_FIRST_DAY'])
                        ->where('status', '!=', 'deleted')->first();
            $isNewUser = ! empty($newUser) ? 0 : 1;
            $weekArr = getWeekStartEndDate();
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $weekStartDate = $weekArr['start_date'];
            $weekEndDate = $weekArr['end_date'];

            foreach ($workoutData as $key => $workout) {
                $workoutData[$key]['completed_time_txt'] = '';
                if (! empty($workout['from_time']) && ! empty($workout['to_time']) && $workout['is_completed'] == 1) {
                    $completetTime = ! empty($workout['completed_time']) ? $workout['completed_time'] : '00:00';
                    $timeArr = explode(':', $completetTime);
                    $timeTxt = 'Completed in ';

                    if (! empty($timeArr[0])) {
                        $timeTxt .= $timeArr[0].' minutes ';
                    }
                    if (! empty($timeArr[1])) {
                        $timeTxt .= $timeArr[1].' seconds ';
                    }
                    // No time for session. default text added for message
                    $workoutData[$key]['completed_time_txt'] = ($workout['type']=='session') ? 'Completed' : $timeTxt;
                }
                if ($workout['is_completed'] == 0) {
                    $todayPendingWorkout++;
                }
                $workout['exercise'] = ! empty($workout['exercise']) ? $workout['exercise'] : '';
            }

            $customExerciseDataArr = [];
            $customExerciseData = FitnessProfile::select(
                'fitness_profiles.*',
                'pwe.name AS workout',
                'ws.set_no',
            )
            ->join('workout_exercises AS pwe', 'pwe.id', '=', 'fitness_profiles.workout_exercise_id')
            ->join('workout_sets AS ws', 'ws.id', '=', 'fitness_profiles.workout_set_id')
            ->where('fitness_profiles.date', $date)
            ->where('fitness_profiles.exercise', '!=', 'DAY_OFF')
            //->whereNotIn('fitness_profiles.day', ['REPEAT', 'WEEK_FIRST_DAY'])
            ->where('fitness_profiles.user_id', $userId)
            ->where('fitness_profiles.type', 'custom')
            ->where('fitness_profiles.status', '!=', 'deleted')
            ->whereNotNull('ws.set_no')->get();

            foreach ($customExerciseData as $key => $workout) {
                $customExerciseData[$key]['completed_time_txt'] = '';
                if (! empty($workout['from_time']) && ! empty($workout['to_time']) && $workout['is_completed'] == 1) {
                    $completetTime = ! empty($workout['completed_time']) ? $workout['completed_time'] : '00:00';
                    $timeArr = explode(':', $completetTime);
                    $timeTxt = 'Completed in ';

                    if (! empty($timeArr[0])) {
                        $timeTxt .= $timeArr[0].' minutes ';
                    }
                    if (! empty($timeArr[1])) {
                        $timeTxt .= $timeArr[1].' seconds ';
                    }
                    $customExerciseData[$key]['completed_time_txt'] = $timeTxt;
                }
                if ($workout['is_completed'] == 0) {
                    $todayPendingWorkout++;
                }
                $workout['exercise'] = ! empty($workout['exercise']) ? $workout['exercise'] : '';
            }
            $groupSets = array();
            // foreach ( $customExerciseData as $value ) {
            //     $groups[$value['workout_exercise_id']][] = $value->toArray();
            // }
            foreach ($customExerciseData as $groupsKey=>$value) {
                $groupSets[$value['workout_exercise_id']]['workout'] = $value['workout'];
                $groupSets[$value['workout_exercise_id']]['sets'][$value['set_no']][] = $value;
            }


            $weekData = FitnessSetting::where('user_id', $userId)
                        //->whereNotIn('day', ['REPEAT', 'WEEK_FIRST_DAY'])
                        ->where('status', 'active')->get();

            foreach ($weekData as $data) {
                if (! empty($data->value) && $data->value != 'DAY_OFF') {
                    $weekDataArr = [
                        'day' => $data->day,
                        'exercise' => ! empty($data->value) ? $data->value : '',
                    ];
                    array_push($weekDays[$data['day']], $weekDataArr);
                }
            }

            return [
                'workoutData' => $workoutData,
                'customWorkoutData' => $groupSets,
                'isNewUser' => $isNewUser,
                'todayPendingWorkout' => $todayPendingWorkout,
                'weekData' => $weekDays,
            ];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Complete workout
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function markComplete($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDate = getTodayDate('Y-m-d');
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = FitnessProfile::where('id', $request->id)->where('user_id', $userData->id)->first();
            if (! empty($model)) {
                $startTimeOne = ! empty($request->from_time) ? $request->from_time : getTodayDate('H:i:s');
                $endTimeOne = ! empty($request->to_time) ? $request->to_time : getTodayDate('H:i:s');
                $toTimeOne = strtotime($currentDate.' '.$endTimeOne);
                $fromTimeOne = strtotime($currentDate.' '.$startTimeOne);
                $totaCompletedTime = ! empty($request->completed_time) ? $request->completed_time : '00:00'; //round(abs($toTimeOne - $fromTimeOne) / 60, 2);

                if($model->type == 'session'){// No time for session. default to_time added for calculation
                    $model->from_time = ! empty($post['to_time']) ? $post['to_time'] : null;
                } else {
                    $model->from_time = ! empty($post['from_time']) ? $post['from_time'] : null;
                }
                $model->to_time = ! empty($post['to_time']) ? $post['to_time'] : null;
                $model->note = ! empty($post['note']) ? $post['note'] : null;
                $model->completed_time = $totaCompletedTime;
                $model->is_completed = 1;
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();
                $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'complete-workout'] , ['reward_game.game']);
                $reward = [
                    'feature_key' => 'complete-workout',
                    'module_id' => $model->id,
                    'allow_multiple' => 1,
                ];
                if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                    RewardRepository::saveUserReward($reward);
                }
            } else {
                DB::rollback();
                throw new Exception('Record not found.', 1);
            }
            ChallengeLogger::log('workouts', $model);// If challenge exist so will be entry FitnessChallengeLog table
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Get next workout date
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function getNextWorkoutDate($request)
    {
        try {
            $userData = getUser();
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $fromDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
            $toDate = date('Y-m-d', strtotime('+7 day', strtotime($fromDate)));
            $settings = FitnessSetting::where('user_id', $userData->id)
                //->whereNotIn('day', ['DAY_OFF', 'REPEAT', 'WEEK_FIRST_DAY'])
                ->where('status', '!=', 'deleted')
                ->groupBy('day')->get()->toArray();

            $nextDateArr = [];
            $count = 0;
            while ($fromDate <= $toDate) {
                $day = strtoupper(formatDate($fromDate, 'l'));
                $availableDay = array_filter($settings, function ($item) use ($day) {
                    if ($item['day'] === $day) {
                        return true;
                    }

                    return false;
                });

                if (count($availableDay) > 0) {
                    $availableDay = array_values($availableDay)[0];
                    $availableDay['date'] = $fromDate;
                    break;
                }
                $count++;
                $fromDate = date('Y-m-d', strtotime('+1 day', strtotime($fromDate)));
            }

            return $availableDay;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * save Log Cron
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveLogCron()
    {
        try {
            $currentDateTime = getLocalDateTime('', 'Y-m-d H:i:s');
            $nextDate = getLocalDateTime('', 'Y-m-d');
            $day = strtoupper(formatDate($nextDate, 'l'));
            // $users = User::where('user_type', 'athlete')->where('id', $user->id)->where('status', '!=', 'deleted')->get();
            $users = User::where('user_type', 'athlete')->where('status', '!=', 'deleted')->get();
            foreach ($users as $user) {
                $model = FitnessProfile::where('date', $nextDate)->where('user_id', $user->id)
                    ->where('status', '!=', 'deleted')->first();

                if (empty($model)) {
                    $settings = FitnessSetting::where('day', $day)
                        ->with(['customExercises','customExercises.sets','customExercises.sets.workoutSetExercises', 'customExercises.sets.workoutSetExercises.exercise'])
                        ->whereNotIn('day', ['DAY_OFF'])//['DAY_OFF', 'REPEAT', 'WEEK_FIRST_DAY']
                        ->where('user_id', $user->id)
                        ->where('status', '!=', 'deleted')
                        ->groupBy('value', 'day')->get()->toArray();

                    if (! empty($settings)) {

                        $exercises = [];
                        $setExercises = [];
                        foreach ($settings as $key => $setting) {
                            if($setting['type']=='static'){
                                $exercises[$key]['day'] = $day;
                                $exercises[$key]['date'] = $nextDate;
                                $exercises[$key]['from_time'] = null;
                                $exercises[$key]['to_time'] = null;
                                $exercises[$key]['exercise'] = $setting['value'];
                                $exercises[$key]['fitness_profile_exercise_id'] = !empty($setting['fitness_profile_exercise_id']) ? $setting['fitness_profile_exercise_id'] : 0;
                                $exercises[$key]['workout_exercise_id'] = !empty($setting['workout_exercise_id']) ? $setting['workout_exercise_id'] : 0;
                                $exercises[$key]['workout_set_id'] = null;
                                $exercises[$key]['set_no'] = 0;
                                $exercises[$key]['type'] = $setting['type'];
                                $exercises[$key]['duration'] = ! empty($setting['duration']) ? $setting['duration'] : 0;
                                $exercises[$key]['user_id'] = $user->id;
                                $exercises[$key]['created_by'] = $user->id;
                                $exercises[$key]['updated_by'] = $user->id;
                                $exercises[$key]['created_at'] = $currentDateTime;
                                $exercises[$key]['updated_at'] = $currentDateTime;
                            } else if($setting['type']=='custom'){
                                // echo '<pre>';
                                // print_r($setting['custom_exercises']);die;
                                if(!empty($setting['custom_exercises'])){
                                    foreach ($setting['custom_exercises']['sets'] as $keyTwo => $sets) {
                                       foreach ($sets['workout_set_exercises'] as $keyThree => $exerciseData) {
                                              array_push($setExercises,[
                                                'day' => $day,
                                                'date' => $nextDate,
                                                'from_time' => null,
                                                'to_time' => null,
                                                'exercise' => (!empty($exerciseData['exercise']) && ! empty($exerciseData['exercise']['name'])) ? $exerciseData['exercise']['name'] : '-',
                                                'fitness_profile_exercise_id' => !empty($setting['fitness_profile_exercise_id']) ? $setting['fitness_profile_exercise_id'] : 0,
                                                'workout_exercise_id' => !empty($setting['workout_exercise_id']) ? $setting['workout_exercise_id'] : 0,
                                                'workout_set_id' => !empty($exerciseData['workout_set_id']) ? $exerciseData['workout_set_id'] : null,
                                                'set_no' => $sets['set_no'],
                                                'type' => $setting['type'],
                                                'duration' => (!empty($exerciseData['exercise']) && ! empty($exerciseData['exercise']['duration'])) ? $exerciseData['exercise']['duration'] : 0,
                                                'user_id' => $user->id,
                                                'created_by' => $user->id,
                                                'updated_by' => $user->id,
                                                'created_at' => $currentDateTime,
                                                'updated_at' => $currentDateTime,
                                            ]);

                                        }
                                    }
                                }
                            } else if($setting['type']=='session'){
                                $exercises[$key]['day'] = $day;
                                $exercises[$key]['date'] = $nextDate;
                                $exercises[$key]['from_time'] = null;
                                $exercises[$key]['to_time'] = null;
                                $exercises[$key]['exercise'] = $setting['value'];
                                $exercises[$key]['fitness_profile_exercise_id'] = null;
                                $exercises[$key]['workout_exercise_id'] = null;
                                $exercises[$key]['workout_set_id'] = null;
                                $exercises[$key]['set_no'] = 0;
                                $exercises[$key]['type'] = $setting['type'];
                                $exercises[$key]['duration'] = ! empty($setting['duration']) ? $setting['duration'] : 0;
                                $exercises[$key]['user_id'] = $user->id;
                                $exercises[$key]['created_by'] = $user->id;
                                $exercises[$key]['updated_by'] = $user->id;
                                $exercises[$key]['created_at'] = $currentDateTime;
                                $exercises[$key]['updated_at'] = $currentDateTime;
                            }

                        }
                        $finalArr = array_merge($exercises,$setExercises);
                        FitnessProfile::insert($finalArr);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Get workoute report
     *
     * @param  array  $where
     * @param  array  $with
     * @return FitnessSetting
     */
    public static function getWorkOutReport($request)
    {
        try {
            $userData =  getUser();
            $userId = !empty($request->athlete_id)?$request->athlete_id:$userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $date = ! empty($request->date) ? $request->date : $currentDate;
            $monthArr = getMonthStartEndDate();
            $workouts = FitnessProfile::where('user_id', $userId)
            ->where('date', '>=', $monthArr['start_date'])
            ->where('date', '<=', $monthArr['end_date'])->get()->toArray();
            $totaCompletedHoursOne = 0;
            $totaDutationOne = 0;
            $today = ['time' => ['hours' => 0, 'minutes' => 0, 'seconds' => 0], 'total_duration' => 0, 'completed_hours' => 0, 'total_workouts' => 0, 'completed_workouts' => 0];
            $thisWeek = ['time' => ['hours' => 0, 'minutes' => 0, 'seconds' => 0], 'total_duration' => 0, 'completed_hours' => 0, 'total_workouts' => 0, 'completed_workouts' => 0];
            $thisMonth = ['time' => ['hours' => 0, 'minutes' => 0, 'seconds' => 0], 'total_duration' => 0, 'completed_hours' => 0, 'total_workouts' => 0, 'completed_workouts' => 0];
            //For seven days
            $weekArr = getWeekStartEndDate();

            $fromDateOne = $weekArr['start_date']; //date('Y-m-d', strtotime('-7 day', strtotime($currentDate)));
            $toDateOne = $weekArr['end_date']; //$currentDate;
            $totalWorkoutsOne = 0;
            $completedWorkoutsOne = 0;
            $totalCompletedMinutesOne = 0;
            $totalCompletedSecondsOne = 0;
            $fitnessSettings = FitnessSetting::where('user_id', $userId)->get()->toArray();
            $daySettings = self::findProfileDaySetting(['user_id'=>$userId]);
            // Start Set week start date from settings

            if (!empty($daySettings)) {
                $startDay =  ! empty($daySettings['week_first_day']) ? $daySettings['week_first_day'] : '';
                $weekArr = getWeekStartEndDate('', $startDay);
            }
            // End Set week start date from settings
            while ($fromDateOne <= $toDateOne) {
                //====Start Condition for past including today  workouts=====
                $weekExercises = array_filter($workouts, function ($item) use ($fromDateOne, $toDateOne) {
                    if ($item['date'] == $fromDateOne && $item['date'] <= $toDateOne) {
                        return true;
                    }

                    return false;
                });
                foreach ($weekExercises as $exercise) {
                    $startTimeOne = ! empty($exercise['from_time']) ? $exercise['from_time'] : getTodayDate('H:i:s');
                    $endTimeOne = ! empty($exercise['to_time']) ? $exercise['to_time'] : getTodayDate('H:i:s');
                    $toTimeOne = strtotime($currentDate.' '.$endTimeOne);
                    $fromTimeOne = strtotime($currentDate.' '.$startTimeOne);
                    $totaCompletedHoursOne += round(abs($toTimeOne - $fromTimeOne) / 60, 2);
                    $totaDutationOne += ! empty($exercise['duration']) ? $exercise['duration'] : 0;
                    $totalWorkoutsOne++;
                    if ($exercise['is_completed'] == 1) {
                        $completedWorkoutsOne++;
                    }

                    if (! empty($exercise['completed_time'])) {
                        $timeArray = explode(':', $exercise['completed_time']);
                        $totalCompletedMinutesOne += ! empty($timeArray[0]) ? (int) $timeArray[0] * 60 : 0;
                        $totalCompletedSecondsOne += ! empty($timeArray[1]) ? (int) $timeArray[1] : 0;
                    }
                }
                //====End Condition for past including today  workouts=====
                //====Start Condition for future workouts=====
                $futureDay = strtoupper(date('l', strtotime($fromDateOne)));
                $futureExercises = array_filter($fitnessSettings, function ($item) use ($futureDay) {
                    if ($item['day'] === $futureDay) {
                        return true;
                    }

                    return false;
                });
                if ($fromDateOne > $currentDate) {
                    $totalWorkoutsOne += count($futureExercises);
                }
                //====End Condition for future workouts=====
                $fromDateOne = date('Y-m-d', strtotime('+1 day', strtotime($fromDateOne)));
            }
            // Total completed time in seconds
            $totalCompletedTimeInSecondsOne = $totalCompletedMinutesOne + $totalCompletedSecondsOne;
            $thisWeek['total_duration'] = (int) ($totaDutationOne > $totaCompletedHoursOne ? $totaDutationOne : $totaCompletedHoursOne);
            $thisWeek['completed_hours'] = (int) $totaCompletedHoursOne;
            $thisWeek['total_workouts'] = (int) $totalWorkoutsOne;
            $thisWeek['completed_workouts'] = (int) $completedWorkoutsOne;
            // Convert total completed time in seconds to hours, minutes, and seconds
            $thisWeek['time']['hours'] = floor($totalCompletedTimeInSecondsOne / 3600);
            $thisWeek['time']['minutes'] = floor(($totalCompletedTimeInSecondsOne % 3600) / 60);
            $thisWeek['time']['seconds'] = $totalCompletedTimeInSecondsOne % 60;
            //Today exercise
            $totaCompletedHoursTwo = 0;
            $totaDutationTwo = 0;
            $totalWorkoutsTwo = 0;
            $completedWorkoutsTwo = 0;
            $totalCompletedMinutesTwo = 0;
            $totalCompletedSecondsTwo = 0;
            $todayExercises = array_filter($workouts, function ($item) use ($currentDate) {
                if ($item['date'] === $currentDate) {
                    return true;
                }

                return false;
            });
            if (count($todayExercises) > 0) {
                foreach ($todayExercises as $exercise) {
                    $startTimeTwo = ! empty($exercise['from_time']) ? $exercise['from_time'] : getTodayDate('H:i:s');
                    $endTimeTwo = ! empty($exercise['to_time']) ? $exercise['to_time'] : getTodayDate('H:i:s');
                    $toTimeTwo = strtotime($currentDate.' '.$endTimeTwo);
                    $fromTimeTwo = strtotime($currentDate.' '.$startTimeTwo);
                    $totaCompletedHoursTwo += round(abs($toTimeTwo - $fromTimeTwo) / 60, 2);
                    $totaDutationTwo += ! empty($exercise['duration']) ? $exercise['duration'] : 0;
                    $totalWorkoutsTwo++;
                    if ($exercise['is_completed'] == 1) {
                        $completedWorkoutsTwo++;
                    }
                    // $totalCompletedMinutesTwo += ! empty($exercise['completed_time']) ? floor((int) $exercise['completed_time']) : 0;
                    if (! empty($exercise['completed_time'])) {
                        $timeArray = explode(':', $exercise['completed_time']);
                        $totalCompletedMinutesTwo += ! empty($timeArray[0]) ? (int) $timeArray[0] * 60 : 0;
                        $totalCompletedSecondsTwo += ! empty($timeArray[1]) ? (int) $timeArray[1] : 0;
                    }
                }
                // Total completed time in seconds
                $totalCompletedTimeInSecondsTwo = $totalCompletedMinutesTwo + $totalCompletedSecondsTwo;
                $today['total_duration'] = (int) ($totaDutationTwo > $totaCompletedHoursTwo ? $totaDutationTwo : $totaCompletedHoursTwo);
                $today['completed_hours'] = (int) $totaCompletedHoursTwo;
                $today['total_workouts'] = (int) $totalWorkoutsTwo;
                $today['completed_workouts'] = (int) $completedWorkoutsTwo;
                // Convert total completed time in seconds to hours, minutes, and seconds
                $today['time']['hours'] = floor($totalCompletedTimeInSecondsTwo / 3600);
                $today['time']['minutes'] = floor(($totalCompletedTimeInSecondsTwo % 3600) / 60);
                $today['time']['seconds'] = $totalCompletedTimeInSecondsTwo % 60;
            }

            //For this month
            $fromDateThree = $monthArr['start_date']; //date('Y-m-d', strtotime('-30 day', strtotime($currentDate)));
            $toDateThree = $monthArr['end_date']; //$currentDate;
            $totaCompletedHoursThree = 0;
            $totaDutationThree = 0;
            $totalWorkoutsThree = 0;
            $completedWorkoutsThree = 0;
            $totalCompletedMinutesThree = 0;
            $totalCompletedSecondsThree = 0;

            while ($fromDateThree <= $toDateThree) {
                //====Start Condition for past including today  workouts=====
                $monthExercises = array_filter($workouts, function ($item) use ($fromDateThree) {
                    if ($item['date'] === $fromDateThree) {
                        return true;
                    }

                    return false;
                });
                foreach ($monthExercises as $exercise) {
                    $startTimeThree = ! empty($exercise['from_time']) ? $exercise['from_time'] : getTodayDate('H:i:s');
                    $endTimeThree = ! empty($exercise['to_time']) ? $exercise['to_time'] : getTodayDate('H:i:s');
                    $toTimeThree = strtotime($currentDate.' '.$endTimeThree);
                    $fromTimeThree = strtotime($currentDate.' '.$startTimeThree);
                    $totaCompletedHoursThree += round(abs($toTimeThree - $fromTimeThree) / 60, 2);
                    $totaDutationThree += ! empty($exercise['duration']) ? $exercise['duration'] : 0;
                    $totalWorkoutsThree++;
                    if ($exercise['is_completed'] == 1) {
                        $completedWorkoutsThree++;
                    }
                    // $totalCompletedMinutesThree += ! empty($exercise['completed_time']) ? floor((int) $exercise['completed_time']) : 0;
                    // if(!empty($exercise['completed_time'])){
                    //     $timeArray = explode(':', $exercise['completed_time']);
                    //  }
                    //  $totalCompletedSecondsThree += !empty($timeArray) ? $timeArray[1] : 0;
                    if (! empty($exercise['completed_time'])) {
                        $timeArray = explode(':', $exercise['completed_time']);
                        $totalCompletedMinutesThree += ! empty($timeArray[0]) ? (int) $timeArray[0] * 60 : 0;
                        $totalCompletedSecondsThree += ! empty($timeArray[1]) ? (int) $timeArray[1] : 0;
                    }
                }
                //====End Condition for past including today  workouts=====
                //====Start Condition for future workouts=====
                $futureDay = strtoupper(date('l', strtotime($fromDateThree)));
                $futureExercisesThree = array_filter($fitnessSettings, function ($item) use ($futureDay) {
                    if ($item['day'] === $futureDay) {
                        return true;
                    }

                    return false;
                });
                if ($fromDateThree > $currentDate) {
                    $totalWorkoutsThree += count($futureExercisesThree);
                }
                //====End Condition for future workouts=====
                $fromDateThree = date('Y-m-d', strtotime('+1 day', strtotime($fromDateThree)));
            }
            $totalCompletedTimeInSecondsThree = $totalCompletedMinutesThree + $totalCompletedSecondsThree;
            $thisMonth['total_duration'] = (int) ($totaDutationThree > $totaCompletedHoursThree ? $totaDutationThree : $totaCompletedHoursThree);
            $thisMonth['completed_hours'] = (int) $totaCompletedHoursThree;
            $thisMonth['total_workouts'] = (int) $totalWorkoutsThree;
            $thisMonth['completed_workouts'] = (int) $completedWorkoutsThree;
            // Convert total completed time in seconds to hours, minutes, and seconds
            $thisMonth['time']['hours'] = floor($totalCompletedTimeInSecondsThree / 3600);
            $thisMonth['time']['minutes'] = floor(($totalCompletedTimeInSecondsThree % 3600) / 60);
            $thisMonth['time']['seconds'] = $totalCompletedTimeInSecondsThree % 60;

            return ['data' => $workouts, 'today' => $today, 'thisWeek' => $thisWeek, 'thisMonth' => $thisMonth];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadWorkoutLog($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $paginationLimit = ! empty($request->limit) ? $request->limit : $paginationLimit;
            $list = FitnessProfile::select(
                'date',
                DB::raw('COUNT(*) as total_exercise'),
                DB::raw('SUM(completed_time) as exercise_total_time'),
                DB::raw('COUNT(IF(is_completed = 1, 1, NULL)) as completed_workouts')
            )->where('status', '!=', 'deleted');

            if (! empty($request->userId)) {
                $list->where('user_id', $request->userId);
            }

            if (! empty($request->userId)) {
                $list->where('user_id', $request->userId);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->groupBy('date')->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function workoutReminderNotificationCron()
    {
        try {
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $currentTime = getLocalDateTime('', 'H:i');

            // Fetch notification settings in a single query
            $moduleData = getModuleBykey(['key' => 'fitness-profile']);
            $userNotificationSettings = NotificationRepository::findAllNotificationSetting([
                ['status', 'active'],
                ['module_id', $moduleData->id]
            ]);
            if ($userNotificationSettings->isEmpty()) {
                return false;
            }
            foreach ($userNotificationSettings as $userNotification) {
                // dd($userNotification);
                $notificationType = $userNotification['notificationType']['slug'];
                $reminderTime = Carbon::parse($userNotification['reminder_time'] ? $userNotification['reminder_time'] : '00:00');
                $users = $userNotification['users'];
                // Cron run according users timezone
                if(!empty($users) && !empty($users->timezone)){
                    $currentDate = getLocalDateTime('', 'Y-m-d', $users->timezone);
                    $currentTime = getLocalDateTime('', 'H:i', $users->timezone);
                }
                // Fetch workouts today exist or not in a single query
                $todayWorkouts = FitnessProfile::where('date', $currentDate)
                    ->where('exercise', '!=', 'DAY_OFF')
                    //->whereNotIn('day', ['REPEAT', 'WEEK_FIRST_DAY'])
                    ->where('status', '!=', 'deleted')
                    ->where('user_id', $users->id)
                    ->first();
                if(!empty($todayWorkouts)){
                    if ($currentTime !== $reminderTime->format('H:i')) {
                        continue;
                    }
                    $message = "You have workouts scheduled for today. Stay consistent and keep up the great work!";
                    NotificationRepository::sendReminderNotification($users, $message, $notificationType);
                }
            }

            return true;
        } catch (\Exception $ex) {
            \Log::error("Workout Reminder Cron Error: " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function getUserExercises()
    {
        try {
            $userData = getUser();
            $exerciseData = [];
            $exercises = FitnessProfileExercise::where('status', 'active')->get()->toArray();
            if(!empty($exercises) && count($exercises) > 0){
                foreach ($exercises as $key => $exercise) {
                    if (! empty($exerciseData[$exercise['day']])) {
                        // print_r($exerciseData);
                        // exit;
                        array_push($exerciseData[$exercise['day']], $exercise);
                    } else {
                        $exerciseData[$exercise['day']] = [$exercise];
                    }
                }
            }

            // echo '<pre>';
            // print_r($exerciseData);
            // exit;

            return $exerciseData;
        } catch (\Exception $ex) {
            return [];
        }
    }
    public static function getWorkoutDates($userId)
    {
        try {
            $user_id = $userId;
            $currentDate = getLocalDateTime('', 'Y-m-d');

            // Define range for last and next workout search
            $fromLastDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate))); // Yesterday
            $toLastDate = date('Y-m-d', strtotime('-7 day', strtotime($fromLastDate))); // Up to last 7 days

            $fromNextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate))); // Tomorrow
            $toNextDate = date('Y-m-d', strtotime('+7 day', strtotime($fromNextDate))); // Up to next 7 days

            // Get user's workout settings
            $settings = FitnessSetting::where('user_id', $user_id)
                ->whereNotIn('day', ['DAY_OFF'])//['DAY_OFF', 'REPEAT', 'WEEK_FIRST_DAY']
                ->where('status', '!=', 'deleted')
                ->groupBy('day')->get()->toArray();

            $lastWorkoutDate = null;
            $nextWorkoutDate = null;

            // Find last workout date
            while ($fromLastDate >= $toLastDate) {
                $day = strtoupper(formatDate($fromLastDate, 'l'));
                $availableDay = array_filter($settings, fn($item) => $item['day'] === $day);

                if (!empty($availableDay)) {
                    $lastWorkoutDate = $fromLastDate;
                    break;
                }
                $fromLastDate = date('Y-m-d', strtotime('-1 day', strtotime($fromLastDate)));
            }

            // Find next workout date
            while ($fromNextDate <= $toNextDate) {
                $day = strtoupper(formatDate($fromNextDate, 'l'));
                $availableDay = array_filter($settings, fn($item) => $item['day'] === $day);

                if (!empty($availableDay)) {
                    $nextWorkoutDate = $fromNextDate;
                    break;
                }
                $fromNextDate = date('Y-m-d', strtotime('+1 day', strtotime($fromNextDate)));
            }

            return [
                'last_workout_date' => $lastWorkoutDate,
                'next_workout_date' => $nextWorkoutDate,
            ];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function loadFitnessCalendarData($request)
    {
        try {
            $userData =  getUser();
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $date = !empty($request->date) ? $request->date : $currentDate;
            $monthDateArr = getAllDatesOfMonth($date);
            $fitnessData = [];
            $userId = $userData->id;//544;
            $fitnessProfileSettings = FitnessSetting::where('user_id', $userId)->where('status', 'active')->get()->toArray();
            foreach ($monthDateArr as $key => $dateStr) {
                $dayOfWeek = strtoupper(Carbon::parse($dateStr)->format('l'));
                $filteredFitness = array_filter($fitnessProfileSettings, function ($item) use ($dayOfWeek) {
                    if ($item['day'] == $dayOfWeek) {
                        return true;
                    }

                    return false;
                });
                $filteredFitness = array_values($filteredFitness);
                $fitnessDateArr[$dateStr] = $filteredFitness;
            }
            return [
                'monthDateArr' => $monthDateArr,
                'data'=> $fitnessDateArr
            ];
        }catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function deleteFitnessExercise($request)
    {
        try {
            $model = FitnessSetting::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->delete();
                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
