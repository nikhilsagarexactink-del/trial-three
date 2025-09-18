<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Services\StripePayment;
use Config;
use DB;
use Exception;
use File;

class PlanRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Plan
     */
    public static function findOne($where, $with = [])
    {
        return Plan::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Plan
     */
    public static function findAll($where, $with = [])
    {
        return Plan::with($with)->where($where)->get();
    }

    /**
     * Count
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Plan
     */
    public static function count($where)
    {
        return Plan::where($where)->count();
    }

    /**
     * Load record list for admin
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
            $list = Plan::where('status', '!=', 'deleted');

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
            $model = Plan::where(['id' => $request->id])->first();
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
            $model = new Plan();
            $model->name = $post['name'];
            $model->key = $post['key'];
            $model->cost_per_month = $post['cost_per_month'];
            $model->cost_per_year = $post['cost_per_year'];
            $model->description = $post['description'];
            $model->visibility = $post['visibility'];
            $model->free_trial_days = $post['free_trial_days'];
            $model->is_free_plan = ! empty($post['is_free_plan']) ? $post['is_free_plan'] : 0;
            $model->is_default_free_plan = isset($post['is_default_free_plan']) ? $post['is_default_free_plan'] : 0;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->status = 'active';
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            //Create product
            $product = StripePayment::createProduct($model);
            $model->stripe_product_id = $product->id;
            //Update default free plan
            if (isset($post['is_default_free_plan'])) {
                $plans = Plan::where('is_default_free_plan', 1)->get();
                foreach ($plans as $plan) {
                    $plan->is_default_free_plan = 0;
                    $plan->save();
                }
            }
            //Create monthly price
            $monthly = [
                'amount' => $model->cost_per_month,
                'interval' => 'month',
                'stripe_product_id' => $model->stripe_product_id,
            ];
            $monthlyPrice = StripePayment::createPrice($monthly);
            $model->stripe_monthly_price_id = $monthlyPrice->id;
            //Create yearly price
            $yearly = [
                'amount' => $model->cost_per_year,
                'interval' => 'year',
                'stripe_product_id' => $model->stripe_product_id,
            ];
            $yearlyPrice = StripePayment::createPrice($yearly);
            $model->stripe_yearly_price_id = $yearlyPrice->id;
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
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $prevPlanName = $model->name;
                $prevMonthlyPrice = $model->cost_per_month;
                $prevYearlyPrice = $model->cost_per_year;
                $model->name = $post['name'];
                $model->key = $post['key'];
                $model->cost_per_month = $post['cost_per_month'];
                $model->cost_per_year = $post['cost_per_year'];
                $model->description = $post['description'];
                $model->visibility = $post['visibility'];
                $model->free_trial_days = $post['free_trial_days'];
                $model->is_free_plan = ! empty($post['is_free_plan']) ? $post['is_free_plan'] : 0;
                $model->is_default_free_plan = isset($post['is_default_free_plan']) ? $post['is_default_free_plan'] : 0;
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                if (empty($model->stripe_product_id)) {
                    //Create product
                    $product = StripePayment::createProduct($model);
                    $model->stripe_product_id = $product->id;
                    //Create monthly price
                    $monthly = [
                        'amount' => $model->cost_per_month,
                        'interval' => 'month',
                        'stripe_product_id' => $model->stripe_product_id,
                    ];
                    $monthlyPrice = StripePayment::createPrice($monthly);
                    $model->stripe_monthly_price_id = $monthlyPrice->id;
                    //Create yearly price
                    $yearly = [
                        'amount' => $model->cost_per_year,
                        'interval' => 'year',
                        'stripe_product_id' => $model->stripe_product_id,
                    ];
                    $yearlyPrice = StripePayment::createPrice($yearly);
                    $model->stripe_yearly_price_id = $yearlyPrice->id;
                } else {
                    //Update stripe product name
                    if (! empty($model->stripe_product_id) && $prevPlanName != $post['name']) {
                        StripePayment::updateProduct($model);
                    }
                    //Update stripe monthly price
                    if (! empty($model->stripe_monthly_price_id) && (float) $prevMonthlyPrice != (float) $post['cost_per_month']) {
                        $monthly = [
                            'amount' => $post['cost_per_month'],
                            'interval' => 'month',
                            'stripe_product_id' => $model->stripe_product_id,
                        ];
                        $monthlyPrice = StripePayment::createPrice($monthly);
                        $model->stripe_monthly_price_id = $monthlyPrice->id;
                    }
                    //Update stripe yearly price
                    if (! empty($model->stripe_yearly_price_id) && (float) $prevYearlyPrice != (float) $post['cost_per_year']) {
                        $yearly = [
                            'amount' => $model->cost_per_year,
                            'interval' => 'year',
                            'stripe_product_id' => $model->stripe_product_id,
                        ];
                        $yearlyPrice = StripePayment::createPrice($yearly);
                        $model->stripe_yearly_price_id = $yearlyPrice->id;
                    }
                }
                //Update default free plan
                if (isset($post['is_default_free_plan'])) {
                    $plans = Plan::where('is_default_free_plan', 1)->get();
                    foreach ($plans as $plan) {
                        $plan->is_default_free_plan = 0;
                        $plan->save();
                    }
                }

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

    public static function getAllMenus()
    {
        try {
            $menus = getSidebarPermissions();
            // $permissions =  getUserModulePermissions();


            return $menus;
        } catch(\Exception $ex) {
            throw $ex;
        }
    }
   public static function getAppMenus()
{
    try {
        // Get sidebar permissions
        $menus = getSidebarPermissions();
        $menuArr = [];

        // Parse the excluded module names from JSON
        $excludedJson = File::get(base_path('public/assets/excluded-app-menus.json'));
        $excludedArray = json_decode($excludedJson, true);
        $excludedKeys = array_column($excludedArray, 'module_key');

        foreach ($menus as $menu) {
            $parentMenuArr = $menu;
            $parentMenuArr['childs'] = [];

            $validChilds = [];

            // Process child menus if more than 1
            if (!empty($menu['childs']) && count($menu['childs']) > 0) {
                foreach ($menu['childs'] as $childMenu) {
                    $moduleKey = $childMenu['module']['key'] ?? null;
                    $showAsParent = $childMenu['show_as_parent'] ?? 0;
                    $status = $childMenu['status'] ?? 'inactive';

                    // Skip if excluded or inactive
                    if ($status !== 'active' || in_array($moduleKey, $excludedKeys)) {
                        continue;
                    }

                    if ($showAsParent == 0) {
                        $validChilds[] = $childMenu;
                    } elseif ($showAsParent == 1) {
                        $menuArr[] = $childMenu;
                    }
                }
            }

            // Handle parent menu inclusion based on filtered children
            if (count($validChilds) > 1) {
                $parentMenuArr['childs'] = $validChilds;
                $menuArr[] = $parentMenuArr;
            } elseif (count($validChilds) == 1) {
                $menuArr[] = $validChilds[0];
            } elseif (count($validChilds) == 0 && $menu['show_as_parent'] == 1 || ($menu['show_as_parent'] == 0 && !empty($menu['parent_id']))) {
                $moduleKey = $menu['module']['key'] ?? null;

                // Only include parent menu if not excluded
                if (!in_array($moduleKey, $excludedKeys)) {
                    unset($menu['module_permission']); // remove permission metadata if required
                    $menuArr[] = $menu;
                }
            }
        }

        return $menuArr;

    } catch (\Exception $ex) {
        throw $ex;
    }
}


}
