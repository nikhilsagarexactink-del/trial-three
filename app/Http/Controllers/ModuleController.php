<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Requests\ModuleFormRequest;
use App\Repositories\MenuLinkBuilderRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\UserRoleRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class ModuleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadModuleList(Request $request)
    {
        try {
            $modules = ModuleRepository::loadModuleList($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $modules,
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

    // /**
    //  * Save Menu
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function saveMenu(ModuleFormRequest $request)
    // {
    //     try {
    //         $result = MenuLinkBuilderRepository::save($request);

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
     * Save Module
     *
     * @return \Illuminate\Http\Response
     */
    public function saveModule(ModuleFormRequest $request)
    {
        try {
            $result = ModuleRepository::saveModule($request);

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


    // /**
    //  * Save Menu
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function saveMenuItem(MenuRequest $request)
    // {
    //     try {
    //         $result = MenuLinkBuilderRepository::saveMenuItem($request);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $result,
    //                 'message' => 'Menu successfully added.',
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

    //  /**
    //  * Save Menu
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function updateMenuOrder(Request $request)
    // {
    //     try {
    //         $result = MenuLinkBuilderRepository::updateMenuOrder($request);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $result,
    //                 'message' => 'Menu order successfully updated.',
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

    //  /**
    //  * Load Menu list
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function loadMenuItems(Request $request)
    // {
    //     try {
    //         $result = MenuLinkBuilderRepository::loadMenuItems($request);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $result,
    //                 'message' => 'Menu list.',
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
}
