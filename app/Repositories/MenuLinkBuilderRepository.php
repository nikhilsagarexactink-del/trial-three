<?php

namespace App\Repositories;

use App\Models\MasterModule;
use App\Models\Menu;
use App\Models\MenuBuilder;
use App\Models\Module;
use App\Models\UserMenuLink;
use App\Models\UserModulePermission;
use DB;
use Config;
use Exception;

class MenuLinkBuilderRepository
{
    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return Module
     */
    public static function getAllModules($where, $with = [])
    {
        return Module::with($with)->where($where)->get();
    }

    public static function getAllMasterModules($where, $with = [])
    {
        return MasterModule::with($with)->where($where)->get();
    }

    /**
     * Get use menus
     *
     * @param  array  $where
     * @param  array  $with
     * @return Menu
     */
    public static function getUserMenus($where, $with = [])
    {
        return MenuBuilder::with($with)->where($where)->get();
    }
    // public static function getUserMenus($where, $with = [])
    // {
    //     return Menu::with($with)->where($where)->orderBy('order', 'ASC')->get();
    // }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findAllModules($request)
    {
        $menuIds = [];
        $menus = UserMenuLink::with(['menu', 'menu.media'])->where('user_role_id', $request->user_role_id)->orderBy('order', 'ASC')->get();
        foreach ($menus as $key => $menu) {
            array_push($menuIds, $menu->menu_id);
        }
        // print_r($menuIds);
        // exit;
        $allModules = Menu::with(['media'])->whereNotIn('id', $menuIds)->where('status', '!=', 'deleted')->get();

        return ['allModules' => $allModules, 'menus' => $menus];
    }

