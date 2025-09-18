<?php

namespace App\Repositories;

use App\Jobs\BroadcastEmailJob;
use App\Jobs\BroadcastAlertJob;
use App\Models\RecurringBroadcast;
use App\Models\RecurringBroadcastTokenMaster;
use App\Services\SmsService;
use App\Services\PostmarkService;
use App\Models\User;
use App\Models\UserBroadcastAlert;
use App\Models\RecurringBroadcastLog;
use App\Jobs\RecurringBroadcastJob;
use Config;
use DB;
use Exception;
use Aws\CloudWatch\CloudWatchClient;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use App\Services\StripePayment;
use App\Repositories\FitnessProfileRepository;
use Carbon\Carbon;
use File;


class RecurringBroadcastRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  RecurringBroadcast
     */
    public static function findOne($where, $with = [])
    {
        return RecurringBroadcast::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  RecurringBroadcast
     */
    public static function findAll($where, $with = [])
    {
        return RecurringBroadcast::with($with)->where($where)->get();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  RecurringBroadcast
     */
    public static function findAllTokens($where, $with = [])
    {
        return RecurringBroadcastTokenMaster::with($with)->where($where)->get();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadRecurringBroadcastList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = RecurringBroadcast::where('status', '!=', 'deleted')->orderBy($sortBy, $sortOrder);
            if ($userData->user_type !== 'admin') {
                $list->where('created_by', $userData->id);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
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
     * Add Record
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
            $model = new RecurringBroadcast();
            $model->title = $post['title'];
            $model->message = $post['message'];
            $model->send_type = ! empty($post['send_type']) ? implode(',', $post['send_type']) : null;
            $model->users_status = 'active';
            $model->user_types = ! empty($post['user_types']) ? implode(',', $post['user_types']) : null;
            $model->trigger_event = ! empty($post['trigger_event']) ? $post['trigger_event'] : null;
            if($post['trigger_event'] == 'sign_up' || $post['trigger_event'] == 'last_login'){
                $model->from_day = isset($post['from_day']) ? $post['from_day'] : null;
                $model->to_day = isset($post['to_day']) ? $post['to_day'] : null;
            } elseif($post['trigger_event'] == 'hasnt_logged_in'){
                $model->has_not_logged_in_days = ! empty($post['has_not_logged_in_days']) ? $post['has_not_logged_in_days'] : null;
            }elseif($post['trigger_event'] == 'anniversary'){
                $model->anniversary_months = ! empty($post['anniversary_months']) ? $post['anniversary_months'] : null;
            }
            $model->send_time = ! empty($post['send_time']) ? $post['send_time'] : null;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
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
    public static function update($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDate = getTodayDate('Y-m-d');
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->title = $post['title'];
                $model->message = $post['message'];
                $model->send_type = ! empty($post['send_type']) ? implode(',', $post['send_type']) : null;
                $model->users_status = 'active';
                $model->user_types = ! empty($post['user_types']) ? implode(',', $post['user_types']) : null;
                $model->trigger_event = ! empty($post['trigger_event']) ? $post['trigger_event'] : null;
                if($post['trigger_event'] == 'sign_up' || $post['trigger_event'] == 'last_login'){
                    $model->from_day = isset($post['from_day']) ? $post['from_day'] : null;
                    $model->to_day = isset($post['to_day']) ? $post['to_day'] : null;
                }
                if($post['trigger_event'] == 'hasnt_logged_in'){
                    $model->has_not_logged_in_days = ! empty($post['has_not_logged_in_days']) ? $post['has_not_logged_in_days'] : null;
                }
                if($post['trigger_event'] == 'anniversary'){
                    $model->anniversary_months = ! empty($post['anniversary_months']) ? $post['anniversary_months'] : null;
                }
                $model->send_time = ! empty($post['send_time']) ? $post['send_time'] : null;
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();
                DB::commit();

                return true;
            } else {
                DB::rollback();
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollback();
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
    public static function changeStatus($request)
    {
        try {
            $model = RecurringBroadcast::where(['id' => $request->id])->first();
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

    public static function triggerRecurringBroadcast()
    {
        try {
            $broadcasts = self::findAll(['status' => 'active']);
            $currentDate = getTodayDate('Y-m-d');
            $timezone = env('APP_TIMEZONE', 'UTC');
            $timeFormat = env('TIME_FORMAT', 'H:i');
            $currentTime = Carbon::now($timezone)->format($timeFormat);

            foreach ($broadcasts as $broadcast) {
                // If trigger event is 'sign_up', send immediately (skip time check)
                if ($broadcast->trigger_event == 'sign_up') {
                     self::handleSignUpBroadcast($broadcast, $currentDate);
                     continue; // Move to the next broadcast
                }
                $mailSendTime = Carbon::createFromFormat('H:i:s', $broadcast->send_time,  $timezone); 
                $mailSendTime = $mailSendTime->format($timeFormat);
                // For all other trigger events, check send_time
                 if ($currentTime !== $mailSendTime) {
                     continue;
                 }
                 // Handle different trigger events
                switch ($broadcast->trigger_event) {
                    case 'day_after_sign_up':
                        self::handleDayAfterSignUpBroadcast($broadcast, $currentDate);
                        break;
                    case 'last_login':
                        self::handleLastLoginBroadcast($broadcast, $currentDate);
                        break;
                    case 'hasnt_logged_in':
                        self::handleHasNotLoggedInBroadcast($broadcast, $currentDate);
                        break;
                    case 'anniversary':
                        self::handleAnniversaryBroadcast($broadcast, $currentDate);
                        break;
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Handle 'sign_up' trigger event.
     */
    private static function handleSignUpBroadcast($broadcast, $currentDate)
    {
        $dateRange = dateRange($broadcast->from_day, $broadcast->to_day, 'Y-m-d');
        $users = User::with('userSubsription')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateRange['from_date'], $dateRange['to_date']])
            ->where('status', 'active')
            ->when($broadcast->user_types, function ($query, $userTypes) {
                return $query->where('user_type', $userTypes);
            })
            ->get();

        self::processUsersForBroadcast($users, $broadcast, 'sign_up');
    }

    /**
     * Handle 'day_after_sign_up' trigger event.
     */
    private static function handleDayAfterSignUpBroadcast($broadcast, $currentDate)
    {
        $dayAfter = Carbon::parse($currentDate)->subDay()->format('Y-m-d');
        $users = User::with('userSubsription')
            ->whereDate('created_at', $dayAfter)
            ->where('status', 'active')
            ->when($broadcast->user_types, function ($query, $userTypes) {
                return $query->where('user_type', $userTypes);
            })
            ->get();

        self::processUsersForBroadcast($users, $broadcast, 'day_after_sign_up');
    }

    /**
     * Handle 'last_login' trigger event.
     */
    private static function handleLastLoginBroadcast($broadcast, $currentDate)
    {
        $dateRange = dateRange($broadcast->from_day, $broadcast->to_day, 'Y-m-d');
        $users = User::with('userSubsription')
            ->whereBetween('last_login_date', [$dateRange['from_date'], $dateRange['to_date']])
            ->where('status', 'active')
            ->when($broadcast->user_types, function ($query, $userTypes) {
                return $query->where('user_type', $userTypes);
            })
            ->get();

        self::processUsersForBroadcast($users, $broadcast, 'last_login');
    }

    /**
     * Handle 'hasnt_logged_in' trigger event.
     */
    private static function handleHasNotLoggedInBroadcast($broadcast, $currentDate)
    {
        $fromDate = Carbon::parse($currentDate)->subDays(abs($broadcast->has_not_logged_in_days))->format('Y-m-d');
        $users = User::with('userSubsription')
            ->whereDate('last_login_date', '==', $fromDate)
            ->where('status', 'active')
            ->when($broadcast->user_types, function ($query, $userTypes) {
                return $query->where('user_type', $userTypes);
            })
            ->get();

        self::processUsersForBroadcast($users, $broadcast, 'hasnt_logged_in');
    }

    /**
     * Handle 'anniversary' trigger event.
     */
    private static function handleAnniversaryBroadcast($broadcast, $currentDate)
    {
        $selectedInterval = $broadcast->anniversary_months;
        $users = User::with('userSubsription')
            ->where('status', 'active')
            ->where('user_type', '!=', 'admin')
            ->get();

        foreach ($users as $user) {
            $anniversaryDate = date('Y-m-d', strtotime('+' . $selectedInterval . ' month', strtotime($user->created_at)));
            if (Carbon::parse($anniversaryDate)->isSameDay($currentDate)) {
                self::processUserForBroadcast($user, $broadcast, 'anniversary', ['anniversary_date' => $anniversaryDate]);
            }
        }
    }

    /**
     * Process users for a broadcast event.
     */
    private static function processUsersForBroadcast($users, $broadcast, $triggerEvent)
    {
        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                self::processUserForBroadcast($user, $broadcast, $triggerEvent);
            }
        }
    }

    /**
     * Process a single user for a broadcast event.
     */
    private static function processUserForBroadcast($user, $broadcast, $triggerEvent, $additionalData = [])
    {
        $tokens = json_decode(File::get(base_path('public/assets/broadcast-token.json')));
        $message = $broadcast->message ?? '';
        $emailSent = RecurringBroadcastLog::where('user_id', $user->id)
            ->where('broadcast_id', $broadcast->id)
           ->where(function($query) use ($broadcast) {
                $query->where('send_type', $broadcast->send_type) // Exact match
                      ->orWhere('send_type', 'like', '%' . $broadcast->send_type . '%'); // Match partial (alert,email includes alert)
            })
            ->where('trigger_event', $triggerEvent)
            ->when(isset($additionalData['anniversary_date']), function ($query) use ($additionalData) {
                return $query->whereYear('anniversary_date', Carbon::parse($additionalData['anniversary_date'])->year)
                            ->whereMonth('anniversary_date', Carbon::parse($additionalData['anniversary_date'])->month);
            })
            ->exists();
        if (!$emailSent) {
            $stripe_invoice_id = $user->userSubsription->stripe_invoice_id ?? null;
            $stripe_subscription_id = $user->userSubsription->stripe_subscription_id ?? null;
            $user_billing_detail = StripePayment::getInvoiceAndSubscription($stripe_invoice_id, $stripe_subscription_id);
            $workoutDates =  FitnessProfileRepository::getWorkoutDates($user->id);
            $url = env('APP_URL');
            $data = [
                'email' => $user->email,
                'name' => ucfirst($user->first_name . ' ' . $user->last_name),
                'user_name' =>  ucfirst($user->screen_name),
                'first_name' => ucfirst($user->first_name),
                'last_name' => ucfirst($user->last_name),
                'title' => $broadcast->title,
                'message' => $broadcast->message,
                'signup_date' => formatDate($user->created_at,'m-d-Y H:i:s'),
                'last_login_date' => formatDate($user->last_login_date,'m-d-Y H:i:s'),
                'broadcast_id' => $broadcast->id,
                'cell_phone_number' => $user->cell_phone_number,
                'user_id' => $user->id,
                'renewal_date' => formatDate($user_billing_detail['renewal_date'], 'm-d-Y'),
                'next_billing_date' => formatDate($user_billing_detail['next_billing_date'], 'm-d-Y'),
                'next_billing_amount' => $user_billing_detail['next_billing_amount'],
                'last_billing_date' => formatDate($user_billing_detail['last_billing_date'], 'm-d-Y'),
                'last_billing_amount' => $user_billing_detail['last_billing_amount'],
                'next_workout_date' => $workoutDates['next_workout_date'] ?? null,
                'last_workout_date' => $workoutDates['last_workout_date'] ?? null,
                'reset_password_url' => $url . '/' . $user->user_type . '/profile-setting',
            ] + $additionalData;



                foreach ($tokens as $token) {
                    $key = $token->token_key;
                    $pattern = '/\[\[\s*' . preg_quote($key, '/') . '\s*\]\]/';
                    $replacement = $data[$key] ?? '';
                    $message = preg_replace($pattern, $replacement, $message);
                }
                $data['message'] = $message;
            $sendTypeArray = explode(',', $broadcast['send_type']);
            // Dispatch job based on send type
            if (in_array('email', $sendTypeArray)) {
                RecurringBroadcastJob::dispatch($data);
            } if (in_array('alert', $sendTypeArray) && !empty($user->cell_phone_number)) {
                BroadcastAlertJob::dispatch($data);
            }
        } 
        // Update or create the RecurringBroadcastLog entry
        RecurringBroadcastLog::updateOrCreate(
            [
                'user_id' => $user->id,
                'broadcast_id' => $broadcast->id,
                'trigger_event' => $triggerEvent,
            ],
            [
                'send_type' => $broadcast->send_type,
                'anniversary_date' => $additionalData['anniversary_date'] ?? null,
                'updated_at' => now(),
                'created_at' => !$emailSent ? now() : null, // Set created_at only if it's a new log (not sent yet)
            ]
        );
    }
   
}