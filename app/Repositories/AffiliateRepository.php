<?php

namespace App\Repositories;
use App\Models\AffiliateSetting;
use App\Models\AffiliateApplication;
use App\Models\AffiliateReferral;
use App\Models\PayoutLog;
use App\Models\UserAffiliatePayoutSetting;
use App\Models\UserSubscription;
use App\Services\StripePayment;
use Carbon\Carbon;
use Config;
use Str;
use DB;
use Log;

class AffiliateRepository
{


    public static function findOneApplication($where, $with=[]) {
        return AffiliateApplication::where($where)->with($with)->first();
    }

    public static function findOneUserAffiliateSetting($where, $with=[]) {
        return UserAffiliatePayoutSetting::where($where)->with($with)->first();
    }

    public static function totalEarnings($where, $with=[]) {
        return AffiliateReferral::where($where)->with($with)
        ->whereHas('user', function ($q) {
            $q->where('status', '!=', 'payment_failed');
        })
        ->sum('earnings');
    }
   public static function saveSetting($request) {
        try {
            $post = $request->all();
            $keys = [
                'plan_type',
                'commission_percentage',
                'description',
                'service_text',
            ];
            foreach ($post as $key => $value) {
                $setting = AffiliateSetting::where('key', $key)->first();
                if (!empty($setting) && in_array($key, $keys)) {
                    if($key == 'plan_type') {
                        $value = implode(',', $value);
                    }
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
   }

   public static function getSettings($keys = [])
    {
        try {
            $settingArr = [];
            if (! empty($keys)) {
                $settingKeys = [];
                foreach ($keys as $key) {
                    $settingKeys[$key] = '';
                }
            } else {
                $settingKeys = [
                    'plan_type',
                    'commission_percentage',
                    'description',
                    'service_text',
                ];
            }

            $settings = AffiliateSetting::where('status', '!=', 'deleted')->get();
            if (! empty($settings)) {
                foreach ($settings as $key => $data) {
                    if (count($keys) == 0 || in_array($data['key'], $keys)) {
                        // Decrypt sensitive fields
                        if (in_array($data['key'], ['plan_type', 'commission_percentage', 'description', 'service_text',])) {
                            $settingArr[$data['key']] = $data['value'];
                        } else {
                            $settingArr[$data['key']] = $data['value'];
                        }
                    }
                 }            
            }
            return $settingArr;
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    public static function applyApplication($request){
        try{
            $post = $request->all();
            $userData = getUser();
            $application = AffiliateApplication::where('user_id', $userData->id)->first();
            if(empty($application)){
                throw new \Exception('Please ensure that you are enabled as an affiliate.');
            }
            // $application = new AffiliateApplication();
            $application->user_id = $userData->id;
            $application->address = isset($post['address']) ? $post['address'] : '';
            $application->terms_agreed_at = now();
            $application->created_at = now();
            $application->save();
            return true;
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function affiliateToggle($request){
        try{
            $post = $request->all();
            $userData = getUser();
            $application = AffiliateApplication::where('user_id', $userData->id)->first();
            if(empty($application)){
                $application = new AffiliateApplication();
                $application->user_id = $userData->id;
                $application->created_at = now();
            }
            $application->is_enabled = $post['is_enabled'] == 'enabled' ? 1 : 0;
            $application->updated_at =  now();
            $application->save();
            return true;
        }catch(\Exception $ex){
            throw $ex;
        }
    }


    public static function loadAffiliateMembers($request){
        try{
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $application = AffiliateApplication::with('userPayoutSetting')->where([['status', '!=', 'deleted'], ['terms_agreed_at', '!=', null]]);

            //Search from name
            if (! empty($post['search'])) {
                $application->whereHas('user', function ($query) use ($post) {
                    $query->where('first_name', 'like', '%'.$post['search'].'%');
                    $query->orWhere('last_name', 'like', '%'.$post['search'].'%');
                });
            }
            //Search from status
            if (! empty($post['status'])) {
                $application->where('status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $application = $application->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

            return $application;
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function changeAffiliateStatus($request){
        try{
            $post = $request->all();
            $application = AffiliateApplication::find($request->id);
            if(empty($application)){
                throw new \Exception('Application not found');
            }
            if($post['status'] == 'approved'){
                $application->token = Str::random(20);
            }else{
                $application->token = null;
            }
            $application->status = $post['status'];
            $application->save();
            return true;
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function loadAffiliateSubscribers($request){
        try{
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $subscribers = AffiliateReferral::where('status', '!=', 'deleted')->where('user_affiliate_id', $userData->id)->with('user.userSubsription')->whereHas('user',function($q){
                $q->where('status','!=','payment_failed');
            });
            $activeSubscribers = clone $subscribers;
            // Active Subscribers
            $activeSubscribers->whereHas('user', function ($q) {
                $q->where('status', '!=', 'payment_failed');
            });
            $totalEarning = $activeSubscribers->sum('earnings');
            if($userData->user_type != 'admin'){
                $subscribers->where('user_affiliate_id', $userData->id);
            }

            //Search from name
            if (! empty($post['search'])) {
                $search = $post['search'];
                $subscribers->whereHas('user', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                    });
                });
            }
            //Search from status
            if (! empty($post['status'])) {
                $subscribers->whereRelation('user','status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $subscribers = $subscribers->paginate($paginationLimit);
            $subscribers->totalEarning = $totalEarning;
            return $subscribers;
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function savePayoutSetting($request){
        try{
            $post = $request->all();
            $userData = getUser();
            $payoutSetting = UserAffiliatePayoutSetting::where('user_id', $userData->id)->first();
            if(empty($payoutSetting)){
                $payoutSetting = new UserAffiliatePayoutSetting();
                $payoutSetting->user_id = $userData->id;
            }
            $payoutSetting->payout_method = $post['payout_method'];
            if($post['payout_method'] == 'billing_credit'){
                $payoutSetting->email = null;
                $payoutSetting->phone_number = null;
            }elseif($post['payout_method'] == 'paypal'){
                $payoutSetting->email = $post['email'];
                $payoutSetting->phone_number = null;
            }else{
                $payoutSetting->email = null;
                $payoutSetting->phone_number = $post['phone_number'];
            }
            $payoutSetting->name = !empty($post['name']) ? $post['name'] : '';
            $payoutSetting->created_by = $userData->id;
            $payoutSetting->updated_by = $userData->id;
            $payoutSetting->save();
            return true;

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function addPayoutLog($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');

            $model = new PayoutLog();
            $model->amount = $post['amount'];
            $model->payout_type = $post['payout_type'];
            $model->user_affiliate_id = $post['user_affiliate_id'];
            $model->paid_at = $currentDateTime;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            $application = AffiliateApplication::where([['user_id', $post['user_affiliate_id']], ['status', '!=', 'deleted']])->first();

            if (!empty($application)) {
                $remainEarnings = $application->total_earnings - $post['amount'];
                $application->total_earnings = $remainEarnings;
                $application->save();  // <-- Fix here: call the save method
            } else {
                throw new \Exception('Affiliate application not found.', 1);
            }

            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    
    public static function loadPayoutHistoryList($request){
        try{
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $application = PayoutLog::where('user_affiliate_id',  $post['user_affiliate_id']);
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $application = $application->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

            return $application;
        }catch(\Exception $ex){
            throw $ex;
        }
    }


    public static function affiliateCreditCron()
    {
        try {
            $firstDayOfMonth = getFirstDayOfMonth('Y-m-d');
            $applications = self::getEligibleAffiliateApplications();
            if ($applications->isEmpty()) {
                return false;
            }

            foreach ($applications as $affiliate) {
                self::processAffiliateCredit($affiliate, $firstDayOfMonth);
            }

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    protected static function getEligibleAffiliateApplications()
    {
        return AffiliateApplication::with('user')
            ->where('total_earnings', '>=', 100)
            ->whereHas('user', function ($query) {
                $query->where('user_type', '!=', 'admin')
                    ->where('status', '!=', 'deleted');
            })
            ->activeApplication()
            ->get();
    }

    protected static function processAffiliateCredit($affiliate, $firstDayOfMonth)
    {
        $user = $affiliate->user;
        
        if (!self::isUserEligibleForCredit($user, $firstDayOfMonth)) {
            Log::info('Subscription is yearly and it end month not match current month.');
            return;
        }

        $upcomingInvoice = StripePayment::getUpcomingInvoice($user->stripe_customer_id);
        $invoiceAmountDue = $upcomingInvoice->amount_due / 100;
        $discountAmount = min($affiliate->total_earnings, $invoiceAmountDue);
        $discountAmount = round($discountAmount, 2);

        if ($discountAmount <= 0) {
            Log::info("Affiliate discount skipped: Zero amount for invoice");
            return;
        }

        DB::beginTransaction();
        try {
            self::applyAffiliateCredit($user, $affiliate, $discountAmount, $firstDayOfMonth);
            DB::commit();
            
            $user->notify(
                (new \App\Notifications\AffiliateDiscountApplied($discountAmount))
                ->delay(now()->addSeconds(3))
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected static function isUserEligibleForCredit($user, $firstDayOfMonth)
    {
        $userAffiliateSetting = UserAffiliatePayoutSetting::where('user_id', $user->id)->first();
        // Check payout settings
        if (empty($userAffiliateSetting) || $userAffiliateSetting->payout_method != 'billing_credit') {
            Log::info("Affiliate discount skipped: Billing credit not enabled for user {$user->id}");
            return false;
        }

        // Check existing payouts
        if (PayoutLog::where('user_affiliate_id', $user->id)
            ->whereDate('paid_at', $firstDayOfMonth)
            ->where('payout_type', 'billing_credit')
            ->exists()) {
            Log::info("Affiliate discount already applied for invoice, skipping.");
            return false;
        }

        // Check subscription
        $subscription = UserSubscription::where([
            ['user_id', $user->id],
            ['stripe_status', 'active']
        ])->first();
        if (empty($subscription) || $subscription->subscription_type == 'free') {
            Log::info("Affiliate discount skipped: No active subscription for user {$user->id}");
            return false;
        }

        if ($subscription->subscription_type == 'yearly') {
            $upcomingInvoice = StripePayment::getUpcomingInvoice($user->stripe_customer_id);
            return self::subscriptionEndingThisMonth($user, $upcomingInvoice);
        }
        return true;
    }

    protected static function applyAffiliateCredit($user, $affiliate, $amount, $date)
    {
        // Update affiliate balance
        $affiliate->update([
            'total_earnings' => $affiliate->total_earnings - $amount,
            'updated_at' => now(),
        ]);

        // Log the payout
         $payoutLog = PayoutLog::create([
            'user_affiliate_id' => $user->id,
            'amount' => $amount,
            'payout_type' => 'billing_credit',
            'paid_at' => $date
        ]);
        try {
            StripePayment::creditAffiliate($user->stripe_customer_id, $amount);
        } catch (\Exception $e) {
            // If Stripe fails, reverse the DB changes
            $affiliate->update([
                'total_earnings' => $affiliate->total_earnings + $amount,
                'updated_at' => now(),
            ]);
            
            $payoutLog->delete();
            
            throw new \Exception("Stripe credit failed: " . $e->getMessage());
        }
    }

    protected static function subscriptionEndingThisMonth($user, $upcomingInvoice){
        try{
            $subscriptionPeriod = StripePayment::getSubscriptionPeriod($upcomingInvoice->subscription);
            $nextBillingDate = !empty($subscriptionPeriod) ? $subscriptionPeriod['end_date'] : '';
            $endDate = Carbon::parse($nextBillingDate);
            // Get start and end of the current month
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Check if the subscription ends in the current month
            $isEndingThisMonth = $endDate->between($startOfMonth, $endOfMonth);

            return $isEndingThisMonth;
        }catch(\Exception $ex){
            throw $ex;
        }
    }
}