    public static function loadMenus($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'ASC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            //sort menu
            $list = Menu::where('status', '!=', 'deleted');

            if ($userData->user_type != 'admin') {
                $list->where('created_by', $userData->id);
            }
            //Search from title
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
            $list = $list->orderBy($sortBy, $sortOrder);
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
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $menus = [];
            // echo '<pre>';
            // print_r($post);
            // exit;
            Menu::where('user_role_id', $post['user_role_id'])->delete();
            if (! empty($post['menus'])) {
                foreach ($post['menus'] as $key => $data) {
                    $menus[$key]['name'] = ! empty($data['name']) ? $data['name'] : '';
                    $menus[$key]['key'] = ! empty($data['key']) ? $data['key'] : '';
                    $menus[$key]['url'] = ! empty($data['url']) ? $data['url'] : '';
                    $menus[$key]['master_module_id'] = ! empty($data['master_module_id']) ? $data['master_module_id'] : '';
                    $menus[$key]['module_id'] = ! empty($data['module_id']) ? $data['module_id'] : '';
                    $menus[$key]['media_id'] = ! empty($data['media_id']) ? $data['media_id'] : '';
                    $menus[$key]['is_custom_url'] = $data['menu_type'] == 'custom-link' ? 1 : 0;
                    $menus[$key]['user_role_id'] = $post['user_role_id'];
                    $menus[$key]['order'] = $key;
                    $menus[$key]['created_by'] = $userData->id;
                    $menus[$key]['updated_by'] = $userData->id;
                    $menus[$key]['created_at'] = $currentDateTime;
                    $menus[$key]['updated_at'] = $currentDateTime;
                }
                Menu::insert($menus);
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // public static function saveMenu($request)
    // {
    //     try {
    //         $post = $request->all();
    //         $userData = getUser();
    //         $currentDateTime = getTodayDate('Y-m-d H:i:s');

    //         $menuKey = str_replace(' ', '-', strtolower($post['name']));
    //         $model = new Menu();
    //         $model->name = $post['name'];
    //         $model->key = $menuKey;
    //         $model->url = $post['url'];
    //         $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : null;
    //         $model->is_custom_url = 1;
    //         $model->created_by = $userData->id;
    //         $model->updated_by = $userData->id;
    //         $model->created_at = $currentDateTime;
    //         $model->updated_at = $currentDateTime;
    //         $model->save();

    //         return true;
    //     } catch (\Exception $ex) {
    //         throw $ex;
    //     }
    // }

     /**
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveMenuItem($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $parentId = null;//!empty($request->parent_id) ? $request->parent_id : null;
            $parentModuleId = null;//!empty($request->parent_module_id) ? $request->parent_module_id : null;
            // if(!empty($request->parent)){
            //     $checkParent = MenuBuilder::where('user_role_id', $request->user_role_id)->where('module_id', $request->parent['id'])->first();
            //     if(empty($checkParent)){
            //         $parentModel = new MenuBuilder();
            //         $parentModel->name = $request->parent['name'];
            //         $parentModel->is_parent_menu = 1;
            //         $parentModel->menu_type = "dynamic";
            //         $parentModel->module_id = $request->parent['id'];
            //         $parentModel->user_role_id = !empty($request->user_role_id) ? $request->user_role_id : null;
            //         $parentModel->media_id = !empty($request->parent['media_id']) ? $request->parent['media_id'] : null;
            //         $parentModel->created_by = $userData->id;
            //         $parentModel->updated_by = $userData->id;
            //         $parentModel->created_at = $currentDateTime;
            //         $parentModel->updated_at = $currentDateTime;
            //         $parentModel->save();
            //         $parentId = $parentModel->id;
            //     } else {
            //         $parentId = $checkParent->id;
            //     }
            // }
            //echo $parentId;die;

            if(!empty($request->parent_module_id)){
                $checkParent = MenuBuilder::where('user_role_id', $request->user_role_id)->where('module_id', $request->parent_module_id)->first();
                $parentModule = Module::where('id', $request->parent_module_id)->first();
                if(empty($checkParent)){
                    $parentModel = new MenuBuilder();
                    $parentModel->name = $parentModule->name;
                    $parentModel->is_parent_menu = 1;
                    $parentModel->menu_type = "dynamic";
                    $parentModel->module_id = $request->parent_module_id;
                    $parentModel->user_role_id = !empty($request->user_role_id) ? $request->user_role_id : null;
                    $parentModel->created_by = $userData->id;
                    $parentModel->updated_by = $userData->id;
                    $parentModel->created_at = $currentDateTime;
                    $parentModel->updated_at = $currentDateTime;
                    $parentModel->save();
                    $parentId = $parentModel->id;
                } else {
                    $parentId = $checkParent->id;
                }
            }
            if(!empty($request->module_id)){
                $checkChild = MenuBuilder::where('user_role_id', $request->user_role_id)->where('module_id', $request->module_id)->first();
                if(!empty($checkChild)){
                    //throw new Exception("Menu already exist.");
                    return true;
                }
            }

            $childModel = new MenuBuilder();
            $childModel->name = $request->name;
            $childModel->parent_id = $parentId;
            //$childModel->parent_module_id = $parentModuleId;
            $childModel->user_role_id = !empty($request->user_role_id) ? $request->user_role_id : null;
            $childModel->url = !empty($request->url) ? $request->url : null;
            $childModel->menu_type = !empty($request->menu_type) ? $request->menu_type : null;
            //$childModel->is_parent_menu = !empty($request->is_parent_menu) ? $request->is_parent_menu : 0;
            $childModel->is_parent_menu = !$parentId ? 1 : 0;
            $childModel->show_as_parent = !$parentId ? 1 : 0;
            $childModel->module_id = !empty($request->module_id) ? $request->module_id : null;
            $childModel->media_id = !empty($request->media_id) ? $request->media_id : null;
            $childModel->created_by = $userData->id;
            $childModel->updated_by = $userData->id;
            $childModel->created_at = $currentDateTime;
            $childModel->updated_at = $currentDateTime;
            $childModel->save();
            DB::commit();
            return $childModel;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update menu order
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function updateMenuOrder($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $orders = !empty($post['order']) ? $post['order'] : [];
            foreach ($orders as $parentIndex => $order) {
                $parentMenu = MenuBuilder::where("id", $order["id"])->first();
                if(!empty($parentMenu)){
                    $parentMenu->sort_order = $parentIndex+1;
                    $parentMenu->save();
                    if(!empty($order['children'])){
                        foreach ($order['children'] as $childIndex => $child) {
                            $childMenu = MenuBuilder::where("id", $child["id"])->first();
                            if(!empty($childMenu)){
                                $childMenu->sort_order = $childIndex+1;
                                $childMenu->save();
                            }
                        }
                    }
                }
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function loadMenuItems($request) {
        try{
            $sortBy = 'sort_order';
            $sortOrder = 'ASC';
            $userRoleId = !empty($request->user_role_id) ? $request->user_role_id : 0;
            $results = MenuBuilder::with(['childs'])->with(['media'])->where("status", "!=", "deleted")->where("is_parent_menu", 1);
            if(!empty($userRoleId)){
                $results->where("user_role_id", $userRoleId);
            }
            if(!empty($request->is_parent_menu)){
                $results->where("is_parent_menu", $request->is_parent_menu);
            }
            $results->orderBy($sortBy, $sortOrder);
            $results = $results->get();
            return $results;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete Menu Item
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function deleteMenuItem($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $deletedIds = [];
            if($request->menu_type=="dynamic" && ($request->deleted_from=="grid" || $request->deleted_from=="checkbox")){
                $menu = $request->deleted_from =="grid" ? MenuBuilder::where('id', $request->id)->first() : MenuBuilder::where([['module_id', $request->id] ,['user_role_id', $request->user_role_id]])->first();
                if (! empty($menu)) {
                    array_push($deletedIds, $menu->module_id);
                    if($menu->is_parent_menu==0){
                        $parentMenus = MenuBuilder::with(['childs'])->where('id', $menu->parent_id)->first();
                        if(count($parentMenus->childs) == 0 || count($parentMenus->childs) == 1){
                            array_push($deletedIds, $parentMenus->module_id);
                            MenuBuilder::where('id', $parentMenus->id)->delete();
                        }
                    }
                    $menu->delete();
                    DB::commit();
                    return $deletedIds;
                } else {
                    DB::rollback();
                    throw new Exception("Menu does not exist.");
                }
            } else if($request->menu_type=="custom"){
                $menu = MenuBuilder::where('id', $request->id)->first();
                if (! empty($menu)) {
                    array_push($deletedIds, $menu->id);
                    if($menu->is_parent_menu==1){
                        //MenuBuilder::where('parent_id', $menu->id)->delete();
                        $childMenus = MenuBuilder::where('parent_id', $menu->id)->get();
                        foreach ($childMenus as $key => $childMenu) {
                            array_push($deletedIds, $childMenu->id);
                            $childMenu->delete();
                        }
                    }
                    $menu->delete();
                    DB::commit();
                    return $deletedIds;
                } else {
                    DB::rollback();
                    throw new Exception("Menu does not exist.");
                }
            }

        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
    // => function ($query) use($userRoleId){
    //     $query->where('user_role_id', $userRoleId);
    //     $query->where('status', 'active');
    // }
    public static function loadSideMenus($request) {
        try{
            $sortBy = 'sort_order';
            $sortOrder = 'ASC';
            $userRoleId = 1;//!empty($request->user_role_id) ? $request->user_role_id : 0;
            $menuArr = [];
            $results =  MenuBuilder::with(['childs','childs.module','modulePermission', 'media'])->where("status", "!=", "deleted")->where("is_parent_menu", 1);
            $results->where("user_role_id", $userRoleId);
            //$results->where("menu_type", 'dynamic');//Temp
            $results->orderBy($sortBy, $sortOrder);
            $results = $results->get();
            //Check module permission
            $userModulePermissions = UserModulePermission::where('user_role_id', $userRoleId)->where('status','active')->get();
            $userModulePermissionIds = $userModulePermissions->pluck('module_id')->toArray();
            // echo '<pre>';
            // print_r($results->toArray());die;
            foreach ($results as $key => $menu) {
                $parentMenuArr = $menu->toArray();
                $parentMenuArr['childs'] = [];
                //if($menu->menu_type=='dynamic'){
                    if(count($menu['childs']) > 1){
                        foreach ($menu['childs'] as $childKey => $childMenu) {
                            $showAsParentMenu = $childMenu['show_as_parent'];
                            unset($childMenu['module']);
                            if($childMenu['status']=='active' && $showAsParentMenu==0){
                                //echo $childMenu->module_id;die;
                                //print_r($userModulePermissionIds);die;
                                if ((in_array($childMenu->module_id, $userModulePermissionIds)) || $childMenu->menu_type=='custom') {
                                    array_push($parentMenuArr['childs'], $childMenu->toArray());
                                }
                            } elseif ($childMenu['status']=='active' && $showAsParentMenu==1){
                                array_push($menuArr, $childMenu);
                            }
                        }
                    }
                    if(count($parentMenuArr['childs']) > 1){
                        array_push($menuArr, $parentMenuArr);
                    } else if((count($parentMenuArr['childs']) == 0 && $menu->show_as_parent == 1) ) {
                        unset($menu['module_permission']);
                        array_push($menuArr, $menu);
                    } else if(count($menu['childs']) == 1) {
                        array_push($menuArr, $menu['childs'][0]);
                    }
                //}
                // else if($menu->menu_type=='custom') {
                //     foreach ($menu['childs'] as $childKey => $childMenu) {
                //         array_push($parentMenuArr['childs'], $childMenu->toArray());
                //     }
                //     array_push($menuArr, $parentMenuArr);
                // }
            }
            // echo '<pre>';
            // //print_r($results->toArray());
            // print_r($menuArr);
            // die;
            return $menuArr;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

     /**
     * Update Menu Item
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function updateMenuItem($request)
    {
       try {
            $post = $request->all();
            $userData = getUser();
            $menu = MenuBuilder::where('id', $request->id)->first();
            if (! empty($menu)) {
                $menu->name = $request->name;
                $menu->url = $request->url;
                $menu->media_id = !empty($request->media_id) ? $request->media_id : null;
                $menu->save();
                return $menu;
            } else {
                throw new Exception("Menu does not exist.");
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

     /**
     * Add Menu Custom Parent Category
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function addCustomParentCategory($request)
    {
       try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $menu = new Module();
            $menu->name = $request->name;
            $menu->type = "parent";
            $menu->is_custom_parent_category = 1;
            $menu->created_by = $userData->id;
            $menu->updated_by = $userData->id;
            $menu->created_at = $currentDateTime;
            $menu->updated_at = $currentDateTime;
            $menu->save();
            return $menu;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
