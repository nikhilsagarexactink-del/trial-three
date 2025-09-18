<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Repositories\PlanRepository;
use App\Repositories\RewardRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\StripeCardRequest;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class UserBillingController extends Controller
{
    /**
     * Show theage quote index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userData = getUser();
            // parent and admin cant directory access athlete billing page
            if(!empty($userData) && $userData->user_type == 'athlete'){
                $subscription = UserRepository::getUserSubscription();
                $plans = PlanRepository::findAll([['status','active'], ['is_free_plan', 0], ['visibility', 'active']]);
                $userFutureSubscription = UserRepository::findOneSubscription([['user_id', $userData->id], ['stripe_status', 'scheduled']]);
                $rewardDetail = RewardRepository::findRewardManagement([['feature_key', '=', 'upgrade-your-subscription'],['status','=','active']],['reward_game.game']);
                
                $settings = SettingRepository::getSettings();
                return view('user-billing.index', compact('subscription', 'plans', 'userFutureSubscription', 'settings','rewardDetail'));
            }else{
                return back()->with('error', 'You do not have access to this page.');
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function indexCards()
    {
        try {
            $settings = SettingRepository::getSettings();
            return view('user-billing.cards-managments.index', compact('settings'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    // public function addCardForm()
    // {
    //     try {
    //         $settings = SettingRepository::getSettings();

    //         return view('user-billing.cards-managments.add', compact('settings'));
    //     } catch (\Exception $ex) {
    //         abort(404);
    //     }
    // }

    public function saveUserCard(StripeCardRequest $request)
    {
        try {
            $result = UserRepository::saveUserCard($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Card added successfully.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
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
    public function loadCardList(Request $request)
    {
        try {
            $results = UserRepository::loadCardList($request);
            $view = View::make('user-billing.cards-managments._list', ['cards' => $results])->render();
            // $pagination = getPaginationLink($results['cards']['data']);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'cards' => $results, 'pagination' => []],
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
    public function setDefaultCard(Request $request)
    {
        try {
            $result = UserRepository::setDefaultCard($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Default card successfully set.',
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
     * Delete Card
     *
     * @return Response
     */
    public function deleteUserCard(Request $request)
    {
        try {
            $result = UserRepository::deleteUserCard($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Card successfully deleted.',
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

     public function getSubscriptionHistory(Request $request)
     {
         try {
            $result = UserRepository::getSubscriptionHistory($request);
            $view1 = View::make('user-billing._list', ['data' => $result])->render();
            // Render the second Blade view
            $view2 = View::make('billing._subscription-summary', ['data' => $result])->render();
            // $pagination = getPaginationLink($result);
             return response()->json(
                 [
                     'success' => true,
                     'data' => ['html' => $view1, 'html_summary' => $view2, 'pagination' => []],
                     'message' => 'User subscription history fethced.',
                 ],
                 Config::get('constants.HttpStatus.OK')
             );
         } catch(\Exception $ex) {
             return response()->json(
                 [
                     'success' => false,
                     'data' => [],
                     'message' => $ex->getMessage(),
                 ],
                 Config::get('constants.HttpStatus.BAD_REQUEST')
             );
         }
     }

    public function getDowngradeHistory(Request $request)
    {
        try {
            $result = UserRepository::getDowngradeHistory($request);
            $view = View::make('user-billing._downgrade_list', ['data' => $result])->render();
            // $pagination = getPaginationLink($result);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => []],
                    'message' => 'User downgrade history fethced.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            return view('quote.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit age quote form.
     *
     * @return \Illuminate\Http\Response
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
     * Add Quote
     *
     * @return \Illuminate\Http\Response
     */
    public function save(QuoteRequest $request)
    {
        try {
            $result = QuoteRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Quote successfully created.',
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
     * Update Quote
     *
     * @return \Illuminate\Http\Response
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadQuoteList(Request $request)
    {
        try {
            $result = QuoteRepository::loadQuoteList($request);
            $view = View::make('quote._list', ['data' => $result])->render();
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
            $result = QuoteRepository::changeStatus($request);

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
     * Delete Downgrade
     *
     * @return Response
     */
    public function deleteDowngradePlan(Request $request)
    {
        try {
            $result = UserRepository::deleteDowngradePlan($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Record successfully deleted.',
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

    public function cancelSubscription(Request $request)
    {
        try {
            // dd($request->all());
            $result = UserRepository::cancelSubscription($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Subscription canceled successfully and set newest plan.',
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

    /* update subscription downgrade */
    public function updateSubscriptionCron(Request $request)
    {
        try {
            $result = UserRepository::updateSubscriptionCron();

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Cron is running successfully.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

     /* update subscription downgrade */
     public function updateSubscriptionStatusCron(Request $request)
     {
         try {
             $result = UserRepository::updateSubscriptionStatusCron();

             return response()->json(
                 [
                     'success' => true,
                     'data' => [],
                     'message' => 'Cron is running successfully.',
                 ],
                 Config::get('constants.HttpStatus.OK')
             );
         } catch(\Exception $ex) {
             return response()->json(
                 [
                     'success' => false,
                     'data' => [],
                     'message' => $ex->getMessage(),
                 ],
                 Config::get('constants.HttpStatus.BAD_REQUEST')
             );
         }
     }

    /*
        ** Account Cancel
    */

    public function cancelAccount(Request $request)
    {
        try {
            $result = UserRepository::cancelAccount($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Your account canceled successfully.',
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

    public function adminChangePlan(Request $request)
    {
        try {
            $result = UserRepository::adminChangePlan($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Plan changed successfully.',
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

    public function subscriptionGracePeriodEndCron(Request $request)
    {
        try {
            $result = UserRepository::subscriptionGracePeriodEndCron();

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Cron is running successfully.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
