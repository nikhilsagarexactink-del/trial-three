<?php

namespace App\Http\Controllers;

use App\Repositories\BillingRepository;
use App\Repositories\PlanRepository;
use App\Repositories\RewardRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use View;

class BillingController extends Controller
{
    /**
     * Show the billing index page.
     *
     * @return Redirect to billing index page
     */
    public function index()
    {
        return view('billing.index');
    }

    /**
     * Get billing data
     *
     * @return Json,Html
     */
    public function loadBillingList(Request $request)
    {
        try {
            $result = BillingRepository::loadBillingList($request);
            $view = View::make('billing._list', ['data' => $result])->render();
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
     * Show the billing detail page.
     *
     * @return Redirect to billing detail page
     */
    public function detail(Request $request)
    {
        try {
            $userData = getUser();
            $athlete = UserRepository::findOne([['stripe_customer_id',$request->customerId]]);
            $plans = PlanRepository::findAll([['status','active'], ['is_free_plan', 0], ['visibility', 'active']]);
            $userFutureSubscription = UserRepository::findOneSubscription([['user_id', $athlete['id']], ['stripe_status', 'scheduled']]);
            $rewardDetail = RewardRepository::findRewardManagement([['feature_key', '=', 'upgrade-your-subscription'],['status', '=','active']], ['reward_game.game']);
            $subscription = BillingRepository::detail($request);
            if (! empty($subscription)) {
                return view('billing.view', compact('subscription','plans', 'userFutureSubscription','rewardDetail','athlete'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }

    /**
     * User billing alert cron
     *
     * @return Json
     */
    public function userBillingAlertCron(Request $request)
    {
        try {
            $result = BillingRepository::userBillingAlertCron($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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
