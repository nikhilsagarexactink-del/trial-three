<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use Config;
use Illuminate\Http\Request;
use View;

class CategoryController extends Controller
{
    /**
     * Show the category index page.
     *
     * @return Redirect to category index page
     */
    public function index()
    {
        return view('nutrition-recipes.category.index');
    }

    /**
     * Show add category form.
     *
     * @return Redirect to add category form
     */
    public function addCategoryForm()
    {
        try {
            return view('nutrition-recipes.category.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit category form.
     *
     * @return Redirect to edit category form
     */
    public function editCategoryForm(Request $request)
    {
        try {
            $result = CategoryRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('nutrition-recipes.category.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Get category data
     *
     * @return Json,Html
     */
    public function loadListCategory(Request $request)
    {
        try {
            $result = CategoryRepository::loadListCategory($request);
            $view = View::make('nutrition-recipes.category._list', ['data' => $result])->render();
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
     * Save category
     *
     * @return Json
     */
    public function saveCategory(Request $request)
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
     * Update Category
     *
     * @return Json
     */
    public function updateCategory(Request $request)
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
     * Change Category Status
     *
     * @return Json
     */
    public function changeStatusCategory(Request $request)
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

    /**
     * Show the category index page.
     *
     * @return Redirect to catagory index page
     */
    public function messageCategoryindex()
    {
        return view('message.category.index');
    }

    /**
     * Get message category list
     *
     * @return Json,Html
     */
    public function loadMessageCategoryList(Request $request)
    {
        try {
            $result = CategoryRepository::loadListCategory($request);
            $view = View::make('message.category._list', ['data' => $result])->render();
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
     * Add category form.
     *
     * @return Redirect to category add form
     */
    public function addMessageCategoryForm()
    {
        try {
            return view('message.category.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit category form.
     *
     * @return Redirect to category edit form
     */
    public function editMessageCategoryForm(Request $request)
    {
        try {
            $result = CategoryRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('message.category.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the getting started category index page
     *
     * @return Redirect to category index page
     */
    public function indexGettingStarted()
    {
        return view('getting-started.category.index');
    }

    /**
     * Show the category index.
     *
     * @return Redirect to category index page
     */
    public function indexMotivationSection()
    {
        try {
            return view('motivation-section.category.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add category form.
     *
     * @return Redirect to add category page
     */
    public function addGettingStartedCategoryForm()
    {
        try {
            return view('getting-started.category.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add motivation section category form.
     *
     * @return Redirect to category add form
     */
    public function addMotivationSectionCategoryForm()
    {
        try {
            return view('motivation-section.category.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit getting started category form.
     *
     * @return Redirect to category edit form
     */
    public function editGettingStartedCategoryForm(Request $request)
    {
        try {
            $result = CategoryRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('getting-started.category.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit category form.
     *
     * @return Redirect to category edit form
     */
    public function editMotivationSectionForm(Request $request)
    {
        try {
            $result = CategoryRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('motivation-section.category.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Get getting started category data
     *
     * @return Json,Html
     */
    public function loadGettingStartedCategoryList(Request $request)
    {
        try {
            $result = CategoryRepository::loadListCategory($request);
            $view = View::make('getting-started.category._list', ['data' => $result])->render();
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
     * Get motivation section data
     *
     * @return Json,Html
     */
    public function loadMotivationSectionList(Request $request)
    {
        try {
            $result = CategoryRepository::loadListCategory($request);
            $view = View::make('motivation-section.category._list', ['data' => $result])->render();
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
     * Get workout category list
     *
     * @return Json,Html
     */
    public function loadWorkOutCategoryList(Request $request)
    {
        try {
            $result = CategoryRepository::loadListCategory($request);
            $view = View::make('workout-builder.category._list', ['data' => $result])->render();
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
