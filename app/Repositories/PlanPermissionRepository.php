<?php

namespace App\Repositories;

use App\Models\MasterModule;
use App\Models\Module;
use App\Models\Plan;
use App\Models\UserModulePermission;
use App\Models\UserPlanPermission;
use App\Models\UserRole;
use Config;
use Exception;

class PlanPermissionRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findUserModulePermission($where, $with = [])
    {
        return UserModulePermission::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findAllUserModulePermission($where, $with = [])
    {
        return UserModulePermission::with($with)->where($where)->get();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findAllUserPlanPermission($where, $with = [])
    {
        return UserPlanPermission::with($with)->where($where)->get();
    }

    /**
     * Load user role list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadRoleList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $userId = $userData->id;
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserRole::where('status', '!=', 'deleted');

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
     * Load module list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadModulesList($request)
    {
        try {
            $userData = getUser();
            $userRole = self::findUserRole(['id' => $request->id]);
            $userModules = UserRoleModulePermission::where('user_role_id', $request->id)->get();
            $list = Module::where('status', '!=', 'deleted')->get();
            foreach ($list as $key => $module) {
                $list[$key]['permission'] = 'no';
                if ($userRole->user_type == 'admin') {
                    $list[$key]['permission'] = 'yes';
                } else {
                    $userModulesArr = $userModules->toArray();
                    $isPermission = array_filter($userModulesArr, function ($item) use ($module, $userRole) {
                        if ($item['module_id'] === $module->id && $item['user_role_id'] === $userRole->id) {
                            return true;
                        }

                        return false;
                    });
                    if (count($isPermission) > 0) {
                        $list[$key]['permission'] = 'yes';
                    }
                }
            }

            return ['modules' => $list, 'user_type' => $userRole->user_type];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function getAllModules()
    {
        $modules = MasterModule::with(['childs.toolTip' , 'toolTip'])->where('is_for_plan_permission', 1)->where('status', 'active')->get();
        $userRoles = UserRole::where('status', 'active')->orderBy('user_type', 'ASC')->get();
        $plans = Plan::where([['visibility' , 'active'],['status', 'active']])->get();
        // echo '<pre>';
        // print_r($modules->toArray());
        // exit;

        return ['modules' => $modules, 'userRoles' => $userRoles, 'plans' => $plans];
    }

    public static function getAllModulesForPermission() {
        try{
            $modules = Module::with(['childs.toolTip','toolTip'])->where("is_custom_parent_category", 0)->where("type", "parent")->where("status", "!=", "deleted")->get();
            $userRoles = UserRole::where('status', 'active')->orderBy('user_type', 'ASC')->get();
            $plans = Plan::where([['visibility' , 'active'],['status', 'active']])->get();
            return ['modules' => $modules, 'userRoles' => $userRoles, 'plans' => $plans];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function savePermission($request)
    {
        try {
            //$post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            if ($request->type == 'role') {
                $userRole = UserRole::where(['id' => $request->id])->first();
                if (! empty($userRole)) {
                    if ($request->checked == 'no') {
                        UserModulePermission::where('module_id', $request->module_id)
                                            ->where('user_role_id', $request->id)->delete();
                    } elseif ($request->checked == 'yes') {
                        $userModule = new UserModulePermission();
                        $userModule->module_id = $request->module_id;
                        $userModule->user_role_id = $request->id;
                        $userModule->created_by = $userData->id;
                        $userModule->updated_by = $userData->id;
                        $userModule->created_at = $currentDateTime;
                        $userModule->updated_at = $currentDateTime;
                        $userModule->save();
                    }

                    return true;
                } else {
                    throw new Exception('User role not found.', 1);
                }
            } elseif ($request->type == 'plan') {
                $plan = Plan::where(['id' => $request->id])->first();
                if (! empty($plan)) {
                    if ($request->checked == 'no') {
                        UserPlanPermission::where('module_id', $request->module_id)
                                            ->where('plan_id', $request->id)->delete();
                    } elseif ($request->checked == 'yes') {
                        $userPlan = new UserPlanPermission();
                        $userPlan->module_id = $request->module_id;
                        $userPlan->plan_id = $request->id;
                        $userPlan->created_by = $userData->id;
                        $userPlan->updated_by = $userData->id;
                        $userPlan->created_at = $currentDateTime;
                        $userPlan->updated_at = $currentDateTime;
                        $userPlan->save();
                    }

                    return true;
                } else {
                    throw new Exception('Plan not found.', 1);
                }
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}