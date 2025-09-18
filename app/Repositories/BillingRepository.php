<?php

namespace App\Repositories;

use App\Jobs\BillingNotificationJob;
use App\Models\BillingNotification;
use App\Models\UserSubscription;
use App\Services\StripePayment;
use Config;

class BillingRepository
{
    /**
     * Load record list
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadBillingList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'user_subscriptions.created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');

            // Subquery to get the latest subscription ID per user
            $latestSubscriptions = UserSubscription::selectRaw('MAX(id) as latest_id')
                ->where('is_subscribed', 1)
                ->groupBy('user_id');

            $list = UserSubscription::select(
                'user_subscriptions.id',
                'user.first_name',
                'user.last_name',
                'user.email',
                'user.stripe_customer_id',
                'user_subscriptions.plan_name',
                'user_subscriptions.subscription_type',
                'user_subscriptions.stripe_subscription_id',
                'user_subscriptions.cost_per_month',
                'user_subscriptions.cost_per_year',
                'user_subscriptions.stripe_status',
                'user_subscriptions.created_at',
            )
            ->join('users AS user', 'user.id', '=', 'user_subscriptions.user_id')
            ->whereIn('user_subscriptions.id', $latestSubscriptions) // Fetch only latest subscriptions
            ->orderBy($sortBy, $sortOrder);

            // Apply user type restrictions
            $list->where(function ($q) use ($userData) {
                if ($userData->user_type != 'admin') {
                    $q->where(function ($subQuery) use ($userData) {
                        $subQuery->where('user.created_by', $userData->id)
                                ->orWhere('user_subscriptions.parent_subscription_id', $userData->id);
                    });
                }
            });

            // Search from name
            if (!empty($post['user_name'])) {
                $list->whereRaw('concat(user.first_name," ",user.last_name) like ?', ['%' . $post['user_name'] . '%']);
            }
            // Search from plan name
            if (!empty($post['plan_name'])) {
                $list->where('user_subscriptions.plan_name', 'like', '%' . $post['plan_name'] . '%');
            }
            // Search from date
            if (!empty($post['start_date']) && !empty($post['end_date'])) {
                $startDate = $post['start_date'] . ' 00:00:00';
                $endDate = $post['end_date'] . ' 23:59:00';
                $list->whereBetween('user_subscriptions.created_at', [$startDate, $endDate]);
            }

            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * Detail
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function detail($request)
    {
        try {
            $detail = UserSubscription::where('user_subscriptions.status','active')
                ->join('users AS user', 'user.id', '=', 'user_subscriptions.user_id')
                ->leftJoin('media', 'media.id', '=', 'user.media_id')
                ->select(
                    'user_subscriptions.id',
                    'user.id as athlete_id',
                    'user.stripe_customer_id',
                    'user.first_name',
                    'user.last_name',
                    'user.email',
                    'media.name as profile_image',
                    'user_subscriptions.stripe_subscription_id',
                    'user_subscriptions.plan_name',
                    'user_subscriptions.plan_id',
                    'user_subscriptions.subscription_date',
                    'user_subscriptions.subscription_type',
                    'user_subscriptions.cost_per_month',
                    'user_subscriptions.cost_per_year',
                    'user_subscriptions.stripe_status',
                    'user_subscriptions.created_at',
                    'user_subscriptions.updated_at'
                )
                ->where('user.stripe_customer_id', $request->customerId)
                ->orderBy('user_subscriptions.updated_at', 'DESC')
                ->first();

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function userBillingAlertCron()
    {
        try {
            $settings = SettingRepository::getSettings();
            $currentDate = getTodayDate('Y-m-d');
            // Retrieve settings for billing alert
            $billingAlertDays = intval($settings['billing-alert-days']);
            $alertNote = $settings['billing-alert-note']; // HTML content

            $list = UserSubscription::select(
                'user_subscriptions.*',
                'users.stripe_customer_id',
                'users.email',
                'users.first_name',
                'users.last_name'
            )
                ->join('users', 'users.id', '=', 'user_subscriptions.user_id')
                ->where([['user_subscriptions.is_subscribed', 1], ['user_subscriptions.stripe_status', '!=', 'canceled'], ['users.stripe_customer_id', '!=', null], ['user_subscriptions.status', '!=', 'deleted']])
                ->get();

            if (! empty($list)) {
                foreach ($list as $subscription) {
                    if (! empty($subscription->stripe_subscription_id)) {
                        $stripeSubscriptionPeriod = StripePayment::getSubscriptionPeriod($subscription->stripe_subscription_id);
                        $nextSubscriptionDate = (!empty($stripeSubscriptionPeriod) ? $stripeSubscriptionPeriod['end_date'] : '');
                        if (! empty($nextSubscriptionDate)) {
                            $nextSubscriptionDate = strtotime($nextSubscriptionDate);
                            $alertDate = strtotime('-'.$billingAlertDays.' days', $nextSubscriptionDate);
                            $alertDate = date('Y-m-d', $alertDate);

                            $checkCurrentNotificationDetail = BillingNotification::where([['notification_date', '>=', $alertDate], ['user_subscription_id', $subscription->id]])->first();
                            if (empty($checkCurrentNotificationDetail)) {
                                // Calculate the difference in days
                                $dayDifference = \Carbon\Carbon::parse($alertDate)->diffInDays(\Carbon\Carbon::parse($subscription->subscription_date));
                                // echo $currentDate.'=='.$alertDate;
                                // exit;
                                if ($currentDate == $alertDate) {
                                    // Prepare email data with HTML support
                                    $emailData = [
                                        'email' => $subscription->email,
                                        'name' => ucfirst($subscription->first_name),
                                        'title' => 'Billing Alert',
                                        'body' => $alertNote, // HTML content from settings
                                    ];

                                    BillingNotificationJob::dispatch($emailData);

                                    // Insert a new record in the billing_notifications table
                                    $billingNotification = new BillingNotification();
                                    $billingNotification->user_id = $subscription->user_id;
                                    $billingNotification->is_notification_sent = 1;
                                    $billingNotification->notification_date = $alertDate;
                                    $billingNotification->user_subscription_id = $subscription->id;
                                    $billingNotification->created_by = $subscription->user_id;
                                    $billingNotification->updated_by = $subscription->user_id;
                                    $billingNotification->save();
                                }
                            }
                        }
                    }
                }
            }

            return true;

            // // Get all active subscriptions that are not canceled
            // $usersSubscription = UserSubscription::with(['user'])
            //     ->where([['is_subscribed', 1], ['stripe_status', '!=', 'canceled']])
            //     ->whereDoesntHave('billingNotification', function ($query) {
            //         $query->where('is_notification_sent', 1);
            //     })
            //     ->get();
            // if (! empty($usersSubscription)) {
            //     foreach ($usersSubscription as $subscription) {
            //         $user = $subscription->user;
            //         if (! empty($user->stripe_customer_id)) {
            //             $nextSubscriptionDate = StripePayment::getNextSubscriptionDate($user->stripe_customer_id);
            //         }
            //         $nextSubscriptionDate = strtotime($nextSubscriptionDate);
            //         // Calculate alert date
            //         $subscriptionDate = strtotime($nextSubscriptionDate);
            //         $alertDate = strtotime('-'.$billingAlertDays.' days', $nextSubscriptionDate);
            //         $alertDate = date('Y-m-d', $alertDate);

            //         // Calculate the difference in days
            //         $dayDifference = \Carbon\Carbon::parse($alertDate)
            //             ->diffInDays(\Carbon\Carbon::parse($subscription->subscription_date));
            //         // Check if the alert date is the same as the subscription date
            //         if ($currentDate == $alertDate) {
            //             // Prepare email data with HTML support
            //             $emailData = [
            //                 'email' => $user->email,
            //                 'name' => ucfirst($user->first_name),
            //                 'title' => 'Billing Alert',
            //                 'body' => $alertNote, // HTML content from settings
            //             ];

            //             BillingNotificationJob::dispatch($emailData);

            //             // Insert a new record in the billing_notifications table
            //             $billingNotification = new BillingNotification();
            //             $billingNotification->user_id = $user->id;
            //             $billingNotification->is_notification_sent = 1;
            //             $billingNotification->notification_date = $alertDate;
            //             $billingNotification->user_subscription_id = $subscription->id;
            //             $billingNotification->created_by = $user->id;
            //             $billingNotification->updated_by = $user->id;
            //             $billingNotification->save();
            //         }
            //     }
            // } else {
            //     return false;
            // }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}