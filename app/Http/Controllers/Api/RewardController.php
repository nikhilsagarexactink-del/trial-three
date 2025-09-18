<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserProductRequest;
use App\Repositories\RewardRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class RewardController extends ApiController
{
    /**
     * User reward list api
     *
     * @return Response
     */
    public function loadRewardsUserList(Request $request)
    {
        try {
            $results = RewardRepository::loadRewardsUserList($request);

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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadUserRewardList(Request $request)
    {
        try {
            $userData = getUser();
            $userTotalPoint = $userData->total_reward_points ?? 0;
            $results = RewardRepository::loadUserRewardList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $results, 'reward' => $userTotalPoint],
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
    public function loadUserHowToEarnRewardList(Request $request)
    {
        try {
            $results = RewardRepository::loadRewardManagementList($request);

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
     * Add reward point Management
     *
     * @return \Illuminate\Http\Response
     */
    public function logRewardPoint(Request $request)
    {
        try {
            $results = RewardRepository::logRewardPoint($request);
            $featureKey = $request->feature_key;
            $earnedPoints = $results['earned_points'] ?? 0;
            $isGamification = $results['data']->is_gamification ?? 0;
            $message = "";
            if ($featureKey == 'use-recipe' && $earnedPoints && $isGamification == 0) {
                $message = "Congratulations!! You have earned {$earnedPoints} points for using a recipe.";
            } elseif ($earnedPoints && $isGamification == 1) {
                $modules = [
                    'use-recipe' => 'using a recipe',
                    'complete-workout'=> 'completing a workout or exercise',
                    'rate-recipe' => 'rating a recipe',
                    'rate-video' => 'rating a training video',
                    'watch-training-video' => 'watching a training video',
                    'log-water-intake' => 'logging your water intake',
                    'log-health-markers' => 'logging your health markers',
                    'log-health-measurement' => 'logging your health measurement',
                    'achieve-workout-goal' => 'achieving your workout goal',
                    'watch-exercise-video' => 'watching an exercise video',
                    'participate-group-training-session' => 'participating in a group training session',
                    'create-workout-goal' => 'creating your workout goal',
                    'watch-workout-video' => 'watching a workout video',
                ];

                $moduleText = $modules[$featureKey] ?? null;

                if ($moduleText) {
                    $message = "Congratulations!! You have earned {$earnedPoints} points for {$moduleText}.";
                }
            }else{
                $message = "You didn't receive any rewards because you haven't played or paticipated in game";
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadUseYourRewardList(Request $request)
    {
        try {
            $results = RewardRepository::loadUseYourRewardList($request);

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
     * Product order add
     *
     * @return \Illuminate\Http\Response
     */
    public function useYourRewardProductOrder(UserProductRequest $request)
    {
        try {
            $result = RewardRepository::useYourRewardProductOrder($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Product successfully  ordered.',
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
     * Add to cart
     *
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        try {
            $result = RewardRepository::addToCart($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Product successfully added to cart.',
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
    public function loadCartList(Request $request)
    {
        try {
            $results = RewardRepository::loadCartList($request);
            $pagination = getPaginationLink($results);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $results, 'pagination' => $pagination],
                    'message' => 'User cart list fetch successfully.',
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
     * Remove to cart
     *
     * @return \Illuminate\Http\Response
     */
    public function removeCart(Request $request)
    {
        try {
            $result = RewardRepository::removeCart($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Cart successfully deleted.',
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
