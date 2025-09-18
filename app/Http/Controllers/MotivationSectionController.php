<?php

namespace App\Http\Controllers;

use App\Http\Requests\MotivationSectionReqeust;
use App\Repositories\CategoryRepository;
use App\Repositories\MotivationSectionRepository;
use App\Repositories\SettingRepository;
use Config;
use Illuminate\Http\Request;
use View;

class MotivationSectionController extends Controller
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

            return view('motivation-section.motivation-section.index', compact('settings'));
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
        $categories = CategoryRepository::findAll([['status', '!=', 'deleted'], ['type', 'motivation-section']]);
        try {
            return view('motivation-section.motivation-section.add', compact('categories'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit training-video form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = MotivationSectionRepository::findOne(['id' => $request->id], ['media']);
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted'], ['type', 'motivation-section']]);
            if (! empty($result)) {
                return view('motivation-section.motivation-section.edit', compact('result', 'categories'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            //print_r($ex->getMessage());die;
            abort(404);
        }
    }

    /**
     * Add Getting Started
     *
     * @return \Illuminate\Http\Response
     */
    public function save(MotivationSectionReqeust $request)
    {
        try {
            $result = MotivationSectionRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Motivation Section successfully created.',
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
     * @return \Illuminate\Http\Response
     */
    public function update(MotivationSectionReqeust $request)
    {
        try {
            $result = MotivationSectionRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Motivation Section successfully updated.',
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
            $result = MotivationSectionRepository::loadList($request);
            $view = View::make('motivation-section.motivation-section._list', ['data' => $result])->render();
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
            $result = MotivationSectionRepository::changeStatus($request);

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
     * @return \Illuminate\Http\Response
     */
    public function userMotivationSectionIndex()
    {
        try {
            $categories = CategoryRepository::findAll([['status', 'active'], ['type', 'motivation-section']]);

            return view('user-motivation-section.index', compact('categories'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadListForUser(Request $request)
    {
        try {
            $result = MotivationSectionRepository::loadListForUser($request);
            $view = View::make('user-motivation-section._list', ['data' => $result])->render();
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
     * Show the Getting Started index.
     *
     * @return \Illuminate\Http\Response
     */
    public function userGettingStartedDetail(Request $request)
    {
        try {
            $video = MotivationSectionRepository::getDetail($request);
            if (! empty($video)) {
                return view('user-motivation-section.detail', compact('video'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            //abort(404);
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
