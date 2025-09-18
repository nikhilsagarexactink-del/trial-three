<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Repositories\QuoteRepository;
use App\Repositories\RecipeRepository;
use App\Repositories\TrainingVideoRepository;
use Config;
use Illuminate\Http\Request;
use View;

class CommentController extends Controller
{
    /**
     * Show the comment index page.
     *
     * @return Redirect to comments index page
     */
    public function index()
    {
        try {
            return view('manage-comments.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit quote form.
     *
     * @return Redirect to edit quote page
     */
    public function editForm(Request $request)
    {
        try {
            $result = QuoteRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('quote.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update Quote
     *
     * @return Json
     */
    public function update(QuoteRequest $request)
    {
        try {
            $result = QuoteRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Quote successfully updated.',
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
     * Get training video review data
     *
     * @return Json,Html
     */
    public function loadTrainingVideoReviewList(Request $request)
    {
        try {
            $result = TrainingVideoRepository::loadUserReviewList($request);
            $view = View::make('manage-comments._training_review_list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'result' => $result,
                        'html' => $view,
                        'pagination' => $pagination,
                    ],
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
     * Delete training video review
     *
     * @return Json
     */
    public function deleteTrainingVideoReview(Request $request)
    {
        try {
            $result = TrainingVideoRepository::deleteReview($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Review successfully deleted.',
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
     * Get reciepe review data
     *
     * @return Json,Html
     */
    public function loadRecipeReviewList(Request $request)
    {
        try {
            $result = RecipeRepository::loadUserReviewList($request);
            $view = View::make('manage-comments._recipe_review_list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'result' => $result,
                        'html' => $view,
                        'pagination' => $pagination,
                    ],
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
     * Delete recipe
     *
     * @return Json
     */
    public function deleteRecipeReview(Request $request)
    {
        try {
            $result = RecipeRepository::deleteReview($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Review successfully deleted.',
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
     * Update review
     *
     * @return Json
     */
    public function updateReview(Request $request)
    {
        try {
            if ($request->type == 'training-video') {
                $result = TrainingVideoRepository::updateReview($request);

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Review successfully deleted.',
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            } elseif ($request->type == 'recipe') {
                $result = RecipeRepository::updateReview($request);

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Review successfully deleted.',
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => 'Please try again.',
                    ],
                    Config::get('constants.HttpStatus.BAD_REQUEST')
                );
            }
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
