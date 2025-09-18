<?php

namespace App\Repositories;

use App\Models\FitnessProfile;
use App\Models\FitnessSetting;
use App\Models\WorkoutSet;
use App\Models\FitnessChallenge;
use App\Models\FitnessChallengeLog;
use App\Models\FitnessChallengeSignup;
use App\Models\FitnessChallengeUserRole;
use App\Models\FitnessChallengePlan;
use App\Models\UserSubscription;
use App\Services\VimeoService;
use App\Jobs\UserCalendarReminderJob;
use App\Repositories\UserRepository;
use App\Models\UserRole;
use Carbon\Carbon;
use Config;
use DB;
use Exception;
use DateTime;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;

class FitnessChallengeRepository
{

    /**
     * Find all Users
     *
     * @param  array  $where
     * @param  array  $with
     * @return  FitnessChallengeSignup
     */
    public static function findChallengeUsers($where, $with = [])
    {
        return FitnessChallengeSignup::with($with)->where($where)->get();
    }

    public static function findSingleChallengeUser($where, $with = [])
    {
        return FitnessChallengeSignup::with($with)->where($where)->first();
    }
    /**
     * Find one Workout
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WorkoutExercise
     */
    public static function findOneChallenge($where, $with=[])
    {
        return FitnessChallenge::with($with)->where($where)->first();
    }

    /**
     * Find all Workout
     *
     * @param  array  $where
     * @param  array  $with
     * @return  FitnessChallenge
     */
    public static function findAllChallenges($where, $with = [])
    {
        return FitnessChallenge::with($with)->where($where)->get();
    }
   
    /**
     * Change record status by Id for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeChallengeStatus($request)
    {
        try {
            $model = FitnessChallenge::where(['id' => $request->id])->first();
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
     * Change record status by Id for Difficulty
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeChallengeUserStatus($request)
    {
        try {
            $model = FitnessChallengeSignup::where(['id' => $request->id,'challenge_id'=>$request->challenge_id])->first();
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
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = getTodayDate('Y-m-d');
            $liveDate = isset($post['live_date']) ? $post['live_date'] : $currentDate;
            $endDate = addSubDate($liveDate, $post['days'], 'Y-m-d', 'add');

            $model = new FitnessChallenge();
            $model->title = $post['name'];
            $model->teaser_description = $post['teaser_description'];
            $model->description = $post['description'];
            $model->workout_id = $post['workout_id'] ?? null;
            $model->number_of_days =  $post['days'];
            $model->type = $post['type'];
            $model->leaderboard = $post['leaderboard'] ?? 0;
            $model->go_live_date = $post['live_date'] ?? $currentDate;
            $model->end_date = $endDate;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->save();
            if(!empty($post['user_role_ids']) && count($post['user_role_ids']) > 0){
                $user_roles = [];
                foreach($post['user_role_ids'] as $userRole){
                    $user_roles[] = [
                        'challenge_id' => $model->id,
                        'user_role_id' => $userRole,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                    ];
                }
                FitnessChallengeUserRole::insert($user_roles);
            }
            // Only for athlete user type..
            if(!empty($post['user_plan_ids']) && count($post['user_plan_ids']) > 0){
                $user_plans = [];
                foreach($post['user_plan_ids'] as $planId){
                    $user_plans[] = [
                        'challenge_id' => $model->id,
                        'plan_id' => $planId,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                    ];
                }
                FitnessChallengePlan::insert($user_plans);
            }
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            Db::rollback();
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
 
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = getTodayDate('Y-m-d');
            $liveDate = isset($post['live_date']) ? $post['live_date'] : $currentDate;
            $endDate = addSubDate($liveDate, $post['days'], 'Y-m-d', 'add');

            $model = self::findOneChallenge(['id' => $request->id]);
            if(empty($model)){
                throw new Exception('Challenge not found.', 1);
            }
            $model->title = $post['name'];
            $model->teaser_description = $post['teaser_description'];
            $model->description = $post['description'];
            $model->workout_id = ($post['type'] === 'workouts' && isset($post['workout_id'])) ? $post['workout_id'] : null;
            $model->number_of_days = $post['days'];
            $model->type = $post['type'];
            $model->leaderboard = $post['leaderboard'] ?? 0;
            $model->go_live_date =$liveDate;
            $model->end_date =$endDate;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->save();
            
            FitnessChallengeUserRole::where('challenge_id',$model->id)->delete();
            if(!empty($post['user_role_ids']) && count($post['user_role_ids']) > 0){
                $user_roles = [];
                foreach($post['user_role_ids'] as $userRole){
                    $user_roles[] = [
                        'challenge_id' => $model->id,
                        'user_role_id' => $userRole,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                    ];
                }
                FitnessChallengeUserRole::insert($user_roles);
            }
            // Only for athlete user type..
            FitnessChallengePlan::where('challenge_id',$model->id)->delete();
            if(!empty($post['user_plan_ids']) && count($post['user_plan_ids']) > 0){
                $user_plans = [];
                foreach($post['user_plan_ids'] as $planId){
                    $user_plans[] = [
                        'challenge_id' => $model->id,
                        'plan_id' => $planId,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                    ];
                }
                FitnessChallengePlan::insert($user_plans);
            }
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }

    /**
     * Delete Record for Fitness Challenge
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */

