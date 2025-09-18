<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Models\PromoCode;
use App\Models\PromoPlan;
use App\Models\UserSubscription;
use App\Services\StripePayment;
use Carbon\Carbon;
use Config;
use DB;
use Exception;

class PromoCodeRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  PromoCode
     */
    public static function findOne($where, $with = [])
    {
        return PromoCode::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  PromoCode
     */
    public static function findAll($where, $with = [])
    {
        return PromoCode::with($with)->where($where)->get();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadPromoCodeList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = PromoCode::with('plans.plan')->where('status', '!=', 'deleted');
            if ($userData->user_type != 'admin') {
                $list->where('created_by', $userData->id);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('code', 'like', '%'.$post['search'].'%');
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
            $model = PromoCode::where(['id' => $request->id])->first();
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
            // Convert expiration date format from 'm-d-Y' to 'Y-m-d'
            $expirationDate = Carbon::createFromFormat('m-d-Y', $request->expiration_date)->format('Y-m-d');
    
            // Create new promo code
            $model = new PromoCode();
            $model->code = $post['code'];
            $model->expiration_date = $expirationDate;
            $model->no_of_users_allowed = $post['no_of_users_allowed'];
            $model->discount_type = $post['discount_type'];
            $model->discount_amount = $post['discount_amount'];
            $model->discount_percentage = $post['discount_percentage'];
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
    
            // Handle selected plans (both monthly and yearly)
            $planData = [];
            foreach ($post['plans'] as $planValue) {
                list($planId, $planType) = explode('_', $planValue); // Extract plan ID and type
                $planData[] = [
                    'promo_code_id' => $model->id,
                    'plan_id' => $planId,
                    'plan_type' => $planType, // 'monthly' or 'yearly'
                    'created_by' => $userData->id,
                    'updated_by' => $userData->id,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime,
                ];
            }
    
            // Insert multiple promo plan entries
            PromoPlan::insert($planData);
    
            // Create promo code in Stripe
            $promoCode = StripePayment::createPromoCode([
                'percent_off' => $post['discount_type'] == 'percent' ? (!empty($post['discount_percentage']) ? $post['discount_percentage'] : null) : null,
                'amount_off' => $post['discount_type'] == 'amount' ? (!empty($post['discount_amount']) ? ($post['discount_amount'] * 100) : null) : null,
                'discount_type' => $post['discount_type'],
                'duration' => 'once',
            ]);
    
            // Save Stripe coupon ID
            $model->stripe_coupon_id = $promoCode->id;
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
        $currentDateTime = getTodayDate('Y-m-d H:i:s');

        // Convert expiration date format from 'm-d-Y' to 'Y-m-d'
        $expirationDate = Carbon::createFromFormat('m-d-Y', $request->expiration_date)->format('Y-m-d');

        // Find existing promo code
        $model = self::findOne(['id' => $request->id]);

        if (!empty($model)) {
            $oldDiscountAmount = $model->discount_amount;
            $oldDiscountPercent = $model->discount_percentage;

            // Update promo code details
            $model->code = $post['code'];
            $model->expiration_date = $expirationDate;
            $model->no_of_users_allowed = $post['no_of_users_allowed'];
            $model->discount_type = $post['discount_type'];
            $model->discount_amount = !empty($post['discount_amount']) ? $post['discount_amount'] : null;
            $model->discount_percentage = !empty($post['discount_percentage']) ? $post['discount_percentage'] : null;
            $model->updated_by = $userData->id;
            $model->updated_at = $currentDateTime;
            $model->save();

            // Remove old selected plans only if new ones exist
            if (!empty($post['plans'])) {
                PromoPlan::where('promo_code_id', $model->id)->delete();

                // Prepare new plan entries
                $planData = [];
                foreach ($post['plans'] as $planValue) {
                    list($planId, $planType) = explode('_', $planValue); // Extract plan ID and type
                    $planData[] = [
                        'promo_code_id' => $model->id,
                        'plan_id' => $planId,
                        'plan_type' => $planType, // 'monthly' or 'yearly'
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                    ];
                }
                PromoPlan::insert($planData);
            }

            // Check if Stripe coupon needs to be updated
            if (empty($model->stripe_coupon_id) ||
                ($post['discount_type'] == 'amount' && $oldDiscountAmount != $model->discount_amount) ||
                ($post['discount_type'] == 'percent' && $oldDiscountPercent != $model->discount_percentage)) {
                
                // Check if the old coupon was used
                $couponUsedCount = UserSubscription::where('stripe_coupon_id', $model->stripe_coupon_id)
                    ->where('status', '!=', 'deleted')
                    ->count();

                // Delete old coupon if unused
                if (!empty($model->stripe_coupon_id) && $couponUsedCount == 0) {
                    StripePayment::deleteCouponCode($model->stripe_coupon_id);
                }

                // Create new promo code in Stripe
                $promoCode = StripePayment::createPromoCode([
                    'percent_off' => $post['discount_type'] == 'percent' ? (!empty($post['discount_percentage']) ? $post['discount_percentage'] : null) : null,
                    'amount_off' => $post['discount_type'] == 'amount' ? (!empty($post['discount_amount']) ? ($post['discount_amount'] * 100) : null) : null,
                    'discount_type' => $post['discount_type'],
                    'duration' => 'once',
                ]);

                // Save new Stripe coupon ID
                $model->stripe_coupon_id = $promoCode->id;
                $model->save();
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
    public static function applyPromoCode($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $todayDate = getTodayDate('Y-m-d');
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $promoCode = PromoCode::with('plans')->where('status','active')->whereRaw('BINARY `code`= ?', [$post['code']]); //->where('expiration_date', '>=', $todayDate)
            $promoCode->whereHas('plans', function ($q) use ($post) {
                $q->where('plan_id', $post['plan_id']);
            });
            $promoCode = $promoCode->first();
            if (! empty($promoCode) && ! empty($promoCode->stripe_coupon_id)) {
                $plan = Plan::where('id', $post['plan_id'])->first();
                if (empty($plan)) {
                    throw new Exception('Invalid promo code.', 1);
                }

             // Get all plans associated with this promo code
                $promoPlans = PromoPlan::where([['promo_code_id', $promoCode->id],['plan_id' , $plan->id]])->pluck('plan_type')->toArray();

                // Check if the selected plan type exists in the promo code's allowed plans
                if (!in_array($post['duration'], $promoPlans)) {
                    throw new Exception('This promo code is not applicable for ' . $post['duration'] . ' plans.', 1);
                }

                $totalUsersAllowed = $promoCode->no_of_users_allowed;
                $planDiscountAmount = $post['duration'] == 'monthly' ? $plan->cost_per_month : $plan->cost_per_year;
                $promoDiscountAmount = $promoCode->discount_type == 'amount' ? $promoCode->discount_amount : 0;
                $coupounUsedCount = UserSubscription::where('stripe_coupon_id', $promoCode->stripe_coupon_id)->where('status', '!=', 'deleted')->count();
                $promocodeExist = StripePayment::getPromoCode($promoCode->stripe_coupon_id);

                if (! empty($promocodeExist)) {
                    if ($promoCode->expiration_date >= $todayDate) {
                        if (($promoCode->discount_type == 'percent' || ($promoCode->discount_type == 'amount' && $promoDiscountAmount < $planDiscountAmount))) {
                            if ($totalUsersAllowed > $coupounUsedCount) {
                                return $promoCode;
                            } else {
                                throw new Exception('Too Many Uses.', 1);
                            }
                        } else {
                            throw new Exception('Invalid promo code.', 1);
                        }
                    } else {
                        throw new Exception('Code Expired.', 1);
                    }
                } else {
                    throw new Exception('Invalid promo code.', 1);
                }
            } else {
                throw new Exception('Invalid promo code.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
