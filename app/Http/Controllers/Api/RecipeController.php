<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\RecipeRepository;
use App\Repositories\RewardRepository;
use Config;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRecipeReviewRequest;
use Response;

class RecipeController extends ApiController
{
    /**
     * Recipet list api
     *
     * @return Response
     */
    public function loadRecipeList(Request $request)
    {
        try {
            $results = RecipeRepository::loadListRecipe($request);
            $categories = RecipeRepository::getRecipeCategories();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $results, 'categories' => $categories],
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
     * Recipet list api
     *
     * @return Response
     */
    public function loadListForUser(Request $request)
    {
        try {
            $results = RecipeRepository::loadListForUser($request);
            $categories = RecipeRepository::getRecipeCategories();
            $recipeReward = RewardRepository::findRewardManagement([['feature_key', '=', 'use-recipe'],['status', 'active']],['reward_game.game']);
            $rateRecipeReward = RewardRepository::findRewardManagement([['feature_key', '=', 'rate-recipe'],['status', 'active']],['reward_game.game']);
          
            
            $useRecipeGameKey = null;
            if($recipeReward->is_gamification == 1 && !empty($recipeReward->reward_game)){
                $useRecipeGame = getDynamicGames($recipeReward);
                $useRecipeGameKey = $useRecipeGame['game_key']??null;
            }

            if($rateRecipeReward->is_gamification == 1 && !empty($rateRecipeReward->reward_game)){
                $rateRecipeGame = getDynamicGames($rateRecipeReward);
                $rateRecipeGameKey = $rateRecipeGame['game_key']??null;
            }

            $useRecipeReward = [
                'reward_detail' => $recipeReward,
                'game_key' => $useRecipeGameKey,
            ];
            $ratedRecipeReward = [
                'reward_detail' => $rateRecipeReward,
                'game-key' => $rateRecipeGameKey,
            ];

            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $results, 'categories' => $categories,'use_recipe_reward'=>$useRecipeReward,'rate_recipe_reward'=> $ratedRecipeReward],
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
     * Get recipet categories list api
     *
     * @return Response
     */
    public function getRecipesCategories(Request $request)
    {
        try {
            $results = RecipeRepository::getRecipeCategories();

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
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
     * Save rating
     *
     * @return \Illuminate\Http\Response
     */
    public function saveRating(UserRecipeReviewRequest $request)
    {
        try {
            $result = RecipeRepository::saveReview($request);
            $message = 'Rating successfully submitted.';
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
     * Get the recipe detail page
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
            $recipeReward = RewardRepository::findRewardManagement([['feature_key', '=', 'use-recipe']]);
            $raterecipeReward = RewardRepository::findRewardManagement([['feature_key', '=', 'rate-recipe']]);
            if (! empty($recipeReward)) {
                $reward = RewardRepository::findOne([['user_id', '=', $userData->id], ['module_id', '=', $request->id], ['reward_management_id', '=', $recipeReward->id]]);
            }
            if (! empty($raterecipeReward)) {
                $ratingReward = RewardRepository::findOne([['user_id', '=', $userData->id], ['module_id', '=', $request->id], ['reward_management_id', '=', $raterecipeReward->id]]);
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => ['recipe' => $recipe, 'reward' => $reward, 'recipeReward' => $recipeReward, 'ratingReward' => $ratingReward, 'raterecipeReward' => $raterecipeReward],
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadUserReviewList(Request $request)
    {
        try {
            $results = RecipeRepository::loadUserReviewList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
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
