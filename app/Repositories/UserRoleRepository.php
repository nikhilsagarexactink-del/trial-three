<?php

namespace App\Repositories;

use App\Models\Module;
use App\Models\UserRole;
use App\Models\UserRoleModulePermission;
use Config;
use DB;

class UserRoleRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findUserRole($where, $with = [])
    {
        return UserRole::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findAllUserRole($where, $with = [])
    {
        return UserRole::with($with)->where($where)->get();
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

    public static function saveRole($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new UserRole();
            $model->name = ucfirst($post['name']);
            $model->user_type = str_replace(' ', '_', strtolower($post['name']));
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            DB::commit();

            return $model;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function changeStatus($request)
    {
        try {
            $model = UserRole::where(['id' => $request->id])->first();
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

    public static function saveMoulePermission($request)
    {
        try {
            $post = $request->all();
            echo '<pre>';
            print_r($post);
            exit;
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $userData = getUser();
            $permissions = [];
            $userRole = UserRole::where(['id' => $request->id])->first();
            if (! empty($userRole)) {
                UserRoleModulePermission::where('user_role_id', $userRole->id)->delete();
                if (! empty($request->moduleIds) && count($request->moduleIds) > 0) {
                    foreach ($request->moduleIds as $key => $id) {
                        $permissions[$key]['module_id'] = $id;
                        $permissions[$key]['user_role_id'] = $request->id;
                        $permissions[$key]['created_by'] = $userData->id;
                        $permissions[$key]['updated_by'] = $userData->id;
                        $permissions[$key]['created_at'] = $currentDateTime;
                        $permissions[$key]['updated_at'] = $currentDateTime;
                    }
                    UserRoleModulePermission::insert($permissions);
                }

                return true;
            } else {
                throw new Exception('User type not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
