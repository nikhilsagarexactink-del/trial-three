<?php

namespace App\Repositories;

use App\Jobs\BroadcastEmailJob;
use App\Jobs\BroadcastAlertJob;
use App\Models\Broadcast;
use App\Services\SmsService;
use App\Services\PostmarkService;
use App\Models\User;
use App\Services\StripePayment;
use App\Models\UserBroadcastAlert;
use App\Repositories\FitnessProfileRepository;
use Config;
use DB;
use Exception;
use Aws\CloudWatch\CloudWatchClient;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;

class BroadcastRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Broadcast
     */
    public static function findOne($where, $with = [])
    {
        return Broadcast::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Broadcast
     */
    public static function findAll($where, $with = [])
    {
        return Broadcast::with($with)->where($where)->get();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadBroadcastList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Broadcast::where('status', '!=', 'deleted')->orderBy($sortBy, $sortOrder);
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
            $model = Broadcast::where(['id' => $request->id])->first();
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
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Broadcast
     */
    public static function sendNotification($broadcast, $timezone = '')
    {
        try {
            $toDate = getTodayDate('Y-m-d');
            $sendTypeArray = $array = explode(',', $broadcast['send_type']);
            $fromDate = date('Y-m-d', strtotime('-30 day', strtotime($toDate)));
            if ($broadcast['type'] === 'scheduled'){
                $toDate = getLocalDateTime('', 'Y-m-d', $timezone);
            }

            $users = User::where('status', '!=', 'deleted');
            if (! empty($broadcast['users_status'])) {
                $users->whereIn('status', explode(',', $broadcast['users_status']));
            }
            if (! empty($broadcast['signed_up_last_thirty_days']) && $broadcast['signed_up_last_thirty_days'] == 1) {
                // $users->where('created_at', '>=', $fromDate)->where('created_at', '<=', $toDate);
                $users->whereBetween('created_at', [$fromDate, $toDate]);
            }
            if (! empty($broadcast['user_types'])) {
                $users->whereIn('user_type', explode(',', $broadcast['user_types']));
            }

            if (! empty($broadcast['last_login_between']) && $broadcast['last_login_between'] == 1) {
                $users->where('last_login_date', '>=', $broadcast['last_login_from_date'])->where('last_login_date', '<=', $broadcast['last_login_to_date']);
            }
            $users = $users->get();
            $jobs = [];
            foreach ($users as $user) {
                $stripe_invoice_id = $user->userSubsription->stripe_invoice_id ?? null;
                $stripe_subscription_id = $user->userSubsription->stripe_subscription_id ?? null;
                $user_billing_detail = StripePayment::getInvoiceAndSubscription($stripe_invoice_id, $stripe_subscription_id);
                $workoutDates =  FitnessProfileRepository::getWorkoutDates($user->id);
                $url = env('APP_URL');

                $emailData = [
                    'email' => $user->email, //'test@mailinator.com',
                    'name' => ucfirst($user->first_name . ' ' . $user->last_name),
                    'user_name' =>  ucfirst($user->screen_name),
                    'first_name' => ucfirst($user->first_name),
                    'last_name' => ucfirst($user->last_name),
                    'title' => $broadcast['title'],
                    'message' => $broadcast['message'],
                    'broadcast_id' => $broadcast['id'],
                    'cell_phone_number' => $user->cell_phone_number,
                    'user_id' => $user->id ,// For Dashboard Alert
                    'signup_date' => $user->created_at,
                    'last_login_date' => $user->last_login_date,
                    'renewal_date' => formatDate($user_billing_detail['renewal_date'], 'm-d-Y'),
                    'next_billing_date' => formatDate($user_billing_detail['next_billing_date'], 'm-d-Y'),
                    'next_billing_amount' => $user_billing_detail['next_billing_amount'],
                    'last_billing_date' => formatDate($user_billing_detail['last_billing_date'], 'm-d-Y'),
                    'last_billing_amount' => $user_billing_detail['last_billing_amount'],
                    'next_workout_date' => $workoutDates['next_workout_date'] ?? null,
                    'last_workout_date' => $workoutDates['last_workout_date'] ?? null,
                    'reset_password_url' => $url . '/' . $user->user_type . '/profile-setting',
                ];
                // Check for 'email' and dispatch the job if found
                if (in_array('email', $sendTypeArray)) {
                    BroadcastEmailJob::dispatch($emailData);
                }

                // Check for 'Dashboard Alert' and save data to database if found
                if (in_array('dashboard_alert', $sendTypeArray)) {
                    self::saveDashboardAlerts($emailData);
                }

                // Check for 'alert' and dispatch the job if found
                if (in_array('alert', $sendTypeArray) && !empty($user->cell_phone_number)) {
                    BroadcastAlertJob::dispatch($emailData);
                }
            }
            Broadcast::where('id', $broadcast['id'])->update(['is_notification_sent' => 1]);
            // Dispatch the batch of jobs
            // Bus::batch($jobs)->dispatch();
            
            return true;
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
            $model = new Broadcast();
            $model->title = $post['title'];
            $model->message = $post['message'];
            $model->type = $post['type'];
            $model->send_type = ! empty($post['send_type']) ? implode(',', $post['send_type']) : null;
            $model->users_status = ! empty($post['users_status']) ? implode(',', $post['users_status']) : null;
            $model->user_types = ! empty($post['user_types']) ? implode(',', $post['user_types']) : null;
            $model->signed_up_last_thirty_days = ! empty($post['signed_up_last_thirty_days']) ? $post['signed_up_last_thirty_days'] : 0;
            $model->last_login_between = ! empty($post['last_login_between']) ? $post['last_login_between'] : 0;
            if ($post['type'] == 'scheduled') {
            $model->scheduled_date = !empty($post['scheduled_date']) ? Carbon::parse($post['scheduled_date'])->toDateString(): null;
            $model->scheduled_time = !empty($post['scheduled_time']) ? Carbon::createFromFormat('g:i A', $post['scheduled_time'])->format('H:i:s'): null;
            $model->timezone = !empty($post['timezone']) ? $post['timezone'] : null;
            } else {
            $timezone = env('APP_TIMEZONE', 'America/Denver');
            $now = Carbon::now($timezone);
            $model->scheduled_date = $now->toDateString();  // '2025-06-19'
            $model->scheduled_time = $now->format('H:i:s'); // '14:15:00'
            $model->timezone       = $timezone;
            }

            if (! empty($post['last_login_between']) && $post['last_login_between'] == 1) {
                    $model->last_login_from_date = !empty($post['last_login_from_date']) ? Carbon::parse($post['last_login_from_date'])->toDateString(): null;
                    $model->last_login_to_date = !empty($post['last_login_to_date']) ? Carbon::parse($post['last_login_to_date'])->toDateString(): null;
            } else {
                $model->last_login_from_date = null;
                $model->last_login_to_date = null;
            }

            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            if ($post['type'] == 'now') {
                self::sendNotification($model);
            }
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
                $model->type = $post['type'];
                $model->send_type = ! empty($post['send_type']) ? implode(',', $post['send_type']) : null;
                $model->users_status = ! empty($post['users_status']) ? implode(',', $post['users_status']) : null;
                $model->user_types = ! empty($post['user_types']) ? implode(',', $post['user_types']) : null;
                $model->signed_up_last_thirty_days = ! empty($post['signed_up_last_thirty_days']) ? $post['signed_up_last_thirty_days'] : 0;
                $model->last_login_between = ! empty($post['last_login_between']) ? $post['last_login_between'] : 0;
                if ($post['type'] == 'scheduled') {
                    $model->scheduled_date = !empty($post['scheduled_date']) ? Carbon::parse($post['scheduled_date'])->toDateString(): null;
                    $model->scheduled_time = !empty($post['scheduled_time']) ? Carbon::createFromFormat('g:i A', $post['scheduled_time'])->format('H:i:s'): null;
                    $model->timezone = !empty($post['timezone']) ? $post['timezone'] : null;
                } 
                // else {
                //     $model->scheduled_date = null;
                //     $model->scheduled_time = null;
                //     $model->timezone = null;
                // }

                if (! empty($post['last_login_between']) && $post['last_login_between'] == 1) {
                       $model->last_login_from_date = !empty($post['last_login_from_date']) ? Carbon::parse($post['last_login_from_date'])->toDateString(): null;
                       $model->last_login_to_date = !empty($post['last_login_to_date']) ? Carbon::parse($post['last_login_to_date'])->toDateString(): null;
                } else {
                    $model->last_login_from_date = null;
                    $model->last_login_to_date = null;
                }
                if ($post['type'] == 'now') {
                    $model->is_notification_sent = 0;
                } elseif ($post['type'] == 'scheduled'
                            && ! empty($broadcast->scheduled_date)
                            && ! empty($broadcast->scheduled_time)
                ) {
                    $scheduleTime = $broadcast->scheduled_date.' '.$broadcast->scheduled_time;
                    if ($scheduleTime >= $currentDateTime) {
                        $model->is_notification_sent = 0;
                    }
                }
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();
                if ($post['type'] == 'now') {
                    self::sendNotification($model);
                }
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
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function broadcastMessageCron()
    {
        try {
            $currentDate = getTodayDate('Y-m-d');
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $settings = SettingRepository::getSettings();
            $timezone = ! empty($settings['timezone']) ? $settings['timezone'] : '';
            $broadcasts = Broadcast::where('type', 'scheduled')
                                    ->where('scheduled_date', $currentDate)
                                    ->where('is_notification_sent', 0)
                                    ->where('status', '!=', 'deleted')->get();
            foreach ($broadcasts as $broadcast) {
                if (! empty($broadcast->scheduled_date) && ! empty($broadcast->scheduled_time)) {
                    // $scheduleTime = $broadcast->scheduled_date.' '.$broadcast->scheduled_time;
                    $scheduleTime = Carbon::createFromFormat('Y-m-d H:i:s', $broadcast->scheduled_date . ' ' . $broadcast->scheduled_time, $timezone);
                    // dd($scheduleTime->lessThanOrEqualTo($currentDateTime));
                    $currentDateTime = Carbon::now($timezone);
                    if ($scheduleTime->lessThanOrEqualTo($currentDateTime)) {
                        // This is a safer, clearer comparison
                        self::sendNotification($broadcast, $timezone);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function fetchBroadcastStatics($request){
        try {
            $post = $request->all();
            $startDate = $post['start_date'];
            $endDate = $post['end_date'];
            $broadcastId = $post['broadcast_id'];
            // Get specific email stats
            $stats = PostmarkService::getEmailStats($startDate, $endDate,$broadcastId);
            return $stats;
        } catch (Aws\Exception\AwsException $e) {
            echo $e->getMessage();
        }
    }

    public static function sendAlertNotification($user)
    {
        try {
            $messageBody = strip_tags($user['message']);  // The message content
            $userName = $user['name'];        // The user's name
            if (empty($user['cell_phone_number'])) {
                return false;
            }
            $phoneNumber = $user['cell_phone_number']; // International format
            $message = "Hi " . $userName . ",\n\n" . $messageBody;
            $messageId = SmsService::sendTextSms($phoneNumber, $message);
            return $messageId;
        } catch (\Exception $e) {
            throw $ex;
        }
    }

    public static function saveDashboardAlerts($data){
        try {
            $userData = getUser();
            $findAlert = UserBroadcastAlert::where('user_id', $data['user_id'])->first();
            if(!empty($findAlert)){
                $findAlert->broadcast_id  = $data['broadcast_id'];
                $findAlert->user_id  = $data['user_id'];
                $findAlert->is_visible  = 1;
                $findAlert->created_by  = $userData->id;
                $findAlert->updated_by  = $userData->id;
                $findAlert->save();
            } elseif(empty($findAlert)){
                $model = new UserBroadcastAlert();
                $model->broadcast_id  = $data['broadcast_id'];
                $model->user_id  = $data['user_id'];
                $model->is_visible  = 1;
                $model->created_by  = $userData->id;
                $model->updated_by  = $userData->id;
                $model->save();
            }
            return true;
        } catch (\Exception $e) {
            throw $ex;
        }
    }

    public static function removeBroadcastAlert($request){
        try {
            $userData = getUser();
            $alert = UserBroadcastAlert::where('user_id', $userData->id)->where('broadcast_id', $request)->first();
            if(!empty($alert)){
                $alert->is_visible = 0;
                $alert->updated_by = $userData->id;
                $alert->save();
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function getDashboardAlert(){
        try {
            $userData = getUser();
            $broadcastAlert = UserBroadcastAlert::where([
                ['user_id', $userData->id],
                ['status', 'active'],
                ['is_visible', 1]
            ])
            ->whereHas('broadcast', function ($query) {
                $query->whereRaw("FIND_IN_SET('dashboard_alert',send_type)") // Ensure it's a dashboard alert
                    ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7)); // Within 7 days
            })
            ->with('broadcast')
            ->first();
            return $broadcastAlert;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}