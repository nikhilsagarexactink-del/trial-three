<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgeRangeRequest;
use App\Repositories\AgeRangeRepository;
use Config;
use Illuminate\Http\Request;
use View;

class AgeRangeController extends Controller
{
    /**
     * Show theage range index page.
     *
     * @return Html
     */
    public function index()
    {
        try {
            return view('training.age-range.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add service form.
     *
     * @return Html
     */
    public function addForm()
    {
        try {
            return view('training.age-range.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit age range form.
     *
     * @return Html
     */
    public function editForm(Request $request)
    {
        try {
            $result = AgeRangeRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('training.age-range.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add age range
     *
     * @return Json
     */
    public function save(AgeRangeRequest $request)
    {
        try {
            $result = AgeRangeRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Age range successfully created.',
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
     * Update age range
     *
     * @return Json
     */
    public function update(AgeRangeRequest $request)
    {
        try {
            $result = AgeRangeRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Age range successfully updated.',
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
     * Get age range data .
     *
     * @return Json,Html
     */
    public function loadList(Request $request)
    {
        try {
            $result = AgeRangeRepository::loadList($request);
            $view = View::make('training.age-range._list', ['data' => $result])->render();
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
            $result = AgeRangeRepository::changeStatus($request);

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
