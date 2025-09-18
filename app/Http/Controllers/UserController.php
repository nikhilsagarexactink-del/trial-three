<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\ParentAccountRequest;
use App\Repositories\UserRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserRoleRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class UserController extends BaseController
{
    /**
     * Show the user index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = GroupRepository::findAll([['status', '!=', 'deleted']]);
        return view('user.index', ['groups' => $groups]);
    }

    /**
     * Add form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request)
    {
        try {
            $result = UserRoleRepository::loadRoleList($request);

            return view('user.add', ['result' => $result]);
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add User
     *
     * @return \Illuminate\Http\Response
     */
    public function save(UserRequest $request)
    {
        try {
            $post = $request->all();
            $result = UserRepository::save($post);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'User successfully saved.',
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
            $result = UserRepository::loadList($request);
            $view = View::make('user._list', ['data' => $result])->render();
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
     * Show edit users form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = UserRepository::findOne(['id' => $request->id], ['groupUsers.group']);
            $groups = GroupRepository::findAll([['status','=','active']]);
            $data = UserRoleRepository::loadRoleList($request);
            if (! empty($result)) {
                return view('user.edit', compact('result','data','groups'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update Plan
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $result = UserRepository::updateUser($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'User successfully updated.',
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
            $result = UserRepository::changeStatus($request);

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
     * Display a parent athelete users.
     *
     * @return Response
     */
    public function hasParentAthlete()
    {
        try {
            $result = UserRepository::hasParentAthlete();

            return response()->json(
                [
                    'success' => true,
                    'data' =>  $result,
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
     * Request Parent Account
     *
     * @param ParentAccountRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestParentAccount(ParentAccountRequest $request) {
        return $this->handleApiResponse(function () use ($request) {
            return UserRepository::requestParentAccount($request);
        }, 'Parent account successfully requested.');
    }

    public function changeAthleteParentRequest(Request $request)
    {
        try {
            $athleteRequest = UserRepository::saveParentAsUser($request);
            $verify_token = $request->verify_token;
            if ($athleteRequest && !empty($verify_token)) {
                return view('manage-parent-account.parent-first-page');
            } else {
                throw new \Exception('Link expired.', 1);
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
