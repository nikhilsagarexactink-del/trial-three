<?php

namespace App\Http\Controllers;

use App\Repositories\UserRoleRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class UserRoleController extends Controller
{
    /**
     * Show the plan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user-role.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadRoleList(Request $request)
    {
        try {
            $result = UserRoleRepository::loadRoleList($request);
            $view = View::make('user-role._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination],
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

    public function addForm()
    {
        try {
            return view('user-role.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function saveRole(Request $request)
    {
        try {
            $result = UserRoleRepository::saveRole($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Role added successfully',
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadModulesList(Request $request)
    {
        try {
            $results = UserRoleRepository::loadModulesList($request);
            $view = View::make('user-role._module', ['data' => $results])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data' => $results, 'html' => $view],
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
     * Change Status
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = UserRoleRepository::changeStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
