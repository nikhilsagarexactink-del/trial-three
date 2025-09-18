<?php

namespace App\Repositories;

use App\Models\MasterModule;
use App\Models\PermissionToolTip;
use App\Models\Menu;
use App\Models\Module;
use App\Models\UserMenuLink;
use Config;
use Exception;

class PermissionToolTipRepository
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
        return PermissionToolTip::with($with)->where($where)->first();
    }


    /**
     * Load all parent child module
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findAllParentChildModules($request)
    {
        try {
            $post =$request->all();
            $all_modules = [];

            $menus = Module::with('childs')
                ->where([['type','parent'],['status', '!=', 'deleted']])
                ->orderBy('created_at', 'ASC')
                ->get();
            foreach ($menus as $parent) {
                // Push parent module
                $addParentModule = false;
                if($parent->childs->count() > 1 ) {
                    foreach ($parent->childs as $child) {
                        if ((int) $child->show_as_parent === 0) {   // found a “true” child
                            $addParentModule = true;
                            break;                                  // no need to keep looping
                        }
                    }
                }
                if($addParentModule) {
                    $all_modules[] = [
                        'id' => $parent->id,
                        'name' => $parent->name,
                        'status' => $parent->status,
                        'is_parent_module' => 1,
                        'type' => $parent->type,
                    ];
                }


                // Check if it has children
                if ($parent->childs && $parent->childs->count() > 0) {

                    foreach ($parent->childs as $child) {

                        $all_modules[] = [
                            'id' => $child->id,
                            'name' => $child->name,
                            'status' => $child->status,
                            'is_parent_module' => 0,
                            'show_as_parent' => $child->show_as_parent,
                            'type' => $child->type,
                        ];
                    }
                }
            }

            return $all_modules;

        } catch (\Exception $ex) {
                throw $ex;
            }
    }
    // public static function findAllParentChildModules($request)
    // {
    //     try {
    //         $post =$request->all();
    //         $all_modules = [];

    //         $menus = MasterModule::with('childs')
    //             ->where([['status', '!=', 'deleted']])
    //             ->orderBy('created_at', 'ASC')
    //             ->get();

    //         foreach ($menus as $parent) {
    //             // Push parent module
    //             $all_modules[] = [
    //                 'id' => $parent->id,
    //                 'name' => $parent->name,
    //                 'status' => $parent->status,
    //                 'is_parent_module' => 1,
    //                 'sub_menus' => $parent->sub_menus,
    //             ];

    //             // Check if it has children
    //             if ($parent->childs && $parent->childs->count() > 0) {
    //                 foreach ($parent->childs as $child) {
    //                     $all_modules[] = [
    //                         'id' => $child->id,
    //                         'name' => $child->name,
    //                         'status' => $child->status,
    //                         'is_parent_module' => 0,
    //                     ];
    //                 }
    //             }
    //         }

    //         return $all_modules;

    //     } catch (\Exception $ex) {
    //             throw $ex;
    //         }
    // }
    /**
     * Add permission tool tip
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function save($request)
    {
        $post = $request->all();
        $userData = getUser();
        $isParent = (int) $post['is_parent_module'];
        $moduleId = $post['module_id'];
        $module = Module::where('id',$moduleId)->first();
        $currentDateTime = getTodayDate('Y-m-d H:i:s');

        $query = PermissionToolTip::where('is_parent_module', $isParent)
            ->where('status', '!=', 'deleted')
            ->where('module_id' , $moduleId);

        if ($query->exists()) {
            throw new \Exception('Permission tool tip already exists for this module.', 1);
        }

        $model = new PermissionToolTip([
            'tool_tip_text'     => $post['tool_tip_text'],
            'is_parent_module'  => $isParent,
            'module_id'         => $moduleId,
            'type'              => $module->type,
            'show_as_parent'    => $module->show_as_parent,
            'status'            => 'active',
            'created_by'        => $userData->id,
            'updated_by'        => $userData->id,
            'created_at'        => $currentDateTime,
            'updated_at'        => $currentDateTime,
        ]);
        // $model = new PermissionToolTip([
        //     'tool_tip_text'     => $post['tool_tip_text'],
        //     'is_parent_module'  => $isParent,
        //     'parent_module_id'  => $isParent ? $moduleId : null,
        //     'child_module_id'   => $isParent ? null : $moduleId,
        //     'status'            => 'active',
        //     'created_by'        => $userData->id,
        //     'updated_by'        => $userData->id,
        //     'created_at'        => $currentDateTime,
        //     'updated_at'        => $currentDateTime,
        // ]);

        $model->save();

        return true;
    }

    /**
     * Update permission tool tip
     *
     * @param array $request
     * @return mixed
     *
     * @throws Exception
     */
    public static function update($request)
    {
        $post = $request->all();
        $userData = getUser();
        $isParent = (int) $post['is_parent_module'];
        $moduleId = $post['module_id'];
        $module = Module::where('id',$moduleId)->first();

        $model = PermissionToolTip::find($post['id']);
        if (!$model) {
            throw new \Exception('Permission tool tip not found.');
        }

        // Check for duplicate (excluding current record)
        $duplicate = PermissionToolTip::where('is_parent_module', $isParent)
            ->where('status', '!=', 'deleted')
            ->where('id', '!=', $post['id'])
            ->where( 'module_id', $moduleId)
            ->first();

        if ($duplicate) {
            throw new \Exception('Permission tool tip already exists for this module.');
        }

        // Update fields
        $model->fill([
            'tool_tip_text'     => $post['tool_tip_text'],
            'is_parent_module'  => $isParent,
            'module_id'         => $moduleId,
            'type'              => $module->type,
            'show_as_parent'    => $module->show_as_parent,
            'updated_by'        => $userData->id,
            'updated_at'        => now(),
        ]);
        // $model->fill([
        //     'tool_tip_text'     => $post['tool_tip_text'],
        //     'is_parent_module'  => $isParent,
        //     'parent_module_id'  => $isParent ? $moduleId : null,
        //     'child_module_id'   => $isParent ? null : $moduleId,
        //     'updated_by'        => $userData->id,
        //     'updated_at'        => now(),
        // ]);

        $model->save();

        return true;
    }

    /**
     * Load record list 
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
                $sortBy = 'created_at';
                $sortOrder = 'DESC';
                $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');

                $list = PermissionToolTip::with('module')
                    ->where('status', '!=', 'deleted');

                // 🔍 Search tooltip text OR parent/child module name
                if (!empty($post['search'])) {
                    $list->where(function ($query) use ($post) {
                        $query->where('tool_tip_text', 'like', '%' . $post['search'] . '%')
                            ->whereHas('module', function ($q) use ($post) {
                                $q->where('name', 'like', '%' . $post['search'] . '%');
                            });
                    });
                }

                // ✅ Filter by status
                if (!empty($post['status'])) {
                    $list->where('status', $post['status']);
                }

                // 📅 Filter by created_at date
                if (!empty($post['date'])) {
                    $list->whereDate('created_at', $post['date']);
                }

                // 🔃 Sorting
                if (!empty($post['sort_by']) && !empty($post['sort_order'])) {
                    $sortBy = $post['sort_by'];
                    $sortOrder = $post['sort_order'];
                }

                $list = $list->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);
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
        $model = PermissionToolTip::find($request->id);
        if (!$model) {
            throw new \Exception('Record not found.', 1);
        }
        $model->status = $request->status;
        $model->save();
        return true;
    }
}