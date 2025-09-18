<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class TrainingVideoCategoryController extends Controller
{
    /**
     * Show the category index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('training.training-category.index');
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            return view('training.training-category.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit category form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = CategoryRepository::findOne(['id' => $request->id]);

            if (! empty($result)) {
                return view('training.training-category.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add category
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        try {
            $result = CategoryRepository::saveCategory($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Category successfully created.',
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
     * Update category
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $result = CategoryRepository::updateCategory($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Category successfully updated.',
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
            $result = CategoryRepository::loadListCategory($request);
            $view = View::make('training.training-category._list', ['data' => $result])->render();
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
            $result = CategoryRepository::changeStatusCategory($request);

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
