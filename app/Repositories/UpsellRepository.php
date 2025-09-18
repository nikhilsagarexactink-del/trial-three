<?php

namespace App\Repositories;

use App\Models\Upsell;
use App\Models\UpsellPlan;
use App\Models\UserSubscription;
use App\Models\UserUpsellLog;
use Config;
use Exception;
use DB;
use Carbon\Carbon;

class UpsellRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return Category
     */
    public static function findOne($where, $with = [])
    {
        return Upsell::with($with)->where($where)->first();
    }

     /**
      * Find all
      *
      * @param  array  $where
      * @param  array  $with
      * @return Upsell
      */
     public static function findAll($where, $with = [])
     {
         return Upsell::with($with)->where($where)->get();
     }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadUpsellList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = $post['sort_by'] ?? 'created_at';
            $sortOrder = $post['sort_order'] ?? 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');

             // Handle expired upsells
             $expiredList = Upsell::where('status', 'published')
             ->whereDate('end_date', '<', Carbon::now()->toDateString())
             ->get();

            if ($expiredList->isNotEmpty()) {
                foreach ($expiredList as $expiredUpsell) {
                    $expiredUpsell->status = 'draft';
                    $expiredUpsell->save();

                    UserUpsellLog::where('user_id', $userData->id)
                        ->where('upsell_id', $expiredUpsell->id)
                        ->delete();
                }
            }

            

            // Non-admin logic
            $previousUpsellLogs = UserUpsellLog::where('user_id', $userData->id)
                ->where('status', 'active');

            $list = Upsell::with('plans.plan')
                ->where('status', 'published');

            $user_plan = UserSubscription::where('user_id', $userData->id)
                ->where('is_free_plan', 1)
                ->where('status', '!=', 'deleted')
                ->first();


            if (!empty($post['location']) && $user_plan) {
                $list->where('location', $post['location'])
                    ->whereDate('end_date', '>=', Carbon::now()->toDateString());

                $upsells = $list->get();
                foreach ($upsells as $upsell) {
                    $existingLog = UserUpsellLog::where('user_id', $userData->id)
                        ->where('upsell_id', $upsell->id)
                        ->first();

                    if (!$existingLog) {
                        UserUpsellLog::create([
                            'user_id' => $userData->id,
                            'upsell_id' => $upsell->id,
                            'frequency_type' => $upsell->frequency,
                        ]);
                    } elseif ($existingLog->frequency_type != $upsell->frequency) {
                        $existingLog->frequency_type = $upsell->frequency;
                        $existingLog->save();
                    } else {
                        $existingLog->delete();
                    }
                }
            }
            if ($userData->user_type == 'admin') {
                $list = Upsell::where('status', '!=', 'deleted');

                if (!empty($post['search'])) {
                    $list->where('title', 'like', '%' . $post['search'] . '%');
                }
                if (!empty($post['status'])) {
                    $list->where('status', $post['status']);
                }

                return $list->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);
            } 

            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function displayUserUpsell($request)
    {
        try {
            // Get the current date in Y-m-d format
            $currentDate = Carbon::now()->toDateString();
            $post = $request->all();
            
            // Assume getUser() returns the currently authenticated user
            $userData = getUser();

            $upsell = Upsell::with('plans')
                ->where('status', 'published')
                ->where('location', $post['location'])
                ->whereDate('start_date', '<=', $currentDate) // Start date should be today or earlier
                ->whereDate('end_date', '>=', $currentDate)
                ->first();

                
            // If no upsell is found, return an empty array
            if (!$upsell) {
                return [];
            }


            // Check if the user is subscribed to any of the upsell's associated plans.
            // We use whereIn to check against all plan_ids from the upsell's plans.
            $userSubscriptionExists = UserSubscription::where('user_id', $userData->id)
                ->whereIn('plan_id', $upsell->plans->pluck('plan_id'))
                // ->where('stripe_status', 'complete')
                ->exists();

            // If the user is not subscribed to any relevant plan, return an empty array
            if (!$userSubscriptionExists) {
                return [];
            }

            // Retrieve the existing upsell log for the user, if it exists.
            $existingLog = UserUpsellLog::where('user_id', $userData->id)
                ->where('upsell_id', $upsell->id)
                ->first();

            // By default, assume that the upsell can be shown
            $canShowUpsell = true;

            // If a log exists, check based on the frequency setting whether the upsell can be shown again.
            if ($existingLog) {
                $lastShown = Carbon::parse($existingLog->updated_at);

                switch ($upsell->frequency) {
                    case 'once_a_day':
                        // Upsell can be shown if at least 1 day has passed since it was last shown.
                        $canShowUpsell = $lastShown->copy()->addDay()->lte(Carbon::now());
                        break;

                        case 'once_per_login':
                            // For once per login, we compare the last shown time with the user's last login time.
                            // The upsell should be shown if the upsell was last shown before the current login.
                            // (i.e. last_shown < last_login_date)
                            if (isset($userData->last_login_date)) {
                                $canShowUpsell = $lastShown->lt(Carbon::parse($userData->last_login_date));
                            } else {
                                // If no last_login_date is available, default to showing the upsell.
                                $canShowUpsell = true;
                            }
                            break;

                    case 'once_per_week':
                        // Upsell can be shown if at least 1 week has passed.
                        $canShowUpsell = $lastShown->copy()->addWeek()->lte(Carbon::now());
                        break;

                    case 'once_per_month':
                        // Upsell can be shown if at least 1 month has passed.
                        $canShowUpsell = $lastShown->copy()->addMonth()->lte(Carbon::now());
                        break;

                    case 'always':
                        // Always show the upsell regardless of previous appearances.
                        $canShowUpsell = true;
                        break;

                    default:
                        $canShowUpsell = true;
                        break;
                }
            }
            
            // If the upsell is eligible to be shown based on the frequency check,
            // update the log (if it exists) or create a new log record.
            if ($canShowUpsell) {
                if ($existingLog) {
                    // Update the updated_at field to the current time
                    $existingLog->update(['updated_at' => Carbon::now()]);
                } else {
                    UserUpsellLog::create([
                        'user_id'        => $userData->id,
                        'upsell_id'      => $upsell->id,
                        'is_appear'      => 1, // Mark upsell as shown
                        'frequency_type' => $upsell->frequency,
                    ]);
                }
                
                return $upsell;
            }
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public static function displayUserAppUpsell($request)
    {
        try {
            // Get the current date in Y-m-d format
            $currentDate = Carbon::now()->toDateString();
            $post = $request->all();
            
            // Assume getUser() returns the currently authenticated user
            $userData = getUser();

            $upsells = Upsell::with('plans')
                ->where('status', 'published')
                ->whereDate('start_date', '<=', $currentDate) // Start date should be today or earlier
                ->whereDate('end_date', '>=', $currentDate)
                ->get();

            // $upsell = Upsell::with('plans')
            //     ->where('status', 'published')
            //     ->where('location', $post['location'])
            //     ->whereDate('start_date', '<=', $currentDate) // Start date should be today or earlier
            //     ->whereDate('end_date', '>=', $currentDate)
            //     ->first();

                
            // If no upsell is found, return an empty array
            if (empty($upsells)) {
                return [];
            }
            $visibleUpsells = [];


            // Check if the user is subscribed to any of the upsell's associated plans.
            // We use whereIn to check against all plan_ids from the upsell's plans.
            

            if(!empty($upsells) && $upsells->count() > 0){

                foreach($upsells as $upsell){
                    $userSubscriptionExists = UserSubscription::where('user_id', $userData->id)
                    ->whereIn('plan_id', $upsell->plans->pluck('plan_id'))
                    // ->where('stripe_status', 'complete')
                    ->exists();
    
                // If the user is not subscribed to any relevant plan, return an empty array
                    if (!$userSubscriptionExists) {
                        return [];
                    }

                    $existingLog = UserUpsellLog::where('user_id', $userData->id)
                    ->where('upsell_id', $upsell->id)
                    ->first();

                // By default, assume that the upsell can be shown
                    $canShowUpsell = true;

                    // If a log exists, check based on the frequency setting whether the upsell can be shown again.
                    if ($existingLog) {
                        $lastShown = Carbon::parse($existingLog->updated_at);

                        switch ($upsell->frequency) {
                            case 'once_a_day':
                                // Upsell can be shown if at least 1 day has passed since it was last shown.
                                $canShowUpsell = $lastShown->copy()->addDay()->lte(Carbon::now());
                                break;

                                case 'once_per_login':
                                    // For once per login, we compare the last shown time with the user's last login time.
                                    // The upsell should be shown if the upsell was last shown before the current login.
                                    // (i.e. last_shown < last_login_date)
                                    if (isset($userData->last_login_date)) {
                                        $canShowUpsell = $lastShown->lt(Carbon::parse($userData->last_login_date));
                                    } else {
                                        // If no last_login_date is available, default to showing the upsell.
                                        $canShowUpsell = true;
                                    }
                                    break;

                                    case 'once_per_week':
                                        // Upsell can be shown if at least 1 week has passed.
                                        $canShowUpsell = $lastShown->copy()->addWeek()->lte(Carbon::now());
                                        break;

                                    case 'once_per_month':
                                        // Upsell can be shown if at least 1 month has passed.
                                        $canShowUpsell = $lastShown->copy()->addMonth()->lte(Carbon::now());
                                        break;

                                    case 'always':
                                        // Always show the upsell regardless of previous appearances.
                                        $canShowUpsell = true;
                                        break;

                                    default:
                                        $canShowUpsell = true;
                                        break;
                                }
                            }
                            
                            // If the upsell is eligible to be shown based on the frequency check,
                            // update the log (if it exists) or create a new log record.
                            if ($canShowUpsell) {
                                if ($existingLog) {
                                    // Update the updated_at field to the current time
                                    $existingLog->update(['updated_at' => Carbon::now()]);
                                } else {
                                    UserUpsellLog::create([
                                        'user_id'        => $userData->id,
                                        'upsell_id'      => $upsell->id,
                                        'is_appear'      => 1, // Mark upsell as shown
                                        'frequency_type' => $upsell->frequency,
                                    ]);
                                }
                                
                                $visibleUpsells[] = $upsell;
                            }
                        }


                    }
                            // Retrieve the existing upsell log for the user, if it exists.
           
            return  $visibleUpsells;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }





    /**
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveUpsell($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $existingLocationUpsells = self::findAll([
                ['location', $request->location], 
                ['status', '=', 'published']
            ]);
            if($existingLocationUpsells->isNotEmpty() && $existingLocationUpsells->count() > 0){
                foreach ($existingLocationUpsells as $upsell) {
                    $upsell->status = 'draft';
                    $upsell->save();
                }
            }
           
                $model = new Upsell();
                $model->title = $post['title'];
                $model->message = $post['message'];
                $model->start_date = $post['start_date'];
                $model->end_date = $post['end_date'];
                $model->frequency = $post['frequency'];
                $model->location = $post['location'];
                $model->created_by = $userData->id;
                $model->updated_by = $userData->id;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->save();
            

            $upsellData = [];
            foreach ($post['plans'] as $key => $value) {
                $upsellData[$key]['upsell_id'] = $model->id;
                $upsellData[$key]['plan_id'] = $value;
                $upsellData[$key]['created_by'] = $userData->id;
                $upsellData[$key]['updated_by'] = $userData->id;
                $upsellData[$key]['created_at'] = $currentDateTime;
                $upsellData[$key]['updated_at'] = $currentDateTime;
            }
            UpsellPlan::insert($upsellData);
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateUpsell($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser ();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
    
            if (empty($model)) {
                throw new Exception('Upsell not found.', 404);
            }
    
            $existingLocationUpsells = self::findAll([
                ['location', $request->location], 
                ['status', '=', 'published']
            ]);
    
            if ($existingLocationUpsells->isNotEmpty()) {
                foreach ($existingLocationUpsells as $upsell) {
                    if ($upsell->id != $request->id) {
                        $upsell->status = 'draft';
                        $upsell->save();
                    }    
                }
            }
    
            if ($post['end_date'] >= getTodayDate('Y-m-d')) {
                $model->title = $post['title'];
                $model->message = $post['message'];
                $model->start_date = $post['start_date'];
                $model->end_date = $post['end_date'];
                $model->frequency = $post['frequency'];
                $model->location = $post['location'];
                $model->created_by = $userData->id;
                $model->updated_by = $userData->id;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->save();
            }
    
            // Handle plans
            $upsellData = [];
            $existingPlans = UpsellPlan::where('upsell_id', $model->id)->pluck('plan_id')->toArray();
            $plansToRemove = array_diff($existingPlans, $post['plans']);
    
            // Delete the removed plans
            if (!empty($plansToRemove)) {
                UpsellPlan::where('upsell_id', $model->id)
                    ->whereIn('plan_id', $plansToRemove)
                    ->delete();
            }
    
            // Filter out the plans that are already associated with the upsell
            $newPlans = array_diff($post['plans'], $existingPlans);
    
            if (!empty($newPlans)) {
                foreach ($newPlans as $key => $value) {
                    $upsellData[$key]['upsell_id'] = $model->id;
                    $upsellData[$key]['plan_id'] = $value;
                    $upsellData[$key]['created_by'] = $userData->id;
                    $upsellData[$key]['updated_by'] = $userData->id;
                    $upsellData[$key]['created_at'] = $currentDateTime;
                    $upsellData[$key]['updated_at'] = $currentDateTime;
                }
                UpsellPlan::insert($upsellData);
            }
    
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
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

     public static function changeUpsellStatus($request)
     {
         try {
             $model = self::findOne(['id' => $request->id]);
     
             if (empty($model)) {
                 throw new Exception('Upsell not found.', 404);
             }
     
             $existingLocationUpsells = self::findAll([
                 ['location', $request->location], 
                 ['status', '=', 'published']
             ]);
     
             if ($request->status != 'published') {
                 UserUpsellLog::where('upsell_id', $model->id)->delete();
             }
     
             if ($existingLocationUpsells->isNotEmpty() && $request->status == 'published') {
                 foreach ($existingLocationUpsells as $upsell) {
                     if ($upsell->id != $request->id) {
                         $upsell->status = 'draft';
                         $upsell->save();
                     }    
                 }
             }
     
             $model->status = $request->status;
             $model->save();
     
             return true;
         } catch (\Exception $ex) {
             throw $ex;
         }
     }
    /* 
    * Extract upsell logs active  according to user 
    * send Boolen value for showing upsell according to frequnecy
    */
    
    public static function showUpsellMessage($request)
{
    try {
        $userData = getUser () ? getUser () : $request->userData;
        $upsellData = Upsell::where('location', $request->location)->where('status', '=', 'published')->first();

        $is_show = false;

        if (!empty($upsellData)) {
           
       

        $userUpsellLog = UserUpsellLog::where('user_id', $userData->id)->where('upsell_id', $upsellData->id)->first();

        if (!empty($userUpsellLog)) {
            $createdAt = Carbon::parse($userUpsellLog->created_at);
            $updatedAt = Carbon::parse($userUpsellLog->updated_at);
            $endDate = Carbon::parse($upsellData->end_date);
            $currentDate = Carbon::now();
            $is_login = session()->get('is_login', false);

            $upsellFrequency = $userUpsellLog->frequency_type;

            if ($upsellFrequency == 'once_a_day') {
                if ($updatedAt > $createdAt && $userUpsellLog->is_appear == 0) {
                    if ($updatedAt->diffInDays($currentDate) == 0) {
                        $is_show = false;
                    } else {
                        $is_show = true;
                        $userUpsellLog->is_appear = 1;
                        $userUpsellLog->updated_at = Carbon::now();
                        $userUpsellLog->save();
                    }
                } else {
                    $is_show = true;
                }
            } elseif ($upsellFrequency == 'once_per_week') {
                if ($updatedAt > $createdAt && $userUpsellLog->is_appear == 0) {
                    if ($updatedAt->diffInWeeks($currentDate) == 0) {
                        $is_show = false;
                    } else {
                        $is_show = true;
                        $userUpsellLog->is_appear = 1;
                        $userUpsellLog->updated_at = Carbon::now();
                        $userUpsellLog->save();
                    }
                } else {
                    $is_show = true;
                }
            } elseif ($upsellFrequency == 'once_per_month') {
                if ($updatedAt > $createdAt && $userUpsellLog->is_appear == 0) {
                    if ($updatedAt->diffInMonths($currentDate) == 0) {
                        $is_show = false;
                    } else {
                        $is_show = true;
                        $userUpsellLog->is_appear = 1;
                        $userUpsellLog->updated_at = Carbon::now();
                        $userUpsellLog->save();
                    }
                } else {
                    $is_show = true;
                }
            } elseif ($upsellFrequency == 'once_per_login') {
                $is_show = $is_login;
            } else {
                $is_show = true; // Show upsell for unknown frequency
            }
        } else {
            $is_show = true; // Show upsell if there's no log
        }
    }

        return $is_show;
    } catch (\Exception $ex) {
        throw $ex;
    }
}
    public static function removeUserUpsell($request)
    {
        try {
            $userData = getUser ();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $userUpsellLog = UserUpsellLog::where('upsell_id', $request->upsell_id)
                ->where('user_id', $userData->id)
                ->first();
            if ($userUpsellLog) {
                $userUpsellLog->is_appear = 1;
                $userUpsellLog->updated_at = $currentDateTime;
                $userUpsellLog->save();
            } else {
                throw new Exception('User upsell log not found.', 404);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
?>