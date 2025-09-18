<?php

namespace App\Http\Controllers;

use App\Repositories\PlanPermissionRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\ModuleRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class PlanPermissionController extends Controller
{
    /**
     * Show the plan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modulePermissions = PlanPermissionRepository::findAllUserModulePermission([]);
        $modulePermissions = $modulePermissions->toArray();
        $planPermissions = PlanPermissionRepository::findAllUserPlanPermission([]);
        $planPermissions = $planPermissions->toArray();
        $data = PlanPermissionRepository::getAllModulesForPermission();
        //$data = ModuleRepository::getAllModulesForPermission([]);
        // echo '<pre>';
        // print_r($data['modules']->toArray());
        // exit;

        return view('plan-permissions.index', compact('data', 'modulePermissions', 'planPermissions'));
    }

    public function savePermission(Request $request)
    {
        try {
            $result = PlanPermissionRepository::savePermission($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Permission successfully updated.',
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
     * Save module permission
     *
     * @return Response
     */
    public function saveMoulePermission(Request $request)
    {
        try {
            $result = UserRoleRepository::saveMoulePermission($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Permission successfully updated.',
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
