<?php

namespace App\Repositories;

use App\Models\MasterModule;
use App\Models\Menu;
use App\Models\MenuBuilder;
use App\Models\Module;
use App\Models\UserMenuLink;
use DB;
use Config;
use Exception;

class ModuleRepository
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

    // public static function getAllMasterModules($where, $with = [])
    // {
    //     return MasterModule::with($with)->where($where)->get();
    // }

    // /**
    //  * Get use menus
    //  *
    //  * @param  array  $where
    //  * @param  array  $with
    //  * @return Menu
    //  */
    // public static function getUserMenus($where, $with = [])
    // {
    //     return Menu::with($with)->where($where)->orderBy('order', 'ASC')->get();
    // }

    // /**
    //  * Find all
    //  *
    //  * @param  array  $where
    //  * @param  array  $with
    //  * @return  User Role
    //  */
    // public static function findAllModules($request)
    // {
    //     $menuIds = [];
    //     $menus = UserMenuLink::with(['menu', 'menu.media'])->where('user_role_id', $request->user_role_id)->orderBy('order', 'ASC')->get();
    //     foreach ($menus as $key => $menu) {
    //         array_push($menuIds, $menu->menu_id);
    //     }
    //     // print_r($menuIds);
    //     // exit;
    //     $allModules = Menu::with(['media'])->whereNotIn('id', $menuIds)->where('status', '!=', 'deleted')->get();

    //     return ['allModules' => $allModules, 'menus' => $menus];
    // }

    // public static function loadMenus($request)
    // {
    //     try {
    //         $post = $request->all();
    //         $userData = getUser();
    //         $sortBy = 'created_at';
    //         $sortOrder = 'DESC';
    //         $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
    //         $list = Menu::where('status', '!=', 'deleted');

    //         if ($userData->user_type != 'admin') {
    //             $list->where('created_by', $userData->id);
    //         }
    //         //Search from title
    //         if (! empty($post['search'])) {
    //             $list->where('title', 'like', '%'.$post['search'].'%');
    //         }
    //         //Search from status
    //         if (! empty($post['status'])) {
    //             $list->where('status', $post['status']);
    //         }
    //         //Sort by
    //         if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
    //             $sortBy = $post['sort_by'];
    //             $sortOrder = $post['sort_order'];
    //         }
    //         $list = $list->orderBy($sortBy, $sortOrder);
    //         $list = $list->paginate($paginationLimit);

    //         return $list;
    //     } catch (\Exception $ex) {
    //         throw $ex;
    //     }
    // }

    // /**
    //  * Add Record
    //  *
    //  * @param array
    //  * @return mixed
    //  *
    //  * @throws Exception $ex
    //  */
    // public static function save($request)
    // {
    //     try {
    //         $post = $request->all();
    //         $userData = getUser();
    //         $currentDateTime = getTodayDate('Y-m-d H:i:s');
    //         $menus = [];
    //         // echo '<pre>';
    //         // print_r($post);
    //         // exit;
    //         Menu::where('user_role_id', $post['user_role_id'])->delete();
    //         if (! empty($post['menus'])) {
    //             foreach ($post['menus'] as $key => $data) {
    //                 $menus[$key]['name'] = ! empty($data['name']) ? $data['name'] : '';
    //                 $menus[$key]['key'] = ! empty($data['key']) ? $data['key'] : '';
    //                 $menus[$key]['url'] = ! empty($data['url']) ? $data['url'] : '';
    //                 $menus[$key]['master_module_id'] = ! empty($data['master_module_id']) ? $data['master_module_id'] : '';
    //                 $menus[$key]['module_id'] = ! empty($data['module_id']) ? $data['module_id'] : '';
    //                 $menus[$key]['media_id'] = ! empty($data['media_id']) ? $data['media_id'] : '';
    //                 $menus[$key]['is_custom_url'] = $data['menu_type'] == 'custom-link' ? 1 : 0;
    //                 $menus[$key]['user_role_id'] = $post['user_role_id'];
    //                 $menus[$key]['order'] = $key;
    //                 $menus[$key]['created_by'] = $userData->id;
    //                 $menus[$key]['updated_by'] = $userData->id;
    //                 $menus[$key]['created_at'] = $currentDateTime;
    //                 $menus[$key]['updated_at'] = $currentDateTime;
    //             }
    //             Menu::insert($menus);
    //         }

    //         return true;
    //     } catch (\Exception $ex) {
    //         throw $ex;
    //     }
    // }

    // // public static function saveMenu($request)
    // // {
    // //     try {
    // //         $post = $request->all();
    // //         $userData = getUser();
    // //         $currentDateTime = getTodayDate('Y-m-d H:i:s');

    // //         $menuKey = str_replace(' ', '-', strtolower($post['name']));
    // //         $model = new Menu();
    // //         $model->name = $post['name'];
    // //         $model->key = $menuKey;
    // //         $model->url = $post['url'];
    // //         $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : null;
    // //         $model->is_custom_url = 1;
    // //         $model->created_by = $userData->id;
    // //         $model->updated_by = $userData->id;
    // //         $model->created_at = $currentDateTime;
    // //         $model->updated_at = $currentDateTime;
    // //         $model->save();

    // //         return true;
    // //     } catch (\Exception $ex) {
    // //         throw $ex;
    // //     }
    // // }

     /**
     * Add Module
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveModule($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            if($request->is_parent_menu==0){
                $checkUrl = Module::where("key", $request->key)->first();
                if(!empty($checkUrl)){
                    throw new Exception("URL key already exists.");
                    return true;
                }
            }

            $childModel = new Module();
            $childModel->name = $request->name;
            if($request->is_parent_menu==0){
                $childModel->parent_id = !empty($request->parent_id) ? $request->parent_id : 0;
                $childModel->key = !empty($request->key) ? $request->key : null;
                $childModel->url = !empty($request->url) ? $request->url : null;
            }
            $childModel->show_as_parent = !empty($request->show_as_parent) ? $request->show_as_parent : 0;
            $childModel->type = ($request->is_parent_menu==1) ? "parent" : "child";
            $childModel->media_id = !empty($request->media_id) ? $request->media_id : null;
            $childModel->created_by = $userData->id;
            $childModel->updated_by = $userData->id;
            $childModel->created_at = $currentDateTime;
            $childModel->updated_at = $currentDateTime;
            $childModel->save();

            return $childModel;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // /**
    //  * Update menu order
    //  *
    //  * @param array
    //  * @return mixed
    //  *
    //  * @throws Exception $ex
    //  */
    // public static function updateMenuOrder($request)
    // {
    //     try {
    //         $post = $request->all();
    //         $userData = getUser();
    //         $currentDateTime = getTodayDate('Y-m-d H:i:s');
    //         $orders = !empty($post['order']) ? $post['order'] : [];
    //         foreach ($orders as $parentIndex => $order) {
    //             $parentMenu = MenuBuilder::where("id", $order["id"])->first();
    //             if(!empty($parentMenu)){
    //                 $parentMenu->sort_order = $parentIndex;
    //                 $parentMenu->save();
    //                 if(!empty($order['children'])){
    //                     foreach ($order['children'] as $childIndex => $child) {
    //                         $childMenu = MenuBuilder::where("id", $child["id"])->first();
    //                         if(!empty($childMenu)){
    //                             $childMenu->sort_order = $childIndex;
    //                             $childMenu->save();
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         return true;
    //     } catch (\Exception $ex) {
    //         throw $ex;
    //     }
    // }

    public static function loadModuleList($request) {
        try{
            $userRoleId = !empty($request->user_role_id) ? $request->user_role_id : 0;
            $results = Module::with(['media'])->where("status", "!=", "deleted");
            if(!empty($request->menu_type)){
               $results->where("type", $request->menu_type);
            }
            $results = $results->get();
            return $results;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function getAllModulesForPermission() {
        try{
            $results = Module::with(['childs'])->where("menu_type", "parent")->where("status", "!=", "deleted");
            $results = $results->get();
            return $results;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
