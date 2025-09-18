<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Repositories\MenuLinkBuilderRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\ModuleRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class MenuLinkController extends Controller
{
    /**
     * Show theage range index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userRoles = UserRoleRepository::findAllUserRole([]);
            //$modules = MenuLinkBuilderRepository::getAllMasterModules([]);
            $modules = ModuleRepository::getAllModules(["type"=>"parent", "is_custom_parent_category"=>0],["childs"]);
            $parentCategories = ModuleRepository::getAllModules(["type"=>"parent"],[]);
            // echo '<pre>';
            // print_r($modules->toArray());
            // exit;

            return view('menu-link-builder.index', compact('userRoles', 'modules', 'parentCategories'));
        } catch (\Exception $ex) {
            print_r($ex->getMessage());die;
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadList(Request $request)
    {
        try {
            $menus = MenuLinkBuilderRepository::getUserMenus([['user_role_id', '=', $request->user_role_id]], ['media']);
            // echo '<pre>';
            // print_r($menus->toArray());
            // exit;

            return response()->json(
                [
                    'success' => true,
                    'data' => ['menus' => $menus],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Save Menu
     *
     * @return \Illuminate\Http\Response
     */
    public function saveMenu(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Menu successfully saved.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Save Menu
     *
     * @return \Illuminate\Http\Response
     */
    // public function saveMenuItem(MenuRequest $request)
    // {
    //     try {
    //         $result = MenuLinkBuilderRepository::saveMenu($request);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => [],
    //                 'message' => 'Menu successfully saved.',
    //             ],
    //             Config::get('constants.HttpStatus.OK')
    //         );
    //     } catch (\Exception $ex) {
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'data' => '',
    //                 'message' => $ex->getMessage(),
    //             ],
    //             Config::get('constants.HttpStatus.BAD_REQUEST')
    //         );
    //     }
    // }


    /**
     * Save Menu
     *
     * @return \Illuminate\Http\Response
     */
    public function saveMenuItem(MenuRequest $request)
    {
        try {
            $result = MenuLinkBuilderRepository::saveMenuItem($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu successfully added.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /**
     * Update Menu Order
     *
     * @return \Illuminate\Http\Response
     */
    public function updateMenuOrder(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::updateMenuOrder($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu order successfully updated.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /**
     * Load Menu list
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMenuItems(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::loadMenuItems($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu list.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /**
     * Delete Menu Item
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteMenuItem(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::deleteMenuItem($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu successfully deleted.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /**
     * Load Side Menu List
     *
     * @return \Illuminate\Http\Response
     */
    public function loadSideMenus(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::loadSideMenus($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu list.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /**
     * Update Menu Item
     *
     * @return \Illuminate\Http\Response
     */
    public function updateMenuItem(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::updateMenuItem($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu successfully deleted.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /**
     * Add Menu Custom Parent Category
     *
     * @return \Illuminate\Http\Response
     */
    public function addCustomParentCategory(Request $request)
    {
        try {
            $result = MenuLinkBuilderRepository::addCustomParentCategory($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Menu parent category successfully added.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
