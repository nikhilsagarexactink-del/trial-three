<?php

namespace App\Repositories;

use App\Models\UserSubscription;
use App\Services\StripePayment;
use App\Repositories\UserRepository;
use App\Jobs\BillingNotificationJob;
use Carbon\Carbon;
use Config;
use Exception;
use Stripe;

class PaymentRepository
{

    protected $userRepository;

     // Inject UserRepository into PaymentRepository
     public function __construct(UserRepository $userRepository)
     {
        $this->userRepository = $userRepository;
     }
    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadPaymentList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserSubscription::with(['user']);

            if(!empty($post['payment_status']) && $post['payment_status'] == 'complete'){
                $list = $list->where('is_subscribed', 1);
            } else {
                $list = $list->where([['is_subscribed', 0], ['stripe_status', 'pending']]);
            }
            //Condiditon for user
            if ($userData->user_type != 'admin') {
                $list->where('user_id', $userData->id);
            }
            //Search from name
            if (! empty($post['user_name'])) {
                //$list->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
                $list->whereHas('user', function ($q) use ($post) {
                    $q->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['user_name'].'%');
                });
            }
            //Search from plan name
            if (! empty($post['plan_name'])) {
                $list->where('plan_name', 'like', '%'.$post['plan_name'].'%');
            }
            //Search from date
            if (! empty($post['start_date']) && ! empty($post['end_date'])) {
                $startDate = $post['start_date'].' 00:00:00';
                $endDate = $post['end_date'].' 23:59:00';
                $list->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate);
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('stripe_status', $post['status']);
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
     * Refund payment
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function refundPayment($request)
    {
        try {
            $post = $request->all();
            $subscription = UserSubscription::where(['id' => $request->id])->first();
            if (! empty($subscription)) {
                $data = [
                    'payment_intent_id' => $post['payment_intent_id'],
                    'amount' => $post['amount'],
                    'reason' => (! empty($post['refund_reason_type']) && $post['refund_reason_type'] != 'other') ? $post['refund_reason_type'] : '',
                ];
                $refund = StripePayment::refundAmount($data);

                if (! empty($refund)) {
                    $subscription->refund_id = $refund->id;
                    $subscription->refund_status = $refund->status;
                    $subscription->is_amount_refunded = 1;
                    $subscription->refund_amount = ! empty($post['amount']) ? $post['amount'] : 0;
                    $subscription->refund_reason_type = ! empty($post['refund_reason_type']) ? $post['refund_reason_type'] : '';
                    $subscription->refund_reason = ! empty($post['refund_reason']) ? $post['refund_reason'] : '';
                    $subscription->refund_log = serialize((array) $refund);
                    $subscription->save();

                    return $refund;
                } else {
                    throw new Exception($refund, 1);
                }
            } else {
                throw new Exception('No record found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Refund payment
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function invoiceDetail($request)
    {
        try {
            $settings = SettingRepository::getSettings(['stripe-secret-key']);
            Stripe\Stripe::setApiKey($settings['stripe-secret-key']);
            $invoice = Stripe\Invoice::retrieve($request->invoiceId);
            if (! empty($invoice)) {
                return $invoice;
            } else {
                throw new Exception('Record not found.', 1);
            }
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
            $paymentHistory = [];
            $detail = UserSubscription::select(
                'user_subscriptions.id',
                'user.first_name',
                'user.last_name',
                'user.email',
                'user.stripe_customer_id',
                'media.name as profile_image',
                'user_subscriptions.plan_name',
                'user_subscriptions.user_id',
                'user_subscriptions.stripe_invoice_id',
                'user_subscriptions.is_amount_refunded',
                'user_subscriptions.refund_id',
                'user_subscriptions.refund_amount',
                'user_subscriptions.refund_reason_type',
                'user_subscriptions.refund_reason',
                'user_subscriptions.refund_status',
                'user_subscriptions.subscription_type',
                'user_subscriptions.cost_per_month',
                'user_subscriptions.cost_per_year',
                'user_subscriptions.stripe_status',
                'user_subscriptions.created_at',
            );
            $detail->join('users AS user', 'user.id', '=', 'user_subscriptions.user_id');
            $detail->leftJoin('media', 'media.id', '=', 'user.media_id');
            $detail->where('user_subscriptions.id', $request->id);
            $detail = $detail->first();

            // Get Customer Subscription History
            $paymentDetail = StripePayment::getSubscriptionHistory($detail->stripe_customer_id);
            foreach ($paymentDetail as $key => $value) {
                $invoice = StripePayment::getInvoice($value->latest_invoice);
                $paymentHistory[$key]['amount'] = $value->plan->amount / 100;
                $paymentHistory[$key]['currency'] = $value->currency;
                $paymentHistory[$key]['date'] = \Carbon\Carbon::parse($value->created)->toDateString();
                $paymentHistory[$key]['status'] = $value->status;
                $paymentHistory[$key]['interval'] = $value->plan->interval;
                $paymentHistory[$key]['plan_name'] = ($value->plan->nickname) ? ucfirst($value->plan->nickname) : StripePayment::getProductName($value->plan->product)['name'];
                $paymentHistory[$key]['stripe_invoice_id'] = $value->latest_invoice;
                $paymentHistory[$key]['payment_status'] = $invoice->status;
            }
            $subsriptionHistory = [
                'detail' => $detail,
                'paymentHistory' => $paymentHistory,
            ];

            return $subsriptionHistory;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Sends notification emails to users with failed payments.
     *
     * This function checks for users with a 'payment_failed' status and 'athlete' user type,
     * and sends a reminder email if their signup date was 24 hours ago. Emails are dispatched
     * via the BillingNotificationJob queue.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * 
     * @throws \Exception If any error occurs during the execution.
     * 
     * @return bool True if the process completes successfully.
     */

    public static function paymentFailedNotificationCron() {
        try {
            $userRepository = app(UserRepository::class); // ✅ Use Laravel's container
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            
            $users = $userRepository->findAll([
                ['status', 'payment_failed'],
                ['user_type', 'athlete']
            ], 'userSubsription'); // Ensure correct relationship name
            if(!empty($users)) {
                foreach ($users as $user) {
                    $signupDateTime = Carbon::parse($user->created_at);
                    $reminderDateTime = $signupDateTime->copy()->addHours(24);
            
                    if ($reminderDateTime->isSameDay($currentDateTime)) {
                        BillingNotificationJob::dispatch(static::prepareEmailData($user));
                    }
                }
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    protected static function prepareEmailData($user) {
        return [
            'email' => $user->email,
            'name' => ucfirst($user->first_name),
            'title' => 'Payment Failed',
            'body' => 'Your account was soo close, but not quite finished.  Would you like to finish your signup?',
            'payment_link' => (!empty($user['userSubsription']) && !empty($user['userSubsription']['payment_link'])) ? $user['userSubsription']['payment_link'] : null,
        ];
    }

    public static function notifyToUser($request) {
        try {
            $userRepository = app(UserRepository::class); // ✅ Use Laravel's container
            $user = $userRepository->findOne([['id', $request['userId']]], 'userSubsription');
            if(!empty($user)) {
                BillingNotificationJob::dispatch(static::prepareEmailData($user));
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function sendPaymentFailedNotification($request) {
        try {
            $userRepository = app(UserRepository::class); // ✅ Use Laravel's container
            $currentDate = getTodayDate('Y-m-d');
            
            $subscriptions = $userRepository->findAllSubscription([['stripe_status', ['past_due', 'unpaid', 'incomplete']], ['renewal_date', '<=', $currentDate]], 'users');
            if(!empty($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    $user = $userRepository->findOne([['id', $subscription->user_id]], 'userSubsription');
                    if(!empty($user)) {
                        BillingNotificationJob::dispatch(static::prepareEmailData($user));
                    }
                }
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
