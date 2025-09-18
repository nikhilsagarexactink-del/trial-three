<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use View;

class GroupController extends Controller
{
    /**
     * Show the Journal index.
     *
     * @return Redirect to group index page
     */
    public function index()
    {
        try {
            return view('group.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add group form.
     *
     * @return Redirect to group add form
     */
    public function addForm()
    {
        try {
            $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);
            return view('group.add', compact('athletes'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save group;
     *
     * @return Json
     */
    public function saveGroup(GroupRequest $request)
    {
        try {
            $result = GroupRepository::saveGroup($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Group created successfully.',
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
     * Load group;
     *
     * @return Json,Html
     */
    public function loadGroupList(Request $request)
    {
        try {
            $userType = userType();
            $result = GroupRepository::loadGroupList($request);
            $view = View::make('group._list', ['data' => $result, 'userType' => $userType])->render();
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
     * Show edit skill level form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $athletes = UserRepository::findAll([['user_type', '=', 'athlete'], ['status', '!=', 'deleted']]);
            $result = GroupRepository::findOne(['id' => $request->id] , ['media']);
            if (! empty($result)) {
                return view('group.edit', compact('result' , 'athletes'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    /**
     * Update Journal
     *
     * @return \Illuminate\Http\Response
     */
    public function updateGroup(GroupRequest $request)
    {
        try {
            $result = GroupRepository::updateGroup($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Group successfully updated.',
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
            $result = GroupRepository::changeStatus($request);

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
     * Load group;
     *
     * @return Json,Html
     */
    public function loadGroupWithCode(Request $request)
    {
        try {
            $result = GroupRepository::loadGroupWithCode($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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

    public function viewGroup() {
            try {
            return view('group.view-group.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }


        /**
     * Load group;
     *
     * @return Json,Html
     */
    public function viewGroupList(Request $request)
    {
        try {
            $result = GroupRepository::loadGroupList($request);
            $view = View::make('group.view-group._list', ['data' => $result])->render();
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

}
