<?php

namespace App\Http\Controllers;

use App\Http\Requests\SportRequest;
use App\Repositories\SportRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class SportController extends Controller
{
    /**
     * Show theage range index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('training.sport.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add sport form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            return view('training.sport.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit sport form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = SportRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('training.sport.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add sport
     *
     * @return \Illuminate\Http\Response
     */
    public function save(SportRequest $request)
    {
        try {
            $result = SportRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Sport successfully created.',
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
     * Update sport
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SportRequest $request)
    {
        try {
            $result = SportRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Sport successfully updated.',
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
    public function loadSportList(Request $request)
    {
        try {
            $result = SportRepository::loadList($request);
            $view = View::make('training.sport._list', ['data' => $result])->render();
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
            $result = SportRepository::changeStatus($request);

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
