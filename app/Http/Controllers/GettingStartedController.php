<?php

namespace App\Http\Controllers;

use App\Http\Requests\GettingStartedRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\GettingStartedRepository;
use App\Repositories\SettingRepository;
use Config;
use Illuminate\Http\Request;
use View;

class GettingStartedController extends Controller
{
    /**
     * Show the Getting Started index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $settings = SettingRepository::getSettings();

            return view('getting-started.getting-started.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        $categories = CategoryRepository::findAll([['status', '!=', 'deleted'], ['type', 'getting-started']]);
        try {
            return view('getting-started.getting-started.add', compact('categories'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit getting started form.
     *
     * @return Redirect to edit form
     */
    public function editForm(Request $request)
    {
        try {
            $result = GettingStartedRepository::findOne(['id' => $request->id], ['media']);
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted']]);
            if (! empty($result)) {
                return view('getting-started.getting-started.edit', compact('result', 'categories'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save Getting Started
     *
     * @return Json
     */
    public function save(GettingStartedRequest $request)
    {
        try {
            $result = GettingStartedRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Getting Started successfully created.',
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
     * Update Getting Started
     *
     * @return Json
     */
    public function update(GettingStartedRequest $request)
    {
        try {
            $result = GettingStartedRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Getting Started successfully updated.',
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
     * Display getting started listing
     *
     * @return Json,Html
     */
    public function loadList(Request $request)
    {
        try {
            $result = GettingStartedRepository::loadList($request);
            $view = View::make('getting-started.getting-started._list', ['data' => $result])->render();
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
     * @return Json
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = GettingStartedRepository::changeStatus($request);

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
     * Show the Getting Started index.
     *
     * @return Redirect to getting started index page
     */
    public function userGettingStartedIndex()
    {
        try {
            $isCompleteVideos = GettingStartedRepository::isCompleteVideo();
            $categories = CategoryRepository::findAll([['status', 'active'], ['type', 'getting-started']],['gettingStartedVideos']);
            return view('user-getting-started.index', compact('categories','isCompleteVideos'));
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Display a listing of getting started user list
     *
     * @return Json,Html
     */
    public function loadListForUser(Request $request)
    {
        try {
            $result = GettingStartedRepository::loadListForUser($request);
            $view = View::make('user-getting-started._list', ['data' => $result])->render();
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
     * Show the Getting Started detail page.
     *
     * @return Redirect to getting started detail page
     */
    public function userGettingStartedDetail(Request $request)
    {
        try {
            $video = GettingStartedRepository::getDetail($request);
            if (! empty($video)) {
                return view('user-getting-started.detail', compact('video'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
            // return response()->json(
            //     [
            //         'success' => false,
            //         'data' => '',
            //         'message' => $ex->getMessage(),
            //     ],
            //     Config::get('constants.HttpStatus.BAD_REQUEST')
            // );
        }
    }

    /**
     * Update getting started order
     *
     * @return \Illuminate\Http\Response
     */
    public function updateGettingStartedOrder(Request $request)
    {
        try {
            $result = GettingStartedRepository::updateGettingStartedOrder($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Getting started order successfully updated.',
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
     * Update getting started mark is complete
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsCompleteGettingStarted(Request $request)
    {
        try {
            $result = GettingStartedRepository::markAsCompleteGettingStarted($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Mark successfully updated.',
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
