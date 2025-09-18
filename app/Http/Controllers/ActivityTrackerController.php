<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityTrackerRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use View;

class ActivityTrackerController extends BaseController
{
    /**
     * Show the Activity Tracker index.
     *
     * @return Html
     */
    public function index()
    {
        try {
            return view('activity-tracker.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the Activity Tracker user index page.
     *
     * @return Html
     */
    public function userIndex()
    {
        try {
            return view('activity-tracker.user-index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the Activity Tracker user permission index page.
     *
     * @return Html
     */
    public function permissionIndex(Request $request)
    {
        try {
            $userData = getUser();
            if ($userData->user_type == 'admin') {
                $athletes = UserRepository::findAll([['user_type', 'athlete'], ['status', 'active']]);
            } else {
                $athletes = UserRepository::findAll([['user_type', 'athlete'], ['status', 'active'], ['parent_id', $userData->id]]);
            }

            return view('activity-tracker.permission.index', compact('athletes'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save user permission
     *
     * @return Json
     */
    public function saveUserPermission(Request $request)
    {
        return $this->handleApiResponse(function () use ($request) {
            return ActivityTrackerRepository::saveUserPermission($request);
        }, 'Log successfully saved.');
    }

    /**
     * Delete activity log
     *
     * @return Json
     */
    public function deleteUserPermission(Request $request)
    {
        return $this->handleApiResponse(function () use ($request) {
            return ActivityTrackerRepository::deleteUserPermission($request);
        }, 'Permission successfully removed.');
    }

    /**
     * Show the Activity user list
     *
     * @return Json,Html
     */
    public function loadUserList(Request $request)
    {
        try {
            $results = ActivityTrackerRepository::loadUserList($request);
            $view = View::make('activity-tracker._user_list', ['data' => $results])->render();
            $pagination = getPaginationLink($results);
            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'data' => $results,
                        'html' => $view,
                        'pagination' => $pagination,
                    ],
                    'message' => 'Activity list',
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
     * Get the Activity list.
     *
     * @return Json, Html
     */
    public function loadActivityList(Request $request)
    {
        try {
            $results = ActivityTrackerRepository::loadActivityList($request);

            $view = View::make('activity-tracker._list', ['results' => $results])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'data' => $results,
                        'html' => $view,
                    ],
                    'message' => 'Activity list',
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
     * Delete activity log
     *
     * @return Json
     */
    public function deleteActivity(Request $request)
    {
        return $this->handleApiResponse(function () use ($request) {
            return ActivityTrackerRepository::delete($request);
        }, 'Log successfully deleted.');
    }

    /**
     * Get user permission list
     *
     * @return Json,Html
     */
    public function loadUserPermissionList(Request $request)
    {
        try {
            $results = ActivityTrackerRepository::loadUserPermissionList($request);
            $view = View::make('activity-tracker.permission._list', ['data' => $results])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'data' => $results,
                        'html' => $view,
                    ],
                    'message' => 'Activity list',
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
