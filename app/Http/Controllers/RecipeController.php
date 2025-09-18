<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeRequest;
use App\Http\Requests\UserRecipeReviewRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\RecipeRepository;
use App\Repositories\RewardRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class RecipeController extends Controller
{
    /**
     * Show the recipe index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('nutrition-recipes.recipe.index');
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addFormRecipe()
    {
        try {
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type','recipe']]);

            return view('nutrition-recipes.recipe.add', compact('categories'));
        } catch (\Exception $ex) {
            //print_r($ex->getMessage());die;
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadListRecipe(Request $request)
    {
        try {
            $result = RecipeRepository::loadListRecipe($request);
            $view = View::make('nutrition-recipes.recipe._list', ['data' => $result])->render();
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
     * Save recipe
     *
     * @return \Illuminate\Http\Response
     */
    public function saveRecipe(RecipeRequest $request)
    {
        try {
            $result = RecipeRepository::saveRecipe($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Recipe successfully created.',
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
     * Edit recipe form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editRecipeForm(Request $request)
    {
        try {
            $recipe = RecipeRepository::findOne(['id' => $request->id], ['categories', 'images', 'images.media']);
            if (! empty($recipe)) {
                $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type','recipe']]);

                return view('nutrition-recipes.recipe.edit', compact('recipe', 'categories'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            return redirect(route('admin.recipe'))->withErrors([$ex->getMessage()]);
        }
    }

    /**
     * Update recipe
     *
     * @return \Illuminate\Http\Response
     */
    public function updateRecipe(RecipeRequest $request)
    {
        try {
            $result = RecipeRepository::updateRecipe($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Recipe successfully updated.',
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
            $result = RecipeRepository::changeStatus($request);

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
     * Show the training video index.
     *
     * @return \Illuminate\Http\Response
     */
    public function userRecipeIndex()
    {
        try {
            $categories = RecipeRepository::getRecipeCategories();

            return view('user-recipes.index', compact('categories'));
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
            $results = RecipeRepository::loadListForUser($request);
            // echo '<pre>';
            // print_r($results);die;
            $view = View::make('user-recipes._list', ['data' => $results])->render();
            $pagination = getPaginationLink($results);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $results, 'html' => $view, 'pagination' => $pagination],
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadUserReviewList(Request $request)
    {
        try {
            $results = RecipeRepository::loadUserReviewList($request);
            $view = View::make('user-recipes._review_list', ['data' => $results])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'results' => $results,
                        'html' => $view,
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
     * Save review
     *
     * @return \Illuminate\Http\Response
     */
    public function saveReview(UserRecipeReviewRequest $request)
    {
        try {
            $result = RecipeRepository::saveReview($request);
            $message = 'Review successfully submitted.';
            if (! empty($result['reward'])) {
                $message = 'Congratulations, you earned '.$result['reward']['earned_points'].' points for reviewing a recipe.';
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => $message,
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
     * Save favourite
     *
     * @return \Illuminate\Http\Response
     */
    public function saveFavourite(Request $request)
    {
        try {
            $result = RecipeRepository::saveFavourite($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Success.',
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
     * Show the recipe detail page
     *
     * @return \Illuminate\Http\Response
     */
    public function userRecipeDetail(Request $request)
    {
        try {
            $userData = getUser();
            $reward = [];
            $ratingReward = [];
            $recipe = RecipeRepository::getDetail($request);
            $recipeReward = RewardRepository::findRewardManagement([['feature_key', '=', 'use-recipe'],['status', 'active']],['reward_game.game']);
            $rateRecipeReward = RewardRepository::findRewardManagement([['feature_key', '=', 'rate-recipe'],['status', 'active']],['reward_game.game']);
            if (! empty($recipeReward)) {
                $reward = RewardRepository::findOne([['user_id', '=', $userData->id], ['module_id', '=', $request->id], ['reward_management_id', '=', $recipeReward->id]]);
            }
            if (! empty($rateRecipeReward)) {
                $ratingReward = RewardRepository::findOne([['user_id', '=', $userData->id], ['module_id', '=', $request->id], ['reward_management_id', '=', $rateRecipeReward->id]]);
            }
            // echo '<pre>';
            // print_r($raterecipeReward->id);
            // exit;

            return view('user-recipes.detail', compact('recipe', 'reward', 'recipeReward', 'ratingReward', 'rateRecipeReward'));
        } catch (\Exception $ex) {
            abort(404);
            print_r($ex->getMessage());
            exit;
        }
    }
}
