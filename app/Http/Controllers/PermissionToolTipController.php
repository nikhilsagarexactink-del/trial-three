<?php

namespace App\Http\Controllers;
use App\Repositories\PermissionToolTipRepository;
use App\Http\Requests\PermissionToolTipRequest;
use Illuminate\Http\Request;
use View;
use Config;

class PermissionToolTipController extends Controller
{
    /**
     * Show the permission tool tip index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
                return view('permission-tool-tips.index');

        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }


        /**
     * Show the permission tool tip add form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request)
    {
        try {
              $modules = PermissionToolTipRepository::findAllParentChildModules($request);
              return view('permission-tool-tips.add' , compact('modules'));

        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }


    public function editForm(Request $request)
    {
        try {
            //  Eager load parentModule and childModule
            $modules = PermissionToolTipRepository::findAllParentChildModules($request);
            $result  = PermissionToolTipRepository::findOne(['id' => $request->id], ['module']);
            if (!empty($result)) {
                return view('permission-tool-tips.edit', compact('result' , 'modules'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            \Log::error('Edit Tooltip Error: '.$ex->getMessage());
            abort(404);
        }
    }


        /**
     * Add Permission tool tip
     *
     * @return \Illuminate\Http\Response
     */
    public function save(PermissionToolTipRequest $request)
    {
        try {
            $result = PermissionToolTipRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Permission tool tip  successfully created.',
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
     * Update permission tool tip 
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionToolTipRequest $request)
    {
        try {
            $result = PermissionToolTipRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Permission tool tip successfully updated.',
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
    public function loadList(Request $request)
    {
        try {
            $result = PermissionToolTipRepository::loadList($request);
            $view = View::make('permission-tool-tips._list', ['data' => $result])->render();
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





    /**
     * Change Status
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = PermissionToolTipRepository::changeStatus($request);

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

}