<?php

namespace App\Http\Controllers;

use App\Http\Requests\RewardManagementRequest;
use App\Http\Requests\RewardProductRequest;
use App\Http\Requests\UserProductRequest;
use App\Http\Requests\UserRewardRequest;
use App\Repositories\RewardRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use View;

class RewardController extends Controller
{
    /**
     * Show the user reward  index.
     *
     * @return Redirect to user reward index page
     */
    public function index()
    {
        try {
            return view('rewards-management.user-rewards.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Json,Html
     */
    public function loadUserRewardList(Request $request)
    {
        try {
            $result = RewardRepository::loadUserRewardList($request);
            $view = View::make('user-reward._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $result, 'html' => $view, 'pagination' => $pagination],
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
     * Update rewards points
     *
     * @return Json
     */
    public function updateUserReward(UserRewardRequest $request)
    {
        try {
            $result = RewardRepository::updateUserReward($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'User reward  successfully updated.',
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
            $result = RewardRepository::changeStatus($request);

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
     * Show the user reward  index.
     *
     * @return Redirect to user reward index page
     */
    public function rewardManagementIndex()
    {
        try {
            return view('rewards-management.rewards-points-management.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add rewards points management form.
     *
     * @return Redirect to add form
     */
    public function addRewardManagementForm()
    {
        try {
            $games = RewardRepository::findAllGame();
            return view('rewards-management.rewards-points-management.add',compact('games'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show user earnd reward index page.
     *
     * @return Redirect to how to earn index page
     */
    public function userEarnReward(Request $request)
    {
        try {
            return view('user-reward.howToEarnIndex');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show user reward index page.
     *
     * @return Redirect to user reward index page
     */
    public function userRewards(Request $request)
    {
        try {
            $userData = getUser();
            $userTotalPoint = $userData->total_reward_points ?? 0;

            return view('user-reward.index', compact('userTotalPoint'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    // public function rewardsUserList(Request $request)
    // {
    //     return view('user-reward.userIndex');
    // }

    /**
     * Display a listing of the reward user list.
     *
     * @return Json,Html
     */
    public function loadRewardsUserList(Request $request)
    {
        try {
            $results = RewardRepository::loadRewardsUserList($request);
            $view = View::make('rewards-management.user-rewards._list', ['data' => $results])->render();
            $pagination = getPaginationLink($results);

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
     * Display a listing of the how to earn reward list.
     *
     * @return Json,Html
     */
    public function loadUserHowToEarnRewardList(Request $request)
    {
        try {
            $result = RewardRepository::loadRewardManagementList($request);
            $view = View::make('user-reward._how_to_earn_list', ['data' => $result])->render();
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
     * Add reward point Management
     *
     * @return Redirect to add reward form
     */
    public function addRewardManagement(RewardManagementRequest $request)
    {
        try {
            $result = RewardRepository::addRewardManagement($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Reward management successfully created.',
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
     * @return Json,Html
     */
    public function loadRewardManagementList(Request $request)
    {
        try {
            $result = RewardRepository::loadRewardManagementList($request);
            $view = View::make('rewards-management.rewards-points-management._list', ['data' => $result])->render();
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
     * Show edit reward user form.
     *
     * @return Redirect to edit reward user form.
     */
    public function editRewardManagementForm(Request $request)
    {
        try {
            $games = RewardRepository::findAllGame();
            $result = RewardRepository::findOneRewardManagement(['id' => $request->id],['reward_game.game']);
            if (! empty($result)) {
                return view('rewards-management.rewards-points-management.edit', compact('result','games'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update reward  management point
     *
     * @return \Illuminate\Http\Response
     */
    public function updateRewardManagement(RewardManagementRequest $request)
    {
        try {
            $result = RewardRepository::updateRewardManagement($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Reward management  successfully updated.',
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
     * Show edit reward user form.
     *
     * @return Redirect to reward point view page
     */
    public function viewRewardManagement(Request $request)
    {
        try {
            $result = RewardRepository::findOneRewardManagement(['id' => $request->id],['reward_game.game']);
            if (! empty($result)) {
                return view('rewards-management.rewards-points-management.view', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Change Status Of Reward Management
     *
     * @return Json
     */
    public function changeStatusRewardManagement(Request $request)
    {
        try {
            $result = RewardRepository::changeStatusRewardManagement($request);

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
     * Show the user reward how-to-earn-rewards index.
     *
     * @return Redirect to how to earn reward index page
     */
    public function userRewardIndex()
    {
        try {
            return view('user-rewards.how-to-earn-rewards.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add reward point Management
     *
     * @return Json
     */
    public function saveRewardPoint(Request $request)
    {
        try {
            $result = RewardRepository::saveUserReward($request);
            $featureKey = $request->feature_key;
            $earnedPoints = $result['earned_points'] ?? 0;
            $isGamification = $result['data']->is_gamification ?? 0;
            $message = "";
            if ($featureKey === 'use-recipe' && $earnedPoints && $isGamification == 0) {
                $message = "Congratulations!! You have earned {$earnedPoints} points for using a recipe.";
            } elseif ($earnedPoints && $isGamification == 1) {
                $modules = [
                    'use-recipe' => 'using a recipe',
                    'rate-recipe' => 'rating a recipe',
                    'rate-video' => 'rating a training video',
                    'watch-training-video' => 'watching a training video',
                    'complete-workout' => 'completing a workout or exercise',
                    'log-water-intake' => 'logging your water intake',
                    'log-health-markers' => 'logging your health markers',
                    'log-health-measurement' => 'logging your health measurement',
                    'upgrade-your-subscription' => 'upgrading your subscription',
                    'achieve-workout-goal' => 'achieving your workout goal',
                    'watch-exercise-video' => 'watching an exercise video',
                    'participate-group-training-session' => 'participating in a group training session',
                    'build-own-workout' => 'creating your own workout',
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
                    'data' => [],
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
     * Update reward  management order
     *
     * @return Json
     */
    public function updateRewardManagementOrder(Request $request)
    {
        try {
            $result = RewardRepository::updateRewardManagementOrder($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Reward management order successfully updated.',
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
     * Show the user reward  how-to-earn-rewards  index.
     *
     * @return Redirect to user reward view page
     */
    public function viewUserRewardPoints(Request $request)
    {
        try {
            $userData = getUser();
            $userTotalEarning = RewardRepository::userTotalReward([['user_id', $request->userId]]);
            $userTotalRedeemed = RewardRepository::userTotalRedeemed([['user_id', $request->userId]]);
            $userDetail = UserRepository::findOne([['id', $request->userId]]);

            return view('rewards-management.user-rewards.view', compact('userTotalEarning', 'userTotalRedeemed', 'userDetail'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of user reward for admin.
     *
     * @return Json,Html
     */
    public function loadUserRewardListForAdmin(Request $request)
    {
        try {
            $result = RewardRepository::loadUserRewardList($request);
            $view = View::make('rewards-management.user-rewards._point_history', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $result, 'html' => $view, 'pagination' => $pagination],
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
     * Show the  reward store management index page.
     *
     * @return Redirect to reward index page
     */
    public function rewardStoreIndex()
    {
        try {
            return view('rewards-management.rewards-store-management.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add rewards store management product form.
     *
     * @return Redirect to rewards store management product add form
     */
    public function addRewardProductForm()
    {
        try {
            return view('rewards-management.rewards-store-management.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add reward store  Management product
     *
     * @return Json
     */
    public function addRewardProduct(RewardProductRequest $request)
    {
        try {
            $result = RewardRepository::addRewardProduct($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Reward  product successfully created.',
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
     * Display a list of the reward product.
     *
     * @return Json,Html
     */
    public function loadRewardProductList(Request $request)
    {
        try {
            $result = RewardRepository::loadRewardProductList($request);
            $view = View::make('rewards-management.rewards-store-management._list', ['data' => $result])->render();
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
     * Edit reward product form.
     *
     * @return Redirect to reward product edit form
     */
    public function editRewardProductForm(Request $request)
    {
        try {
            $product = RewardRepository::findRewardProduct(['id' => $request->id], ['images', 'images.media']);
            if (! empty($product)) {
                return view('rewards-management.rewards-store-management.edit', compact('product'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update reward product
     *
     * @return Json
     */
    public function updateRewardProduct(RewardProductRequest $request)
    {
        try {
            $result = RewardRepository::updateRewardProduct($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Reward product successfully updated.',
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
     * Change Status Of Reward Product
     *
     * @return Json
     */
    public function changeStatusRewardProduct(Request $request)
    {
        try {
            $result = RewardRepository::changeStatusRewardProduct($request);

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
     * Change Availability Status Of Reward Product
     *
     * @return Json
     */
    public function changeAvailabilityStatusRewardProduct(Request $request)
    {
        try {
            $result = RewardRepository::changeAvailabilityStatusRewardProduct($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Availability status successfully updated.',
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
     * Show the  use your reward  index page.
     *
     * @return Redirect to user product list page
     */
    public function useYourRewardIndex()
    {
        try {
            $userData = getUser();
            $totalRewardPoints = $userData->total_reward_points ?? 0;
            $totalCarts = RewardRepository::userCarts();
            $userTotalEarning = RewardRepository::userTotalReward([['user_id', $userData->id]]);
            $userDetail = UserRepository::findOne([['id', $userData->id]]);
            $userTotalRedeemed = RewardRepository::userTotalRedeemed([['user_id', $userData->id]]);

            return view('user-reward.use-your-reward.index', compact('userTotalEarning', 'userTotalRedeemed', 'totalCarts'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of reward product list
     *
     * @return Json,Html
     */
    public function loadUseYourRewardList(Request $request)
    {
        try {
            $results = RewardRepository::loadUseYourRewardList($request);
            $view = View::make('user-reward.use-your-reward._list', ['data' => $results])->render();
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
     * Show the  cart index page.
     *
     * @return Redirect to cart page
     */
    public function cartIndex()
    {
        try {
            $totalReward = RewardRepository::rewardPoints();

            return view('user-reward.cart.index', compact('totalReward'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add to cart
     *
     * @return Json
     */
    public function addToCart(Request $request)
    {
        try {
            $result = RewardRepository::addToCart($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Product successfully added in cart.',
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
     * Display a listing of the cart.
     *
     * @return Json,Html
     */
    public function loadCartList(Request $request)
    {
        try {
            $results = RewardRepository::loadCartList($request);
            $view = View::make('user-reward.cart._list', ['data' => $results])->render();
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
     * Remove product from cart
     *
     * @return Json
     */
    public function removeCart(Request $request)
    {
        try {
            $result = RewardRepository::removeCart($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Product successfully deleted from your cart.',
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
      * product order index
      *
      * @return Html
      */
     public function UseYourRewardProductOrderIndex(Request $request)
     {
         try {
             $profileDetail = UserRepository::getProfileDetail($request);

             return view('user-reward.product-order.index', compact('profileDetail'));
         } catch (\Exception $ex) {
             //print_r($ex->getMessage());die;
             abort(404);
         }
     }

    /**
     * Remove product from cart
     *
     * @return Json
     */
    public function validateUserRewardPoint(Request $request)
    {
        try {
            $result = RewardRepository::validateUserRewardPoint($request);

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
    * Product order add
    *
    * @return Json
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
     * Reward redemption index
     *
     * @return Html
     */
    public function rewardRedemptionIndex()
    {
        try {
            return view('rewards-management.rewards-redemption.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a list of the reward product.
     *
     * @return Json,Html
     */
    public function loadRewardRedemptionList(Request $request)
    {
        try {
            $result = RewardRepository::loadRewardRedemptionList($request);
            $view = View::make('rewards-management.rewards-redemption._list', ['data' => $result])->render();
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
     * Change  Status Of Reward Redemption
     *
     * @return Json
     */
    public function changeStatusRewardRedemption(Request $request)
    {
        try {
            $result = RewardRepository::changeStatusRewardRedemption($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Product status successfully updated.',
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