     public static function delete($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $model = self::findOneChallenge(['id' => $request->id]);
            $model->delete();
            FitnessChallengeUserRole::where('challenge_id',$model->id)->delete();
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            Db::rollback();
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
    public static function loadChallengeList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $athlete_id = $request->athlete_id;

            if (!empty($athlete_id)) {
                $athlete = UserRepository::findOne(['id' => $athlete_id]);
                // Optional: use $athlete somewhere if needed
            }

            $paginationLimit = !empty($post['perPage']) ? $post['perPage'] : 10;
            $sortBy = 'created_at';
            $sortOrder = 'DESC';

            $list = FitnessChallenge::with(['user_roles.role', 'workout'])
                ->where('status', '!=', 'deleted')
                ->orderBy($sortBy, $sortOrder);

            // Filter by user_role_id
            if (!empty($post['user_role_id'])) {
                $list->whereHas('user_roles', function ($query) use ($post) {
                    $query->where('user_role_id', $post['user_role_id']);
                });
            }

            if (!empty($post['type'])) {
                $list->where('type', $post['type']);
            }

            if (!empty($post['workout_id'])) {
                $list->where('workout_id', $post['workout_id']);
            }

            if (!empty($post['search'])) {
                $list->where('title', 'like', '%' . $post['search'] . '%');
            }

            if (!empty($post['status'])) {
                $list->where('status', $post['status']);
            }

            return $list->paginate($paginationLimit);

        } catch (\Exception $ex) {
            // Optional: Log error for debugging
            // \Log::error('Error loading challenge list: '.$ex->getMessage());
            throw $ex;
        }
    }

   /**
     * Load record list for Challenge Users
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadChallengeUsersList($request)
    {
        try {
            $post = $request->all();
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $sortBy = 'created_at';
            $sortOrder = 'DESC';

            // Fetch challenge users based on the challenge ID
            $list = FitnessChallengeSignup::with('user')->where('challenge_id',$request->id);

            // Filter by user_id if provided
            if (!empty($post['user_id'])) {
                $list = $list->whereHas('user', function ($query) use ($post) {
                    $query->where('id', $post['user_id']); // Assuming 'id' is the primary key for users
                });
            }

            // Optional: Filter by status if provided
            if (!empty($post['status'])) {
                $list = $list->where('status', $post['status']);
            }

            // Apply sorting and pagination
            return $list->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    public static function showLeaderboard($request)
    {
        try {
            $userData = getUser();
            $currentDate = \Carbon\Carbon::parse(getTodayDate('Y-m-d'));
            $athlete_id = $request->athlete_id;
            $sortBy = 'created_at';
            $sortOrder = 'DESC';

            $myRole = UserRole::where('user_type', $userData->user_type)->first();
            $athlete = null;

            if (!empty($athlete_id)) {
                $athlete = UserRepository::findOne(['id' => $athlete_id]);
                $myRole = UserRole::where('user_type', $athlete->user_type)->first();
            }

            $userId = !empty($athlete) ? $athlete->id : $userData->id;

            $challenges = FitnessChallenge::with([
                'user_roles' => function ($query) use ($myRole) {
                    $query->where('user_role_id', $myRole->id);
                },
                'signUps'
            ])->where('leaderboard', 1)->orderBy($sortBy, $sortOrder)->get();

            $allChallenges = [];

            foreach ($challenges as $challenge) {
                $startDate = \Carbon\Carbon::parse($challenge->go_live_date);
                $endDate = \Carbon\Carbon::parse($challenge->end_date);

                $isUserJoined = FitnessChallengeSignup::where('user_id', $userId)
                    ->where('challenge_id', $challenge->id)
                    ->first();

                // ✅ Case 1: User has signed up and challenge is ongoing
                if ($isUserJoined && $currentDate->between($startDate, $endDate)) {
                    $challenge->is_signup = true;
                    $challenge->show_leaderboard = true;
                    $allChallenges[] = $challenge;
                }

                // ✅ Case 2: User has NOT signed up and is in the 7-day signup window
                elseif (
                    !$isUserJoined &&
                    $currentDate->between(
                        $startDate->copy()->subDays(7),
                        $startDate->copy()->subDay()
                    )
                ) {
                    $challenge->is_signup = false;
                    $challenge->show_leaderboard = false;
                    $allChallenges[] = $challenge;
                }
            }
            return $allChallenges;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function loadChallengeWidget($type = null, $is_my_challenge = false) {
        try{
            $userData = getUser();
            $currentDate = getTodayDate('Y-m-d');
            $sevenDays = Carbon::now()->addDays(7);
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $subscription = UserSubscription::where('user_id', $userData->id)->activeSubscription()->first(); // or wherever plan_id is stored
            $result = FitnessChallenge::with('userType')
                ->where('status', '!=', 'deleted')
                ->whereDate('go_live_date', '<=', $sevenDays)  // within next 7 days
                ->whereDate('go_live_date', '>=', $currentDate) // from today onwards
                ->whereHas('userType', function($query) use ($userData) {
                    $query->where('user_type', $userData->user_type);
                })
                ->whereDoesntHave('signUps', function($query) use ($userData) {
                    $query->where('user_id', $userData->id);
                })
                ->where(function ($query) use ($userData, $subscription) {
                    if (!empty($subscription) && $userData->user_type === 'athlete') {
                        $query->whereDoesntHave('plans')
                            ->orWhereHas('plans', function ($q) use ($subscription) {
                                $q->where('plans.id', $subscription->plan_id);
                            });
                    }
                });

                // Show challenge teaser widget relevant module page (e.g., Sleep Tracker) for other types
                if ($type) {
                    $result->where('type', $type);
                }
                if($is_my_challenge) {
                    $result = $result->paginate($paginationLimit);
                }else{
                    $result = $result->get();
                }
            
            return $result;

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function getChallengeLeaderboard()
    {
        $user = getUser();
        $currentDate = getTodayDate('Y-m-d');
        $challenges = self::findAllChallenges([
            ['status', 'active'],
            ['leaderboard', 1],
            ['go_live_date', '>=', $currentDate]
        ]);

        return collect($challenges)->map(function ($challenge) use ($user) {
            // Get participant IDs first to avoid unnecessary queries if no participants
            $participantIds = $challenge->signUps()->pluck('user_id')->toArray();
            // if (empty($participantIds)) {
            //     return null;
            // }

            $dateField = self::getLogTable($challenge->type) === 'user_meals' 
                ? 'meal_date' 
                : 'date';

            // $logsQuery = DB::table(self::getLogTable($challenge->type))
            //     ->join('users', 'users.id', '=', self::getLogTable($challenge->type) . '.user_id')
            //     ->select([
            //         self::getLogTable($challenge->type) . '.user_id',
            //         'users.first_name',
            //         'users.last_name',
            //         DB::raw("COUNT(DISTINCT DATE($dateField)) as completed_days")
            //     ])
            //     ->whereIn(self::getLogTable($challenge->type) . '.user_id', $participantIds)
            //     ->whereBetween($dateField, [$challenge->go_live_date, $challenge->end_date]);

            $logsQuery = DB::table(self::getLogTable($challenge->type))
            ->join('users', 'users.id', '=', self::getLogTable($challenge->type) . '.user_id')
            ->leftJoin('media', 'media.id', '=', 'users.media_id')
            ->select([
                self::getLogTable($challenge->type) . '.user_id',
                'users.first_name',
                'users.last_name',
                'media.id as media_id',
                'media.name as media_name',
                'media.base_url',
                'media.base_path',
                DB::raw("COUNT(DISTINCT DATE($dateField)) as completed_days")
            ])
            ->whereIn(self::getLogTable($challenge->type) . '.user_id', $participantIds)
            ->whereBetween($dateField, [$challenge->go_live_date, $challenge->end_date]);
            //Add extra conditions only for workouts
            if ($challenge->type === 'workouts') {
                $logsQuery->where(self::getLogTable($challenge->type) . '.is_completed', 1)
                ->where(self::getLogTable($challenge->type) . '.workout_exercise_id', $challenge->workout_id);
            }

            $logs = $logsQuery
            ->groupBy([
                self::getLogTable($challenge->type) . '.user_id',
                'users.first_name',
                'users.last_name'
            ])
            ->orderByDesc('completed_days')
            ->get();

            $topUsers = $logs->take(5);
            $isUserInTop = $topUsers->contains('user_id', $user->id);
            $activeUser = (!$isUserInTop) ? $logs->firstWhere('user_id', $user->id) : null;
            return [
                'id' => $challenge->id,
                'title' => $challenge->title,
                'type' => $challenge->type,
                'number_of_days' => $challenge->number_of_days,
                'go_live_date' => $challenge->go_live_date,
                'leaderboard' => [
                    'top_users' => $topUsers,
                    'active_user' => $activeUser,
                    'challenge_days' => $challenge->number_of_days,
                ]
            ];
        })->filter()->values()->toArray();
    }

    protected static function getLogTable($type)
    {
        return match ($type) {
            'workouts' => 'fitness_profiles',
            'water-intake' => 'water_tracker_goal_logs',
            'step-counter' => 'step_counter_goal_logs',
            'sleep-tracker' => 'sleep_tracker_goal_logs',
            'food-tracker' => 'user_meals',
            default => throw new \Exception("Unsupported challenge type"),
        };
    }

    public static function signupChallenge($request)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $post = $request->all();
            
            // Validate required fields
            if (empty($post['challenge_id']) || empty($post['challenge_type'])) {
                throw new InvalidArgumentException('Missing required challenge information');
            }

            // Check existing signup
            $existingSignup = FitnessChallengeSignup::where([
                ['challenge_id', $post['challenge_id']],
                ['user_id', $userData->id]
            ])->exists();

            if ($existingSignup) {
                throw new Exception('You have already signed up for this challenge. Please try another one');
            }

            // Handle challenge type specific logic
            if ($post['challenge_type'] == 'workouts') {
                self::saveChallengeWorkout($post, $userData);
            }

            // Create signup record
            FitnessChallengeSignup::create([
                'challenge_id' => $post['challenge_id'],
                'user_id' => $userData->id,
                'signup_date' => now()->format('Y-m-d'),
                'created_by' => $userData->id,
                'updated_by' => $userData->id
            ]);

            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    protected static function saveChallengeWorkout($post, $userData)
    {
        $currentDateTime = getTodayDate('Y-m-d H:i:s');
        $challenge = self::findOneChallenge(['id' => $post['challenge_id']]);
        if (!$challenge) {
            throw new Exception('Challenge not found');
        }

        if (empty($challenge->workout_id)) {
            throw new Exception('Challenge workout data is missing');
        }

        $duration = $challenge->number_of_days;
        $liveDate = Carbon::parse( $challenge->go_live_date);
        // Validate challenge dates
        if (now()->gt($liveDate)) {
            throw new Exception('Challenge has already started');
        }

        // Prepare workout exercises data - using original query approach
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
            ->where('workout_sets.workout_exercise_id', $challenge->workout->id)
            ->get();

        $fitnessData = [];
        foreach ($workoutSetExercises as $exercise) {
            $fitnessData[] = [
                'type' => 'custom',
                'exercise' => $exercise->name ?? null,
                'workout_exercise_id' => $challenge->workout->id,
                'fitness_profile_exercise_id' => null,
                'workout_set_id' => $exercise->workout_set_id ?? null,
                'set_no' => $exercise->set_no ?? 0,
                'duration' => $exercise->duration ?? 0,
                'user_id' => $userData->id,
                'created_by' => $userData->id,
                'updated_by' => $userData->id,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ];
        }

        // Prepare daily schedule data
        $scheduleData = [];
        for ($i = 0; $i < $duration; $i++) {
            $date = $liveDate->copy()->addDays($i);
            $dayName = strtoupper($date->format('l'));
            
            $scheduleData[] = [
                'day' => $dayName,
                'exercise_type' => 'available-workouts',
                'duration' => 0,
                'type' => 'custom',
                'value' => $challenge->workout->name,
                'workout_exercise_id' => $challenge->workout->id,
                'user_id' => $userData->id,
                'created_by' => $userData->id,
                'updated_by' => $userData->id,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ];

            // Add date to fitness data if needed
            if (!empty($fitnessData)) {
                foreach ($fitnessData as &$item) {
                    $item['day'] = $dayName;
                    $item['date'] = $date->format('Y-m-d');
                }
            }
        }

        if (!empty($scheduleData)) {
            FitnessSetting::insert($scheduleData);
        }

        if (!empty($fitnessData)) {
            FitnessProfile::insert($fitnessData);
        }

        return true;
    }

    public static function userChallenges($request) {
        $user = getUser();
        $challenges = FitnessChallengeSignup::with(['challenge'])->where('user_id', $user->id)->get()->toArray();
        return $challenges;
    }

    /**
     * Retrieve active challenges for the current user.
     *
     * @param \Illuminate\Http\Request $request
     * @return array List of active challenges
     */
    public static function loadUserChallenges($request)
    {
        $post = $request->all();
        $sortBy = 'created_at';
        $sortOrder = 'DESC';
        $userData = getUser(); 
        $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
        $subscription = UserSubscription::where('user_id', $userData->id)->activeSubscription()->first(); // or wherever plan_id is stored
        $challenges = FitnessChallenge::with(['signups' => function($q) use ($userData) {
            $q->where('user_id', $userData->id);
        }])
        ->where('end_date', '>=', now())
        ->where(function ($query) use ($userData, $subscription) {
            if (!empty($subscription) && $userData->user_type === 'athlete') {
                $query->whereDoesntHave('plans')
                ->orWhereHas('plans', function ($q) use ($subscription) {
                    $q->where('plans.id', $subscription->plan_id);
                });
            }
        });
        if($post['search']) {
            $challenges->where('title', 'like', '%' . $post['search'] . '%');
        }
        if($post['type']) {
            $challenges->where('type', $post['type']);
        }
        $challenges = $challenges->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

        $challenges->getCollection()->transform(function ($challenge) use ($userData) {
            $challenge->is_joined = $challenge->signups->isNotEmpty();
            $challenge->status = self::getChallengeStatus($challenge, $userData->id);
            return $challenge;
        });

        return $challenges;
    }

    private static function getChallengeStatus($challenge, $userId)
    {
        $currentDate = getTodayDate('Y-m-d');

        // Check if user is signed up
        $isSignedUp = DB::table('fitness_challenge_signups')
            ->where('challenge_id', $challenge->id)
            ->where('user_id', $userId)
            ->exists();

        // CASE 1: Not signed up and challenge already started
        if ((! $isSignedUp|| $isSignedUp) && $currentDate > $challenge->go_live_date) {
            return 'Expired';
        }

        // CASE 2: Not signed up, challenge upcoming
        if (! $isSignedUp) {
            return 'Not Signed Up';
        }

        // CASE 3: Challenge not started yet
        if ($currentDate < $challenge->go_live_date) {
            return 'Not Started';
        }

        // CASE 4: Challenge ended
        if ($currentDate > $challenge->end_date) {
            return 'Completed';
        }

        // CASE 5: Challenge ongoing → check user log activity
        $logTable = self::getLogTable($challenge->type);
        $column = $logTable === 'user_meals' ? 'meal_date' : 'date';

        $hasLog = DB::table($logTable)
            ->where('user_id', $userId)
            ->whereBetween($column, [$challenge->go_live_date, $currentDate])
            ->exists();
        return $hasLog ? 'In Progress' : 'Active'; // Active = signed up but no logs yet
    }

    public static function loadUserProgress($request)
    {
        $userId = $request['user_id'];
        $userData = getUser();
        $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
        $subscription = UserSubscription::where('user_id', $userId)->activeSubscription()->first(); // or wherever plan_id is stored
        $challenges = FitnessChallenge::with(['signups' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }])
        // ->where('end_date', '>=', now())
        ->where(function ($query) use ($userData, $subscription) {
            
                $query->whereDoesntHave('plans')
                ->orWhereHas('plans', function ($q) use ($subscription) {
                    $q->where('plans.id', $subscription->plan_id);
                });
        });
        $challenges = $challenges->get();

        // $challenges->getCollection()->transform(function ($challenge) use ($userData) {
        //     $challenge->is_joined = $challenge->signups->isNotEmpty();
        //     // $challenge->status = self::getChallengeStatus($challenge, $userData->id);
        //     return $challenge;
        // });

        return $challenges;
    }

    
    
    public static function loadParticipantChallenges($request)
    {
        try {
            $post = $request->all();
            $user_id = $request->route('user_id');

            $challenges = FitnessChallengeSignup::with('challenge')
                ->where('user_id', $user_id)
                ->get()->toArray();

            return $challenges;
        } catch(\Exception $ex){
            throw $ex;
        }
    }

   public static function loadChallengeParticipantProgress($request)
    {
         try {
            $post = $request->all();
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $user_id = $request->route('user_id');
            $challenge_id = $request->route('challenge_id');
            $challenges = FitnessChallengeLog::with('challenge')->where([['user_id' , $user_id ] , [ 'challenge_id' , $challenge_id] ,['status', '!=', 'deleted']]);
            $challenges = $challenges->paginate($paginationLimit);

            return $challenges;
            }catch(\Exception $ex){
            throw $ex;
        }
    }
}